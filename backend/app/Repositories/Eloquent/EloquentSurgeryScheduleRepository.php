<?php

namespace App\Repositories\Eloquent;

use App\Models\SurgerySchedule;
use App\Repositories\Interfaces\SurgeryScheduleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentSurgeryScheduleRepository implements SurgeryScheduleRepositoryInterface
{
    public function __construct(
        protected SurgerySchedule $model,
    ) {}

    public function findById(int $id): ?SurgerySchedule
    {
        return $this->model->with('patient', 'doctor', 'operatingRoom', 'institution', 'team')->find($id);
    }

    public function create(array $data): SurgerySchedule
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): SurgerySchedule
    {
        $surgery = $this->model->findOrFail($id);
        $surgery->update($data);

        return $surgery;
    }

    public function listByDoctor(int $doctorId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('doctor_id', $doctorId)
            ->with('patient', 'operatingRoom', 'team')
            ->orderBy('scheduled_start', 'desc')
            ->paginate($perPage);
    }

    public function listByInstitution(int $institutionId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->byInstitution($institutionId)
            ->with('patient', 'doctor', 'operatingRoom');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        return $query->orderBy('scheduled_start', 'desc')->paginate($perPage);
    }

    public function getByDateRange(int $institutionId, string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byInstitution($institutionId)
            ->whereBetween('scheduled_start', [$from, $to])
            ->with('patient', 'doctor', 'operatingRoom', 'team')
            ->orderBy('scheduled_start')
            ->paginate($perPage);
    }

    public function getByOR(int $operatingRoomId, string $date, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('operating_room_id', $operatingRoomId)
            ->whereDate('scheduled_start', $date)
            ->with('patient', 'doctor', 'team')
            ->orderBy('scheduled_start')
            ->paginate($perPage);
    }

    public function getWithTeam(int $id): ?SurgerySchedule
    {
        return $this->model
            ->with(['patient', 'doctor', 'operatingRoom', 'team.user', 'materials', 'report'])
            ->find($id);
    }
}
