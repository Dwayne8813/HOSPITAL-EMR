<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentNotificationRepository implements NotificationRepositoryInterface
{
    public function __construct(
        protected Notification $model,
    ) {}

    public function findById(int $id): ?Notification
    {
        return $this->model->with('user', 'institution')->find($id);
    }

    public function create(array $data): Notification
    {
        return $this->model->create($data);
    }

    public function listByUser(int $userId, ?string $type = null, int $limit = 50): Collection
    {
        $query = $this->model->forUser($userId);

        if ($type) {
            $query->byType($type);
        }

        return $query->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function markAsRead(int $id): bool
    {
        $notification = $this->model->findOrFail($id);

        return $notification->update(['read_at' => now()]);
    }

    public function getUnread(int $userId): Collection
    {
        return $this->model
            ->forUser($userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
