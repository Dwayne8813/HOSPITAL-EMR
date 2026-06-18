<?php

namespace App\Repositories\Eloquent;

use App\Models\Hospitalization;
use App\Repositories\Interfaces\HospitalizationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentHospitalizationRepository implements HospitalizationRepositoryInterface
{
    public function __construct(
        protected Hospitalization $model,
    ) {}

    public function findById(int $id): ?Hospitalization
    {
        return $this->model->with('patient', 'doctor', 'bed', 'room', 'institution', 'evolutions', 'transfers')->find($id);
    }

    public function create(array $data): Hospitalization
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Hospitalization
    {
        $hospitalization = $this->model->findOrFail($id);
        $hospitalization->update($data);

        return $hospitalization;
    }

    public function listByPatient(int $patientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byPatient($patientId)
            ->with('doctor', 'bed', 'room')
            ->orderBy('admission_date', 'desc')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor', 'bed', 'room');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['doctor_id'])) {
            $query->byDoctor($filters['doctor_id']);
        }

        return $query->orderBy('admission_date', 'desc')->paginate($perPage);
    }

    public function admit(array $data): Hospitalization
    {
        $hospitalization = $this->model->create($data);

        if (!empty($data['bed_id'])) {
            \App\Models\Bed::where('id', $data['bed_id'])->update(['status' => 'occupied']);
        }

        return $hospitalization->fresh(['patient', 'doctor', 'bed', 'room']);
    }

    public function discharge(int $id, array $data): Hospitalization
    {
        $hospitalization = $this->model->findOrFail($id);
        $hospitalization->update(array_merge($data, [
            'status' => 'discharged',
            'discharged_at' => now(),
        ]));

        if ($hospitalization->bed_id) {
            \App\Models\Bed::where('id', $hospitalization->bed_id)->update(['status' => 'available']);
            \App\Models\Room::where('id', $hospitalization->room_id)->decrement('current_occupancy');
        }

        return $hospitalization->fresh(['patient', 'doctor', 'bed', 'room']);
    }

    public function transfer(int $id, int $newBedId, ?string $reason = null): Hospitalization
    {
        $hospitalization = $this->model->findOrFail($id);
        $oldBedId = $hospitalization->bed_id;
        $oldRoomId = $hospitalization->room_id;

        $newBed = \App\Models\Bed::findOrFail($newBedId);

        $hospitalization->update([
            'bed_id' => $newBedId,
            'room_id' => $newBed->room_id,
        ]);

        \App\Models\Bed::where('id', $oldBedId)->update(['status' => 'available']);
        \App\Models\Bed::where('id', $newBedId)->update(['status' => 'occupied']);

        \App\Models\Room::where('id', $oldRoomId)->decrement('current_occupancy');
        \App\Models\Room::where('id', $newBed->room_id)->increment('current_occupancy');

        $hospitalization->transfers()->create([
            'from_bed_id' => $oldBedId,
            'from_room_id' => $oldRoomId,
            'to_bed_id' => $newBedId,
            'to_room_id' => $newBed->room_id,
            'reason' => $reason,
            'transferred_by' => auth()->id(),
            'transferred_at' => now(),
        ]);

        return $hospitalization->fresh(['bed', 'room', 'transfers']);
    }

    public function getActive(int $institutionId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->active()
            ->with('patient', 'doctor', 'bed', 'room')
            ->orderBy('admission_date', 'desc')
            ->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereBetween('admission_date', [$from, $to])
            ->with('patient', 'doctor')
            ->orderBy('admission_date', 'desc')
            ->paginate($perPage);
    }
}
