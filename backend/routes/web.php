<?php

use App\Http\Controllers\Installer\InstallerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Minimal web routes for the hospital EMR/EHR system.
| Main functionality is exposed via API routes.
|
*/

/*
|--------------------------------------------------------------------------
| Installer Routes
|--------------------------------------------------------------------------
|
| These routes handle the installation process and are only available
| when the system is not yet installed.
|
*/

Route::prefix('install')->group(function () {
    // Check if installer is disabled
    Route::middleware(['installer.check'])->group(function () {
        Route::get('/', function () {
            return view('installer.index');
        })->name('installer.index');

        // API endpoints for installer
        Route::get('/status', [InstallerController::class, 'status'])
            ->name('installer.status');

        Route::get('/requirements', [InstallerController::class, 'checkRequirements'])
            ->name('installer.requirements');

        Route::post('/test-database', [InstallerController::class, 'testDatabase'])
            ->name('installer.test-database');

        Route::post('/install', [InstallerController::class, 'install'])
            ->name('installer.install');

        Route::post('/create-admin', [InstallerController::class, 'createAdmin'])
            ->name('installer.create-admin');

        Route::post('/complete', [InstallerController::class, 'complete'])
            ->name('installer.complete');
    });
});

/*
|--------------------------------------------------------------------------
| Main Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/api/v1/docs');
});

Route::get('/api/v1/docs', function () {
    return response()->json([
        'name' => config('app.name', 'Hospital EMR/EHR System'),
        'version' => '1.0.0',
        'description' => 'Complete Hospital Electronic Medical Records / Electronic Health Records API',
        'documentation' => '/api/v1/docs',
        'endpoints' => [
            'auth' => [
                'login' => 'POST /api/v1/auth/login',
                'register' => 'POST /api/v1/auth/register',
                'forgot_password' => 'POST /api/v1/auth/forgot-password',
                'reset_password' => 'POST /api/v1/auth/reset-password',
                'logout' => 'POST /api/v1/auth/logout',
                'refresh' => 'POST /api/v1/auth/refresh',
                'me' => 'GET /api/v1/auth/me',
                'change_password' => 'POST /api/v1/auth/change-password',
                'enable_mfa' => 'POST /api/v1/auth/enable-mfa',
                'verify_mfa' => 'POST /api/v1/auth/verify-mfa',
            ],
            'patients' => [
                'index' => 'GET /api/v1/patients',
                'store' => 'POST /api/v1/patients',
                'show' => 'GET /api/v1/patients/{id}',
                'update' => 'PUT /api/v1/patients/{id}',
                'destroy' => 'DELETE /api/v1/patients/{id}',
                'search' => 'GET /api/v1/patients/search',
            ],
            'medical_records' => [
                'index' => 'GET /api/v1/medical-records',
                'store' => 'POST /api/v1/medical-records',
                'show' => 'GET /api/v1/medical-records/{id}',
                'update' => 'PUT /api/v1/medical-records/{id}',
                'destroy' => 'DELETE /api/v1/medical-records/{id}',
            ],
            'appointments' => [
                'index' => 'GET /api/v1/appointments',
                'store' => 'POST /api/v1/appointments',
                'show' => 'GET /api/v1/appointments/{id}',
                'update' => 'PUT /api/v1/appointments/{id}',
                'destroy' => 'DELETE /api/v1/appointments/{id}',
            ],
            'prescriptions' => [
                'index' => 'GET /api/v1/prescriptions',
                'store' => 'POST /api/v1/prescriptions',
                'show' => 'GET /api/v1/prescriptions/{id}',
                'pdf' => 'GET /api/v1/prescriptions/{id}/pdf',
            ],
            'lab_orders' => [
                'index' => 'GET /api/v1/lab-orders',
                'store' => 'POST /api/v1/lab-orders',
                'show' => 'GET /api/v1/lab-orders/{id}',
            ],
            'billing' => [
                'invoices' => 'GET /api/v1/invoices',
                'payments' => 'GET /api/v1/payments',
            ],
            'reports' => [
                'dashboard' => 'GET /api/v1/reports/dashboard',
                'patients' => 'GET /api/v1/reports/patients',
                'revenue' => 'GET /api/v1/reports/revenue',
            ],
        ],
    ]);
});
