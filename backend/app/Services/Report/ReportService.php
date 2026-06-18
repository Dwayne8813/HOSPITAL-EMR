<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Models\Diagnosis;
use App\Models\EmergencyTriage;
use App\Models\Hospitalization;
use App\Models\ImagingOrder;
use App\Models\Institution;
use App\Models\Invoice;
use App\Models\LabOrder;
use App\Models\Medication;
use App\Models\MedicationDispensation;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportService
{
    public function getDashboard(Institution $institution): array
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth();

        return [
            'patients' => [
                'total' => Patient::byInstitution($institution->id)->count(),
                'new_this_month' => Patient::byInstitution($institution->id)
                    ->where('created_at', '>=', $startOfMonth)->count(),
                'new_today' => Patient::byInstitution($institution->id)
                    ->whereDate('created_at', $today)->count(),
            ],
            'appointments' => [
                'today_total' => Appointment::byInstitution($institution->id)
                    ->byDate($today)->count(),
                'today_completed' => Appointment::byInstitution($institution->id)
                    ->byDate($today)->where('status', 'completed')->count(),
                'today_pending' => Appointment::byInstitution($institution->id)
                    ->byDate($today)->whereIn('status', ['scheduled', 'confirmed'])->count(),
                'today_cancelled' => Appointment::byInstitution($institution->id)
                    ->byDate($today)->where('status', 'cancelled')->count(),
            ],
            'revenue' => [
                'today' => Payment::where('institution_id', $institution->id)
                    ->whereDate('received_at', $today)
                    ->where('status', 'completed')->sum('amount'),
                'month' => Payment::where('institution_id', $institution->id)
                    ->where('received_at', '>=', $startOfMonth)
                    ->where('status', 'completed')->sum('amount'),
                'pending' => Invoice::byInstitution($institution->id)
                    ->where('payment_status', '!=', 'paid')
                    ->where('payment_status', '!=', 'cancelled')
                    ->sum('total_amount'),
            ],
            'emergency' => [
                'waiting' => EmergencyTriage::where('institution_id', $institution->id)
                    ->where('status', 'waiting')->count(),
                'today_total' => EmergencyTriage::where('institution_id', $institution->id)
                    ->whereDate('arrival_date', $today)->count(),
            ],
            'hospitalization' => [
                'active' => Hospitalization::where('institution_id', $institution->id)
                    ->where('status', 'active')->count(),
                'admissions_today' => Hospitalization::where('institution_id', $institution->id)
                    ->whereDate('admission_date', $today)->count(),
            ],
            'laboratory' => [
                'pending' => LabOrder::byInstitution($institution->id)
                    ->where('status', 'pending')->count(),
                'completed_today' => LabOrder::byInstitution($institution->id)
                    ->where('status', 'completed')
                    ->whereDate('updated_at', $today)->count(),
            ],
            'pharmacy' => [
                'low_stock' => Medication::byInstitution($institution->id)
                    ->lowStock()->count(),
            ],
        ];
    }

    public function getPatientReport(Institution $institution, array $filters = []): array
    {
        $query = Patient::byInstitution($institution->id);

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $total = $query->count();
        $genderDistribution = (clone $query)
            ->selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        $ageGroups = (clone $query)
            ->selectRaw("
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN 'child'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 35 THEN 'young_adult'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 55 THEN 'middle_aged'
                    ELSE 'senior'
                END as age_group,
                count(*) as count
            ")
            ->groupBy('age_group')
            ->pluck('count', 'age_group')
            ->toArray();

        $monthlyTrend = (clone $query)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return [
            'total_patients' => $total,
            'gender_distribution' => $genderDistribution,
            'age_distribution' => $ageGroups,
            'monthly_trend' => $monthlyTrend,
        ];
    }

    public function getAppointmentReport(Institution $institution, array $filters = []): array
    {
        $query = Appointment::byInstitution($institution->id);

        if (isset($filters['date_from'])) {
            $query->where('appointment_date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('appointment_date', '<=', $filters['date_to']);
        }
        if (isset($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        $total = $query->count();
        $statusDistribution = (clone $query)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $dailyTrend = (clone $query)
            ->selectRaw('DATE(appointment_date) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return [
            'total_appointments' => $total,
            'status_distribution' => $statusDistribution,
            'daily_trend' => $dailyTrend,
            'no_show_rate' => $total > 0
                ? round(($statusDistribution['no_show'] ?? 0) / $total * 100, 2)
                : 0,
            'cancellation_rate' => $total > 0
                ? round(($statusDistribution['cancelled'] ?? 0) / $total * 100, 2)
                : 0,
        ];
    }

    public function getRevenueReport(Institution $institution, array $filters = []): array
    {
        $query = Payment::where('institution_id', $institution->id)
            ->where('status', 'completed');

        if (isset($filters['date_from'])) {
            $query->where('received_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('received_at', '<=', $filters['date_to']);
        }

        $totalRevenue = (clone $query)->sum('amount');

        $byMethod = (clone $query)
            ->selectRaw('payment_method, count(*) as count, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->toArray();

        $dailyRevenue = (clone $query)
            ->selectRaw('DATE(received_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();

        return [
            'total_revenue' => $totalRevenue,
            'by_method' => $byMethod,
            'daily_revenue' => $dailyRevenue,
            'average_payment' => $totalRevenue / max(count($dailyRevenue), 1),
        ];
    }

    public function getLabReport(Institution $institution, array $filters = []): array
    {
        $query = LabOrder::byInstitution($institution->id);

        if (isset($filters['date_from'])) {
            $query->where('order_date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('order_date', '<=', $filters['date_to']);
        }

        $totalOrders = $query->count();
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $pendingOrders = (clone $query)->where('status', 'pending')->count();

        return [
            'total_orders' => $totalOrders,
            'completed' => $completedOrders,
            'pending' => $pendingOrders,
            'average_turnaround_hours' => 0,
            'completion_rate' => $totalOrders > 0 ? round($completedOrders / $totalOrders * 100, 2) : 0,
        ];
    }

    public function getPharmacyReport(Institution $institution, array $filters = []): array
    {
        $query = MedicationDispensation::whereHas('medication', function ($q) use ($institution) {
            $q->where('institution_id', $institution->id);
        });

        if (isset($filters['date_from'])) {
            $query->where('dispensed_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('dispensed_at', '<=', $filters['date_to']);
        }

        $totalDispensations = $query->count();
        $totalRevenue = 0;

        $topMedications = (clone $query)
            ->selectRaw('medication_id, COUNT(*) as count')
            ->groupBy('medication_id')
            ->orderByDesc('count')
            ->limit(10)
            ->with('medication')
            ->get();

        return [
            'total_dispensations' => $totalDispensations,
            'total_revenue' => $totalRevenue,
            'top_medications' => $topMedications,
            'low_stock_count' => Medication::byInstitution($institution->id)->lowStock()->count(),
        ];
    }

    public function getHospitalizationReport(Institution $institution, array $filters = []): array
    {
        $query = Hospitalization::where('institution_id', $institution->id);

        if (isset($filters['date_from'])) {
            $query->where('admission_date', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('admission_date', '<=', $filters['date_to']);
        }

        $totalAdmissions = $query->count();
        $active = (clone $query)->where('status', 'active')->count();
        $discharged = (clone $query)->where('status', 'discharged')->count();

        $avgLength = Hospitalization::where('institution_id', $institution->id)
            ->where('status', 'discharged')
            ->whereNotNull('actual_discharge_date')
            ->selectRaw('AVG(DATEDIFF(actual_discharge_date, admission_date)) as avg_days')
            ->value('avg_days') ?? 0;

        return [
            'total_admissions' => $totalAdmissions,
            'active' => $active,
            'discharged' => $discharged,
            'average_length_of_stay' => round($avgLength, 2),
        ];
    }

    public function getDiagnosisReport(Institution $institution, array $filters = []): array
    {
        $query = Diagnosis::whereHas('medicalRecord', function ($q) use ($institution) {
            $q->where('institution_id', $institution->id);
        });

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $topDiagnoses = $query
            ->selectRaw('description, diagnosis_type, COUNT(*) as count')
            ->groupBy('description', 'diagnosis_type')
            ->orderByRaw('count DESC')
            ->limit(20)
            ->get();

        $byType = (clone $query)
            ->selectRaw('diagnosis_type, count(*) as count')
            ->groupBy('diagnosis_type')
            ->pluck('count', 'diagnosis_type')
            ->toArray();

        return [
            'top_diagnoses' => $topDiagnoses,
            'by_type' => $byType,
            'total_diagnoses' => $query->count(),
        ];
    }

    public function exportExcel(array $data, array $columns): string
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\GenericExport($data, $columns),
            'report_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    public function exportPdf(array $data, string $template): string
    {
        return \Barryvdh\DomPDF\Facades\Pdf::loadView("reports.{$template}", ['data' => $data])
            ->download('report_' . now()->format('Y-m-d_His') . '.pdf');
    }
}
