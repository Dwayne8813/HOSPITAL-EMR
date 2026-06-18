<?php
/**
 * Hospital EMR/EHR System - Installer Helper Functions
 */

session_start();

define('INSTALLER_VERSION', '1.0.0');
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
define('CACHE_PATH', BASE_PATH . '/bootstrap/cache');
define('ENV_FILE', BASE_PATH . '/.env');

function isInstalled(): bool {
    if (file_exists(ENV_FILE)) {
        $env = file_get_contents(ENV_FILE);
        if (preg_match('/^APP_INSTALLED=true$/m', $env)) {
            return true;
        }
    }
    return false;
}

function redirectIfInstalled(): void {
    if (isInstalled()) {
        header('Location: /');
        exit;
    }
}

function getStep(): int {
    return isset($_GET['step']) ? (int)$_GET['step'] : 1;
}

function setStep(int $step): void {
    $_SESSION['current_step'] = $step;
}

function getRequirements(): array {
    $requirements = [];

    $phpVersion = PHP_VERSION;
    $requirements[] = [
        'name' => "PHP Version >= 8.2",
        'current' => $phpVersion,
        'passed' => version_compare($phpVersion, '8.2.0', '>=')
    ];

    $extensions = ['pdo_mysql', 'mbstring', 'openssl', 'json', 'fileinfo', 'gd', 'xml', 'curl'];
    foreach ($extensions as $ext) {
        $requirements[] = [
            'name' => "PHP Extension: {$ext}",
            'current' => extension_loaded($ext) ? 'Loaded' : 'Not loaded',
            'passed' => extension_loaded($ext)
        ];
    }

    $functions = ['exec', 'shell_exec'];
    foreach ($functions as $func) {
        $requirements[] = [
            'name' => "PHP Function: {$func}()",
            'current' => function_exists($func) ? 'Available' : 'Disabled',
            'passed' => function_exists($func),
            'optional' => true
        ];
    }

    $dirs = [
        'storage' => STORAGE_PATH,
        'bootstrap/cache' => CACHE_PATH
    ];
    foreach ($dirs as $name => $path) {
        $exists = is_dir($path);
        $writable = $exists && is_writable($path);
        $requirements[] = [
            'name' => "Directory writable: {$name}/",
            'current' => !$exists ? 'Not found' : ($writable ? 'Writable' : 'Not writable'),
            'passed' => $writable
        ];
    }

    $requirements[] = [
        'name' => 'mod_rewrite enabled',
        'current' => function_exists('apache_get_modules') ? (in_array('mod_rewrite', apache_get_modules()) ? 'Enabled' : 'Disabled') : 'Cannot detect (shared hosting assumed OK)',
        'passed' => !function_exists('apache_get_modules') || in_array('mod_rewrite', apache_get_modules())
    ];

    return $requirements;
}

function testDatabaseConnection(string $host, int $port, string $name, string $user, string $pass): array {
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_TIMEOUT => 5
        ];
        $pdo = new PDO($dsn, $user, $pass, $options);

        $stmt = $pdo->query("SELECT VERSION() as version");
        $row = $stmt->fetch();
        $mysqlVersion = $row['version'] ?? 'unknown';

        $major = (int) explode('.', $mysqlVersion)[0];

        return [
            'success' => true,
            'message' => "Connected successfully. MySQL version: {$mysqlVersion}",
            'mysql_version' => $mysqlVersion,
            'version_ok' => $major >= 8
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

function createEnvFile(array $config): bool {
    $appKey = 'base64:' . base64_encode(random_bytes(32));

    $env = <<<ENV
APP_NAME="{$config['institution_name']}"
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL=http://{$config['host']}

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST={$config['db_host']}
DB_PORT={$config['db_port']}
DB_DATABASE={$config['db_name']}
DB_USERNAME={$config['db_user']}
DB_PASSWORD={$config['db_password']}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="admin@{$config['institution_email']}"
MAIL_FROM_NAME="{$config['institution_name']}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="{$config['institution_name']}"

INSTALLER_COMPLETED=true
APP_INSTALLED=true
ENV;

    $result = file_put_contents(ENV_FILE, $env);
    return $result !== false;
}

function runMigrations(): array {
    $output = [];
    $exitCode = 0;

    $cmd = 'php ' . BASE_PATH . '/artisan migrate --force 2>&1';
    exec($cmd, $output, $exitCode);

    return [
        'success' => $exitCode === 0,
        'output' => implode("\n", $output),
        'exit_code' => $exitCode
    ];
}

function runSeeders(): array {
    $output = [];
    $exitCode = 0;

    $cmd = 'php ' . BASE_PATH . '/artisan db:seed --force 2>&1';
    exec($cmd, $output, $exitCode);

    return [
        'success' => $exitCode === 0,
        'output' => implode("\n", $output),
        'exit_code' => $exitCode
    ];
}

function generateKey(): array {
    $output = [];
    $exitCode = 0;

    $cmd = 'php ' . BASE_PATH . '/artisan key:generate --force 2>&1';
    exec($cmd, $output, $exitCode);

    return [
        'success' => $exitCode === 0,
        'output' => implode("\n", $output),
        'exit_code' => $exitCode
    ];
}

function setPermissions(): array {
    $dirs = [
        BASE_PATH . '/storage',
        BASE_PATH . '/bootstrap/cache'
    ];

    $results = [];
    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            $results[] = [
                'dir' => $dir,
                'success' => @chmod($dir, 0755) !== false
            ];
        }
    }

    return $results;
}

function getServerInfo(): array {
    return [
        'php_version' => PHP_VERSION,
        'mysql_version' => function_exists('mysqli_get_client_info') ? mysqli_get_client_info() : 'N/A',
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'os' => php_uname('s') . ' ' . php_uname('r'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
    ];
}

function getConfig(): array {
    return [
        'host' => $_SESSION['db_host'] ?? 'localhost',
        'port' => $_SESSION['db_port'] ?? '3306',
        'name' => $_SESSION['db_name'] ?? '',
        'user' => $_SESSION['db_user'] ?? '',
        'password' => $_SESSION['db_password'] ?? '',
        'institution_name' => $_SESSION['institution_name'] ?? '',
        'institution_nit' => $_SESSION['institution_nit'] ?? '',
        'institution_address' => $_SESSION['institution_address'] ?? '',
        'institution_phone' => $_SESSION['institution_phone'] ?? '',
        'institution_email' => $_SESSION['institution_email'] ?? '',
        'admin_name' => $_SESSION['admin_name'] ?? '',
        'admin_email' => $_SESSION['admin_email'] ?? '',
        'admin_password' => $_SESSION['admin_password'] ?? '',
    ];
}
