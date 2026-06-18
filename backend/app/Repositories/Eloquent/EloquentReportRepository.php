<?php

namespace App\Repositories\Eloquent;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\LabOrder;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Repositories\Interfaces\ReportRepositoryInterface;

class EloquentReportRepository implements ReportRepositoryInterface
{
    public function getDashboardStats(int $institutionId): array
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        return [
            'patients' => [
                'total' => Patient::byInstitution($institutionId)->count(),
                'today' => Patient::byInstitution($institutionId)->whereDate('created_at', $today)->count(),
                'this_month' => Patient::byInstitution($institutionId)->whereDate('created_at', '>=', $monthStart)->count(),
            ],
            'appointments' => [
                'today' => Appointment::byInstitution($institutionId)->byDate($today)->count(),
                'upcoming' => Appointment::byInstitution($institutionId)->upcoming()->count(),
                'completed_today' => Appointment::byInstitution($institutionId)->whereDate('scheduled_at', $today)->where('status', 'completed')->count(),
            ],
            'revenue' => [
                'today' => (float) Payment::byInstitution($institutionId)->whereDate('payment_date', $today)->sum('amount'),
                'this_month' => (float) Payment::byInstitution($institutionId)->whereDate('payment_date', '>=', $monthStart)->sum('amount'),
            ],
            'lab_orders' => [
                'pending' => LabOrder::byInstitution($institutionId)->pending()->count(),
                'today' => LabOrder::byInstitution($institutionId)->whereDate('ordered_at', $today)->count(),
            ],
            'pharmacy' => [
                'low_stock' => Medication::byInstitution($institutionId)->lowStock()->count(),
                'prescriptions_today' => Prescription::byInstitution($institutionId)->whereDate('prescribed_at', $today)->count(),
            ],
        ];
    }

    public function getPatientStats(int $institutionId, ?string $from = null, ?string $to = null): array
    {
        $query = Patient::byInstitution($institutionId);

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $total = $query->count();
        $byGender = Patient::byInstitution($institutionId)->select('gender', \DB::raw('COUNT(*) as total'))->groupBy('gender')->pluck('total', 'gender')->toArray();

        $ageGroups = Patient::byInstitution($institutionId)
            ->select(
                \DB::raw("CASE
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN '0-17'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 30 THEN '18-30'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 31 AND 50 THEN '31-50'
                    WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 51 AND 70 THEN '51-70'
                    ELSE '70+'
                END as age_group"),
                \DB::raw('COUNT(*) as total')
            )
            ->groupBy('age_group')
            ->pluck('total', 'age_group')
            ->toArray();

        return [
            'total' => $total,
            'by_gender' => $byGender,
            'by_age_group' => $ageGroups,
        ];
    }

    public function getAppointmentStats(int $institutionId, ?string $from = null, ?string $to = null): array
    {
        $query = Appointment::byInstitution($institutionId);

        if ($from && $to) {
            $query->whereBetween('scheduled_at', [$from, $to]);
        }

        $byStatus = $query->clone()->select('status', \DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')->toArray();

        $byDay = $query->clone()
            ->select(\DB::raw('DATE(scheduled_at) as date'), \DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $noShow = $query->clone()->where('no_show', true)->count();

        return [
            'total' => array_sum($byStatus),
            'by_status' => $byStatus,
            'by_day' => $byDay,
            'no_show' => $noShow,
            'no_show_rate' => array_sum($byStatus) > 0 ? round(($noShow / array_sum($byStatus)) * 100, 2) : 0,
        ];
    }

    public function getRevenueStats(int $institutionId, ?string $from = null, ?string $to = null): array
    {
        $paymentQuery = Payment::byInstitution($institutionId);
        $invoiceQuery = Invoice::byInstitution($institutionId);

        if ($from && $to) {
            $paymentQuery->whereBetween('payment_date', [$from, $to]);
            $invoiceQuery->whereBetween('issued_at', [$from, $to]);
        }

        $totalPayments = (float) $paymentQuery->sum('amount');
        $totalInvoiced = (float) $invoiceQuery->sum('total_amount');
        $totalPending = (float) Invoice::byInstitution($institutionId)->where('status', '!=', 'paid')->sum('balance');

        $byMonth = Payment::byInstitution($institutionId)
            ->select(\DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'), \DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return [
            'total_invoiced' => $totalInvoiced,
            'total_payments' => $totalPayments,
            'total_pending' => $totalPending,
            'collection_rate' => $totalInvoiced > 0 ? round(($totalPayments / $totalInvoiced) * 100, 2) : 0,
            'by_month' => $byMonth,
        ];
    }

    public function getLabStats(int $institutionId, ?string $from = null, ?string $to = null): array
    {
        $query = LabOrder::byInstitution($institutionId);

        if ($from && $to) {
            $query->whereBetween('ordered_at', [$from, $to]);
        }

        $byStatus = $query->clone()->select('status', \DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')->toArray();

        $byPriority = $query->clone()->select('priority', \DB::raw('COUNT(*) as total'))->groupBy('priority')->pluck('total', 'priority')->toArray();

        $completedCount = $query->clone()->where('status', 'completed')->count();
        $totalCount = $query->clone()->count();

        return [
            'total' => $totalCount,
            'by_status' => $byStatus,
            'by_priority' => $byPriority,
            'completion_rate' => $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 2) : 0,
        ];
    }

    public function getPharmacyStats(int $institutionId, ?string $from = null, ?string $to = null): array
    {
        $lowStock = Medication::byInstitution($institutionId)->lowStock()->count();
        $totalMedications = Medication::byInstitution($institutionId)->active()->count();

        $prescriptionQuery = Prescription::byInstitution($institutionId);

        if ($from && $to) {
            $prescriptionQuery->whereBetween('prescribed_at', [$from, $to]);
        }

        $totalPrescriptions = $prescriptionQuery->count();
        $dispensedPrescriptions = (clone $prescriptionQuery)->where('status', 'dispensed')->count();

        return [
            'total_medications' => $totalMedications,
            'low_stock' => $lowStock,
            'total_prescriptions' => $totalPrescriptions,
            'dispensed_prescriptions' => $dispensedPrescriptions,
            'dispensation_rate' => $totalPrescriptions > 0 ? round(($dispensedPrescriptions / $totalPrescriptions) * 100, 2) : 0,
        ];
    }
}
