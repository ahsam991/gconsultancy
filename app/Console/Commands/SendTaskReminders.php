<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SendTaskReminders extends Command
{
    protected $signature = 'app:send-task-reminders';

    protected $description = 'Notify assignees of tasks due in the next 24 hours.';

    public function handle(): int
    {
        $tasks = Task::with('assignee')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDay()->toDateString()])
            ->whereNotIn('status', ['completed', 'COMPLETED', 'cancelled', 'CANCELLED'])
            ->get();

        $sent = 0;
        $skipped = 0;
        foreach ($tasks as $task) {
            $assignee = $task->assignee;
            if (! $assignee) {
                $skipped++;
                continue;
            }
            if ($this->alreadyNotified($assignee->id, $task->id)) {
                $skipped++; // idempotent: reminder already sent in last 24h
                continue;
            }
            if (! class_exists(\App\Notifications\RecordNotification::class)) {
                $skipped++;
                continue;
            }
            try {
                $assignee->notify(new \App\Notifications\RecordNotification(
                    'Task due soon: '.$task->title,
                    'Task "'.$task->title.'" is due on '.$task->due_date.'.',
                    ['task_id' => $task->id, 'reminder' => 'task_due_24h']
                ));
                $sent++;
            } catch (\Throwable $e) {
                report($e);
                $skipped++;
            }
        }

        $this->info("Task reminders: sent {$sent}, skipped {$skipped}.");

        return 0;
    }

    protected function alreadyNotified(int $userId, int $taskId): bool
    {
        if (! Schema::hasTable('notifications')) {
            return false;
        }
        $rows = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('created_at', '>=', now()->subDay())
            ->pluck('data');
        foreach ($rows as $raw) {
            $data = is_string($raw) ? json_decode($raw, true) : (array) $raw;
            if (($data['task_id'] ?? null) == $taskId && ($data['reminder'] ?? null) === 'task_due_24h') {
                return true;
            }
        }

        return false;
    }
}
