<?php

declare(strict_types=1);

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InstallerController extends Controller
{
    /**
     * Installation steps
     */
    private const STEPS = [
        'welcome' => 'Welcome & Requirements Check',
        'database' => 'Database Configuration',
        'install' => 'Run Installation',
        'admin' => 'Create Admin Account',
        'settings' => 'System Settings',
        'complete' => 'Installation Complete',
    ];

    /**
     * Get installation status
     */
    public function status(): JsonResponse
    {
        $isInstalled = config('app.installer_completed', false) ||
                       File::exists(storage_path('app/installed.txt'));

        return response()->json([
            'success' => true,
            'data' => [
                'installed' => $isInstalled,
                'current_step' => $this->getCurrentStep(),
                'steps' => self::STEPS,
            ],
        ]);
    }

    /**
     * Check system requirements
     */
    public function checkRequirements(): JsonResponse
    {
        $requirements = [
            'php_version' => [
                'name' => 'PHP Version',
                'required' => '8.2',
                'current' => PHP_VERSION,
                'passed' => version_compare(PHP_VERSION, '8.2', '>='),
            ],
            'extensions' => $this->checkExtensions(),
            'directories' => $this->checkDirectories(),
            'functions' => $this->checkFunctions(),
        ];

        $allPassed = $requirements['php_version']['passed'] &&
                     collect($requirements['extensions'])->every('passed') &&
                     collect($requirements['directories'])->every('passed') &&
                     collect($requirements['functions'])->every('passed');

        return response()->json([
            'success' => true,
            'data' => [
                'requirements' => $requirements,
                'all_passed' => $allPassed,
            ],
        ]);
    }

    /**
     * Test database connection
     */
    public function testDatabase(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'connection' => 'required|string|in:mysql,pgsql,sqlite,sqlsrv',
            'host' => 'required_if:connection:mysql,pgsql,sqlsrv|string|nullable',
            'port' => 'required_if:connection:mysql,pgsql,sqlsrv|integer|nullable',
            'database' => 'required|string',
            'username' => 'required_if:connection:mysql,pgsql,sqlsrv|string|nullable',
            'password' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            if ($request->connection === 'sqlite') {
                $databasePath = database_path($request->database);
                if (!File::exists($databasePath)) {
                    File::put($databasePath, '');
                }
                $connected = true;
            } else {
                config([
                    'database.connections.test' => [
                        'driver' => $request->connection,
                        'host' => $request->host,
                        'port' => $request->port,
                        'database' => $request->database,
                        'username' => $request->username,
                        'password' => $request->password,
                        'prefix' => '',
                    ],
                ]);

                DB::purge('test');
                $connected = DB::connection('test')->getPdo() !== null;
            }

            return response()->json([
                'success' => true,
                'message' => 'Database connection successful',
                'data' => [
                    'connected' => $connected,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Run installation
     */
    public function install(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'connection' => 'required|string|in:mysql,pgsql,sqlite,sqlsrv',
            'host' => 'required_if:connection:mysql,pgsql,sqlsrv|string|nullable',
            'port' => 'required_if:connection:mysql,pgsql,sqlsrv|integer|nullable',
            'database' => 'required|string',
            'username' => 'required_if:connection:mysql,pgsql,sqlsrv|string|nullable',
            'password' => 'nullable|string',
            'prefix' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Update .env file
            $this->updateEnvFile([
                'DB_CONNECTION' => $request->connection,
                'DB_HOST' => $request->host ?? '127.0.0.1',
                'DB_PORT' => $request->port ?? '3306',
                'DB_DATABASE' => $request->database,
                'DB_USERNAME' => $request->username ?? 'root',
                'DB_PASSWORD' => $request->password ?? '',
                'DB_PREFIX' => $request->prefix ?? '',
            ]);

            // Generate application key if not set
            if (empty(config('app.key'))) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            // Generate JWT secret if not set
            if (empty(config('jwt.secret'))) {
                Artisan::call('jwt:secret', ['--force' => true]);
            }

            // Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // Seed database
            Artisan::call('db:seed', ['--force' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Installation completed successfully',
                'data' => [
                    'step' => 'admin',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Installation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create admin user
     */
    public function createAdmin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'hospital_name' => 'required|string|max:255',
            'hospital_address' => 'nullable|string|max:500',
            'hospital_phone' => 'nullable|string|max:50',
            'hospital_email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create institution
            $institution = DB::table('institutions')->insertGetId([
                'name' => $request->hospital_name,
                'slug' => str($request->hospital_name)->slug(),
                'address' => $request->hospital_address,
                'phone' => $request->hospital_phone,
                'email' => $request->hospital_email,
                'settings' => json_encode([
                    'timezone' => config('app.timezone'),
                    'date_format' => 'Y-m-d',
                    'time_format' => 'H:i',
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get admin role
            $adminRole = DB::table('roles')->where('slug', 'admin')->first();

            if (!$adminRole) {
                $adminRoleId = DB::table('roles')->insertGetId([
                    'name' => 'Administrator',
                    'slug' => 'admin',
                    'description' => 'Full system access',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $adminRoleId = $adminRole->id;
            }

            // Create admin user
            DB::table('users')->insert([
                'institution_id' => $institution,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $adminRoleId,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign role to user
            $userId = DB::table('users')->where('email', $request->email)->first()->id;
            DB::table('role_user')->insert([
                'role_id' => $adminRoleId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Admin user created successfully',
                'data' => [
                    'step' => 'complete',
                    'user' => [
                        'id' => $userId,
                        'name' => $request->name,
                        'email' => $request->email,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete installation
     */
    public function complete(): JsonResponse
    {
        try {
            // Mark installation as complete
            $this->updateEnvFile([
                'INSTALLER_COMPLETED' => 'true',
            ]);

            // Create installed marker file
            File::put(storage_path('app/installed.txt'), now()->toIso8601String());

            // Clear and cache configuration
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            return response()->json([
                'success' => true,
                'message' => 'Installation completed successfully',
                'data' => [
                    'completed' => true,
                    'login_url' => url('/login'),
                    'admin_email' => $this->getAdminEmail(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete installation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current installation step
     */
    private function getCurrentStep(): string
    {
        if (config('app.installer_completed', false)) {
            return 'complete';
        }

        if (File::exists(storage_path('app/installed.txt'))) {
            return 'complete';
        }

        return 'welcome';
    }

    /**
     * Check PHP extensions
     */
    private function checkExtensions(): array
    {
        $required = [
            'bcmath',
            'ctype',
            'curl',
            'dom',
            'fileinfo',
            'filter',
            'gd',
            'json',
            'mbstring',
            'openssl',
            'pdo',
            'xml',
            'zip',
        ];

        return collect($required)->map(function ($ext) {
            return [
                'name' => $ext,
                'passed' => extension_loaded($ext),
            ];
        })->toArray();
    }

    /**
     * Check directory permissions
     */
    private function checkDirectories(): array
    {
        $directories = [
            storage_path('app'),
            storage_path('logs'),
            storage_path('framework/cache'),
            base_path('bootstrap/cache'),
        ];

        return collect($directories)->map(function ($dir) {
            $writable = is_writable($dir);
            return [
                'name' => str_replace(base_path(), '', $dir),
                'path' => $dir,
                'passed' => $writable,
            ];
        })->toArray();
    }

    /**
     * Check PHP functions
     */
    private function checkFunctions(): array
    {
        $functions = [
            'exec',
            'shell_exec',
            'proc_open',
            'proc_get_status',
            'putenv',
        ];

        return collect($functions)->map(function ($func) {
            return [
                'name' => $func,
                'passed' => function_exists($func),
            ];
        })->toArray();
    }

    /**
     * Update .env file
     */
    private function updateEnvFile(array $data): void
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            File::copy(base_path('.env.example'), $envPath);
        }

        $envContent = File::get($envPath);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$key}={$value}";
            }
        }

        File::put($envPath, $envContent);
    }

    /**
     * Get admin email from database
     */
    private function getAdminEmail(): ?string
    {
        try {
            $admin = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.slug', 'admin')
                ->first();

            return $admin?->email;
        } catch (\Exception $e) {
            return null;
        }
    }
}
