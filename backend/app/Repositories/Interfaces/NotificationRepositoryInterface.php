<?php

namespace App\Repositories\Interfaces;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryInterface
{
    public function findById(int $id): ?Notification;

    public function create(array $data): Notification;

    public function listByUser(int $userId, ?string $type = null, int $limit = 50): Collection;

    public function markAsRead(int $id): bool;

    public function getUnread(int $userId): Collection;
}
