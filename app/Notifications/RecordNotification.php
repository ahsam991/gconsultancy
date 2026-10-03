<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RecordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $title, public string $message, public array $extra = [])
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return array_merge(['title' => $this->title, 'message' => $this->message], $this->extra);
    }
}
