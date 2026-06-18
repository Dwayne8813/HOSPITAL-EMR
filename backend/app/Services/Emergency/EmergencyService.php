<?php

declare(strict_types=1);

namespace App\Services\Emergency;

use App\DTOs\EmergencyTriageDTO;
use App\Models\EmergencyTriage;
use App\Models\Institution;
use Illuminate\Http\Request;

class EmergencyService
{
    public function triage(array $data): EmergencyTriage
    {
        $institutionId = auth()->user()->institution_id;

        return EmergencyTriage::create([
            'institution_id' => $institutionId,
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'] ?? null,
            'triage_level' => $data['triage_level'] ?? 'V',
            'status' => 'waiting',
            'chief_complaint' => $data['chief_complaint'] ?? null,
            'arrival_mode' => $data['arrival_mode'] ?? null,
            'notes' => $data['notes'] ?? null,
            'assigned_bed_id' => $data['assigned_bed_id'] ?? null,
            'arrival_time' => $data['arrival_time'] ?? now()->toDateTimeString(),
            'triage_time' => now()->toDateTimeString(),
        ]);
    }

    public function getWaiting(Institution $institution): \Illuminate\Database\Eloquent\Collection
    {
        return EmergencyTriage::where('institution_id', $institution->id)
            ->where('status', 'waiting')
            ->with(['patient', 'doctor', 'assignedBed'])
            ->orderBy('triage_level')
            ->orderBy('triage_time')
            ->get();
    }

    public function updateStatus(int $triageId, string $status, ?string $notes = null): EmergencyTriage
    {
        $triage = EmergencyTriage::findOrFail($triageId);

        $updateData = ['status' => $status];

        if ($notes) {
            $updateData['notes'] = $triage->notes ? $triage->notes . "\n" . $notes : $notes;
        }

        match ($status) {
            'in_treatment' => $updateData['treatment_start_time'] = now()->toDateTimeString(),
            'discharged' => $updateData['discharge_time'] = now()->toDateTimeString(),
            default => null,
        };

        $triage->update($updateData);

        return $triage->fresh()->load(['patient', 'doctor']);
    }

    public function getTriageStats(Institution $institution): array
    {
        $today = now()->toDateString();

        $levels = EmergencyTriage::where('institution_id', $institution->id)
            ->whereDate('created_at', $today)
            ->selectRaw('triage_level, count(*) as count')
            ->groupBy('triage_level')
            ->pluck('count', 'triage_level')
            ->toArray();

        $statusCounts = EmergencyTriage::where('institution_id', $institution->id)
            ->whereDate('created_at', $today)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total_today' => EmergencyTriage::where('institution_id', $institution->id)
                ->whereDate('created_at', $today)->count(),
            'by_level' => $levels,
            'by_status' => $statusCounts,
            'average_wait_time' => $this->calculateAverageWaitTime($institution->id, $today),
        ];
    }

    public function getTodaysPatients(Institution $institution): \Illuminate\Database\Eloquent\Collection
    {
        return EmergencyTriage::where('institution_id', $institution->id)
            ->whereDate('created_at', now()->toDateString())
            ->with(['patient', 'doctor'])
            ->orderBy('triage_time')
            ->get();
    }

    private function calculateAverageWaitTime(int $institutionId, string $date): float
    {
        $triages = EmergencyTriage::where('institution_id', $institutionId)
            ->whereDate('created_at', $date)
            ->whereNotNull('treatment_start_time')
            ->get();

        if ($triages->isEmpty()) return 0;

        $totalMinutes = $triages->sum(function ($triage) {
            return \Carbon\Carbon::parse($triage->triage_time)
                ->diffInMinutes(\Carbon\Carbon::parse($triage->treatment_start_time));
        });

        return round($totalMinutes / $triages->count(), 2);
    }
}
