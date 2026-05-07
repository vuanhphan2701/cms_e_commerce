<?php

namespace App\Repositories;

use App\Models\Notification;
use Core\Repositories\BaseRepository;

class NotificationRepository extends BaseRepository
{
    protected string $model = Notification::class;

    public function getUnreadForUser(int $userId)
    {
        return $this->model::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsRead(int $notificationId)
    {
        return $this->update($notificationId, [
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markAllAsRead(int $userId)
    {
        return $this->model::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }
}
