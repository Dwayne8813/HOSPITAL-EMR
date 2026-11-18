<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\PatientController;
use App\Http\Controllers\V1\MedicalRecordController;
use App\Http\Controllers\V1\ConsultationController;
use App\Http\Controllers\V1\SoapNoteController;
use App\Http\Controllers\V1\EvolutionController;
use App\Http\Controllers\V1\VitalSignController;
use App\Http\Controllers\V1\DiagnosisController;
use App\Http\Controllers\V1\Cie10Controller;
use App\Http\Controllers\V1\AppointmentController;
use App\Http\Controllers\V1\DoctorScheduleController;
use App\Http\Controllers\V1\PrescriptionController;
use App\Http\Controllers\V1\LabOrderController;
use App\Http\Controllers\V1\LabResultController;
use App\Http\Controllers\V1\ImagingOrderController;
use App\Http\Controllers\V1\ImagingResultController;
use App\Http\Controllers\V1\MedicationController;
use App\Http\Controllers\V1\SupplierController;
use App\Http\Controllers\V1\PurchaseOrderController;
use App\Http\Controllers\V1\InventoryItemController;
use App\Http\Controllers\V1\InventoryMovementController;
use App\Http\Controllers\V1\BuildingController;
use App\Http\Controllers\V1\RoomController;
use App\Http\Controllers\V1\BedController;
use App\Http\Controllers\V1\HospitalizationController;
use App\Http\Controllers\V1\SurgeryScheduleController;
use App\Http\Controllers\V1\EmergencyTriageController;
use App\Http\Controllers\V1\InvoiceController;
use App\Http\Controllers\V1\PaymentController;
use App\Http\Controllers\V1\ServiceCatalogController;
use App\Http\Controllers\V1\CashRegisterController;
use App\Http\Controllers\V1\CertificateController;
use App\Http\Controllers\V1\TelemedicineController;
use App\Http\Controllers\V1\ReportController;
use App\Http\Controllers\V1\DashboardController;
use App\Http\Controllers\V1\SettingController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\NotificationController;
use App\Http\Controllers\V1\BackupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Hospital EMR/EHR System - Versioned API Routes
| Version: 1.0
|
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PUBLIC ROUTES (no auth required)
    |--------------------------------------------------------------------------
    */
    Route::get('/health', fn () => response()->json(['status' => 'healthy']));

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    /*
    |--------------------------------------------------------------------------
    | PROTECTED ROUTES (JWT auth required)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['jwt.auth', 'ensure.active'])->group(function () {

        // ─── Auth ───────────────────────────────────────────────
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::post('/enable-mfa', [AuthController::class, 'enableMfa']);
            Route::post('/verify-mfa', [AuthController::class, 'verifyMfa']);
        });

        // ─── Patients ──────────────────────────────────────────
        Route::get('/patients/search', [PatientController::class, 'search']);
        Route::apiResource('patients', PatientController::class);
        Route::get('/patients/{patient}/medical-history', [PatientController::class, 'medicalHistory']);
        Route::get('/patients/{patient}/documents', [PatientController::class, 'documents']);
        Route::post('/patients/{patient}/documents', [PatientController::class, 'addDocument']);
        Route::get('/patients/{patient}/allergies', [PatientController::class, 'allergies']);
        Route::post('/patients/{patient}/allergies', [PatientController::class, 'addAllergy']);
        Route::get('/patients/{patient}/insurances', [PatientController::class, 'insurances']);
        Route::post('/patients/{patient}/insurances', [PatientController::class, 'addInsurance']);
        Route::get('/patients/{patient}/contacts', [PatientController::class, 'contacts']);
        Route::post('/patients/{patient}/contacts', [PatientController::class, 'addContact']);

        // ─── Medical Records ───────────────────────────────────
        Route::apiResource('medical-records', MedicalRecordController::class);
        Route::get('/medical-records/{medicalRecord}/antecedents', [MedicalRecordController::class, 'getAntecedents']);
        Route::put('/medical-records/{medicalRecord}/antecedents', [MedicalRecordController::class, 'updateAntecedents']);

        // ─── Consultations ─────────────────────────────────────
        Route::post('/consultations', [ConsultationController::class, 'store']);
        Route::get('/consultations/{consultation}', [ConsultationController::class, 'show']);

        // ─── SOAP Notes ────────────────────────────────────────
        Route::post('/soap-notes', [SoapNoteController::class, 'store']);
        Route::get('/soap-notes/{medicalRecordId}', [SoapNoteController::class, 'show']);
        Route::put('/soap-notes/{soapNote}', [SoapNoteController::class, 'update']);

        // ─── Evolutions ────────────────────────────────────────
        Route::get('/evolutions/{medicalRecordId}', [EvolutionController::class, 'index']);
        Route::post('/evolutions', [EvolutionController::class, 'store']);

        // ─── Vital Signs ───────────────────────────────────────
        Route::post('/vital-signs', [VitalSignController::class, 'store']);
        Route::get('/vital-signs/{patientId}', [VitalSignController::class, 'index']);

        // ─── Diagnoses ─────────────────────────────────────────
        Route::post('/diagnoses', [DiagnosisController::class, 'store']);
        Route::get('/diagnoses/{medicalRecordId}', [DiagnosisController::class, 'index']);
        Route::delete('/diagnoses/{diagnosis}', [DiagnosisController::class, 'destroy']);

        // ─── CIE-10 ────────────────────────────────────────────
        Route::get('/cie10', [Cie10Controller::class, 'index']);
        Route::get('/cie10/search', [Cie10Controller::class, 'search']);

        // ─── Appointments ──────────────────────────────────────
        Route::get('/appointments/available-slots', [AppointmentController::class, 'availableSlots']);
        Route::apiResource('appointments', AppointmentController::class);
        Route::post('/appointments/{appointment}/confirm', [AppointmentController::class, 'confirm']);
        Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel']);
        Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete']);
        Route::post('/appointments/{appointment}/no-show', [AppointmentController::class, 'noShow']);

        // ─── Doctor Schedules ──────────────────────────────────
        Route::apiResource('doctor-schedules', DoctorScheduleController::class);

        // ─── Prescriptions ─────────────────────────────────────
        Route::apiResource('prescriptions', PrescriptionController::class);
        Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf']);

        // ─── Lab Orders ────────────────────────────────────────
        Route::apiResource('lab-orders', LabOrderController::class);
        Route::get('/lab-results/{id}', [LabResultController::class, 'show']);
        Route::post('/lab-results', [LabResultController::class, 'store']);
        Route::post('/lab-results/{labOrderItem}/validate', [LabResultController::class, 'validateResult']);
        Route::get('/lab-results/patient/{patientId}', [LabResultController::class, 'index']);

        // ─── Imaging Orders ────────────────────────────────────
        Route::apiResource('imaging-orders', ImagingOrderController::class);
        Route::post('/imaging-results', [ImagingResultController::class, 'store']);
        Route::get('/imaging-results/{imagingOrder}', [ImagingResultController::class, 'show']);

        // ─── Medications ───────────────────────────────────────
        Route::get('/medications/low-stock', [MedicationController::class, 'lowStock']);
        Route::get('/medications/expiring', [MedicationController::class, 'expiring']);
        Route::apiResource('medications', MedicationController::class);

        // ─── Suppliers ─────────────────────────────────────────
        Route::apiResource('suppliers', SupplierController::class);

        // ─── Purchase Orders ───────────────────────────────────
        Route::apiResource('purchase-orders', PurchaseOrderController::class);
        Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive']);

        // ─── Inventory ─────────────────────────────────────────
        Route::apiResource('inventory-items', InventoryItemController::class);
        Route::apiResource('inventory-movements', InventoryMovementController::class)->only(['index', 'store']);

        // ─── Buildings ─────────────────────────────────────────
        Route::apiResource('buildings', BuildingController::class);

        // ─── Rooms ─────────────────────────────────────────────
        Route::apiResource('rooms', RoomController::class);

        // ─── Beds ──────────────────────────────────────────────
        Route::get('/beds/map', [BedController::class, 'bedMap']);
        Route::apiResource('beds', BedController::class);

        // ─── Hospitalization ───────────────────────────────────
        Route::apiResource('hospitalizations', HospitalizationController::class);
        Route::post('/hospitalizations/{hospitalization}/discharge', [HospitalizationController::class, 'discharge']);
        Route::post('/hospitalizations/{hospitalization}/transfer', [HospitalizationController::class, 'transfer']);
        Route::get('/hospitalizations/{hospitalization}/evolutions', [HospitalizationController::class, 'evolutions']);
        Route::get('/hospitalizations/{hospitalization}/transfers', [HospitalizationController::class, 'transfers']);

        // ─── Surgery ───────────────────────────────────────────
        Route::apiResource('surgery-schedules', SurgeryScheduleController::class);

        // ─── Emergency ─────────────────────────────────────────
        Route::apiResource('emergency-triage', EmergencyTriageController::class);
        Route::get('/emergency/waiting', [EmergencyTriageController::class, 'waiting']);
        Route::get('/emergency/stats', [EmergencyTriageController::class, 'triageStats']);

        // ─── Billing ───────────────────────────────────────────
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('payments', PaymentController::class)->only(['index', 'store']);
        Route::get('/service-catalog', [ServiceCatalogController::class, 'index']);
        Route::post('/service-catalog', [ServiceCatalogController::class, 'store']);

        // ─── Cash Register ─────────────────────────────────────
        Route::post('/cash-register/open', [CashRegisterController::class, 'open']);
        Route::post('/cash-register/close', [CashRegisterController::class, 'close']);
        Route::get('/cash-register/movements', [CashRegisterController::class, 'movements']);
        Route::get('/cash-register/arrears', [CashRegisterController::class, 'arrears']);

        // ─── Certificates ──────────────────────────────────────
        Route::apiResource('certificates', CertificateController::class);
        Route::get('/certificates/{certificate}/pdf', [CertificateController::class, 'pdf']);

        // ─── Telemedicine ──────────────────────────────────────
        Route::apiResource('telemedicine-sessions', TelemedicineController::class);
        Route::get('/telemedicine-sessions/{session}/messages', [TelemedicineController::class, 'messages']);
        Route::post('/telemedicine-sessions/{session}/messages', [TelemedicineController::class, 'sendMessage']);

        // ─── Reports ───────────────────────────────────────────
        Route::prefix('reports')->group(function () {
            Route::get('/dashboard', [ReportController::class, 'dashboard']);
            Route::get('/patients', [ReportController::class, 'patients']);
            Route::get('/appointments', [ReportController::class, 'appointments']);
            Route::get('/revenue', [ReportController::class, 'revenue']);
            Route::get('/lab', [ReportController::class, 'lab']);
            Route::get('/pharmacy', [ReportController::class, 'pharmacy']);
            Route::get('/hospitalization', [ReportController::class, 'hospitalization']);
            Route::get('/diagnoses', [ReportController::class, 'diagnoses']);
            Route::get('/export/{type}', [ReportController::class, 'export']);
        });

        // ─── Dashboard ─────────────────────────────────────────
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // ─── Settings ──────────────────────────────────────────
        Route::apiResource('settings', SettingController::class)->only(['index', 'update']);

        // ─── Institution ───────────────────────────────────────
        Route::get('/institution', function () {
            $institution = auth()->user()->institution;
            return response()->json([
                'success' => true,
                'data' => $institution,
            ]);
        });
        Route::put('/institution', function (\Illuminate\Http\Request $request) {
            $institution = auth()->user()->institution;
            $validated = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'nit' => ['sometimes', 'string', 'max:50'],
                'address' => ['sometimes', 'string', 'max:500'],
                'city' => ['sometimes', 'string', 'max:100'],
                'phone' => ['sometimes', 'string', 'max:50'],
                'email' => ['sometimes', 'email', 'max:255'],
                'logo' => ['sometimes', 'nullable', 'string', 'max:500'],
                'mission' => ['sometimes', 'nullable', 'string', 'max:2000'],
                'vision' => ['sometimes', 'nullable', 'string', 'max:2000'],
                'primary_color' => ['sometimes', 'nullable', 'string', 'max:7'],
                'secondary_color' => ['sometimes', 'nullable', 'string', 'max:7'],
            ]);
            $institution->update($validated);
            return response()->json([
                'success' => true,
                'data' => $institution->fresh(),
                'message' => 'Institution updated successfully.',
            ]);
        });

        // ─── Users (admin) ─────────────────────────────────────
        Route::apiResource('users', UserController::class);

        // ─── Notifications ─────────────────────────────────────
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);

        // ─── Backups ───────────────────────────────────────────
        Route::prefix('backups')->group(function () {
            Route::post('/create', [BackupController::class, 'create']);
            Route::get('/', [BackupController::class, 'list']);
            Route::get('/{backup}/download', [BackupController::class, 'download']);
            Route::post('/{backup}/restore', [BackupController::class, 'restore']);
        });
    });
});
