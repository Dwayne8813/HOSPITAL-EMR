<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\EmergencyTriage;
use App\Models\Hospitalization;
use App\Models\Invoice;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $institutionId = auth()->user()->institution_id;
            $today = now()->toDateString();
            $startOfMonth = now()->startOfMonth();

            $data = [
                'summary' => [
                    'total_patients' => Patient::byInstitution($institutionId)->count(),
                    'active_hospitalizations' => Hospitalization::where('institution_id', $institutionId)
                        ->where('status', 'active')->count(),
                    'today_appointments' => Appointment::byInstitution($institutionId)
                        ->byDate($today)->count(),
                    'pending_lab_orders' => LabOrder::byInstitution($institutionId)
                        ->where('status', 'pending')->count(),
                    'emergency_waiting' => EmergencyTriage::where('institution_id', $institutionId)
                        ->where('status', 'waiting')->count(),
                    'monthly_revenue' => Payment::where('institution_id', $institutionId)
                        ->where('received_at', '>=', $startOfMonth)
                        ->where('status', 'completed')->sum('amount'),
                    'pending_payments' => Invoice::byInstitution($institutionId)
                        ->where('payment_status', '!=', 'paid')
                        ->where('payment_status', '!=', 'cancelled')
                        ->sum('total_amount'),
                ],
                'recent_appointments' => Appointment::byInstitution($institutionId)
                    ->with(['patient', 'doctor'])
                    ->where('appointment_date', '>=', now())
                    ->whereNotIn('status', ['cancelled'])
                    ->orderBy('appointment_date')
                    ->limit(5)
                    ->get(),
                'recent_patients' => Patient::byInstitution($institutionId)
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get(),
                'emergency_waiting_list' => EmergencyTriage::where('institution_id', $institutionId)
                    ->where('status', 'waiting')
                    ->with(['patient'])
                    ->orderBy('triage_level')
                    ->limit(5)
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Dashboard data retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve dashboard data.',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }
}
