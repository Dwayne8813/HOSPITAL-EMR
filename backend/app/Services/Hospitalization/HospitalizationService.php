<?php

declare(strict_types=1);

namespace App\Services\Hospitalization;

use App\DTOs\HospitalizationDTO;
use App\Models\Bed;
use App\Models\BedTransfer;
use App\Models\Building;
use App\Models\Hospitalization;
use App\Models\HospitalizationEvolution;
use App\Models\Institution;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HospitalizationService
{
    public function admit(array $data): Hospitalization
    {
        return DB::transaction(function () use ($data) {
            $institutionId = auth()->user()->institution_id;

            if (isset($data['bed_id'])) {
                $bed = Bed::findOrFail($data['bed_id']);
                if ($bed->status !== 'available') {
                    throw new \App\Exceptions\BusinessLogicException('Selected bed is not available.');
                }
                $bed->update(['status' => 'occupied']);
            }

            return Hospitalization::create([
                'institution_id' => $institutionId,
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'room_id' => $data['room_id'] ?? null,
                'bed_id' => $data['bed_id'] ?? null,
                'status' => 'active',
                'admission_reason' => $data['admission_reason'],
                'diagnosis' => $data['diagnosis'] ?? null,
                'notes' => $data['notes'] ?? null,
                'admission_date' => $data['admission_date'] ?? now()->toDateTimeString(),
            ]);
        });
    }

    public function discharge(Hospitalization $hospitalization, array $data): Hospitalization
    {
        return DB::transaction(function () use ($hospitalization, $data) {
            $hospitalization->update([
                'status' => 'discharged',
                'discharge_date' => $data['discharge_date'] ?? now()->toDateTimeString(),
                'discharge_summary' => $data['discharge_summary'] ?? null,
                'final_diagnosis' => $data['final_diagnosis'] ?? null,
            ]);

            if ($hospitalization->bed_id) {
                Bed::where('id', $hospitalization->bed_id)
                    ->update(['status' => 'available']);
            }

            return $hospitalization->fresh()->load(['patient', 'doctor', 'room', 'bed']);
        });
    }

    public function transfer(Hospitalization $hospitalization, int $toBedId, string $reason): Hospitalization
    {
        return DB::transaction(function () use ($hospitalization, $toBedId, $reason) {
            $toBed = Bed::findOrFail($toBedId);
            if ($toBed->status !== 'available') {
                throw new \App\Exceptions\BusinessLogicException('Target bed is not available.');
            }

            if ($hospitalization->bed_id) {
                Bed::where('id', $hospitalization->bed_id)->update(['status' => 'available']);
            }

            BedTransfer::create([
                'hospitalization_id' => $hospitalization->id,
                'institution_id' => $hospitalization->institution_id,
                'from_bed_id' => $hospitalization->bed_id,
                'to_bed_id' => $toBedId,
                'reason' => $reason,
                'transferred_by' => auth()->id(),
                'transferred_at' => now(),
            ]);

            $toBed->update(['status' => 'occupied']);

            $hospitalization->update([
                'bed_id' => $toBedId,
                'room_id' => $toBed->room_id,
            ]);

            return $hospitalization->fresh()->load(['patient', 'doctor', 'room', 'bed']);
        });
    }

    public function getBedMap(Institution $institution): array
    {
        $buildings = Building::where('institution_id', $institution->id)
            ->with(['rooms.beds.hospitalization.patient'])
            ->get();

        $summary = [
            'total_beds' => Bed::where('institution_id', $institution->id)->count(),
            'occupied' => Bed::where('institution_id', $institution->id)->where('status', 'occupied')->count(),
            'available' => Bed::where('institution_id', $institution->id)->where('status', 'available')->count(),
            'maintenance' => Bed::where('institution_id', $institution->id)->where('status', 'maintenance')->count(),
            'occupancy_rate' => 0,
        ];

        if ($summary['total_beds'] > 0) {
            $summary['occupancy_rate'] = round(($summary['occupied'] / $summary['total_beds']) * 100, 2);
        }

        return [
            'buildings' => $buildings,
            'summary' => $summary,
        ];
    }

    public function listActive(Institution $institution): \Illuminate\Database\Eloquent\Collection
    {
        return Hospitalization::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->with(['patient', 'doctor', 'room', 'bed'])
            ->orderByDesc('admission_date')
            ->get();
    }

    public function addEvolution(int $hospitalizationId, array $data): HospitalizationEvolution
    {
        return HospitalizationEvolution::create([
            'hospitalization_id' => $hospitalizationId,
            'institution_id' => auth()->user()->institution_id,
            'doctor_id' => $data['doctor_id'] ?? auth()->id(),
            'evolution_type' => $data['evolution_type'] ?? 'clinical',
            'subjective' => $data['subjective'] ?? null,
            'objective' => $data['objective'] ?? null,
            'assessment' => $data['assessment'] ?? null,
            'plan' => $data['plan'] ?? null,
            'notes' => $data['notes'] ?? null,
            'evolution_date' => $data['evolution_date'] ?? now()->toDateTimeString(),
        ]);
    }

    public function getEvolutions(int $hospitalizationId): \Illuminate\Database\Eloquent\Collection
    {
        return HospitalizationEvolution::where('hospitalization_id', $hospitalizationId)
            ->with(['doctor'])
            ->orderByDesc('evolution_date')
            ->get();
    }

    public function getTransfers(int $hospitalizationId): \Illuminate\Database\Eloquent\Collection
    {
        return BedTransfer::where('hospitalization_id', $hospitalizationId)
            ->with(['fromBed.room', 'toBed.room', 'transferredBy'])
            ->orderByDesc('transferred_at')
            ->get();
    }

    public function getStats(Institution $institution): array
    {
        $startOfMonth = now()->startOfMonth();

        return [
            'current_active' => Hospitalization::where('institution_id', $institution->id)
                ->where('status', 'active')->count(),
            'admissions_this_month' => Hospitalization::where('institution_id', $institution->id)
                ->where('admission_date', '>=', $startOfMonth)->count(),
            'discharges_this_month' => Hospitalization::where('institution_id', $institution->id)
                ->where('status', 'discharged')
                ->where('discharge_date', '>=', $startOfMonth)->count(),
            'average_length_of_stay' => Hospitalization::where('institution_id', $institution->id)
                ->where('status', 'discharged')
                ->where('discharge_date', '>=', $startOfMonth)
                ->selectRaw('AVG(DATEDIFF(discharge_date, admission_date)) as avg_days')
                ->value('avg_days') ?? 0,
            'bed_occupancy_rate' => $this->calculateOccupancyRate($institution->id),
        ];
    }

    private function calculateOccupancyRate(int $institutionId): float
    {
        $totalBeds = Bed::where('institution_id', $institutionId)->count();
        if ($totalBeds === 0) return 0;

        $occupiedBeds = Bed::where('institution_id', $institutionId)
            ->where('status', 'occupied')->count();

        return round(($occupiedBeds / $totalBeds) * 100, 2);
    }
}
