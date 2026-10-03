<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Lead;
use App\Models\SlaBreach;
use App\Models\SlaPolicy;
use App\Models\Task;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CheckSlaBreaches extends Command
{
    protected $signature = 'app:check-sla';

    protected $description = 'Detect SLA breaches for active policies and notify managers.';

    public function handle(): int
    {
        if (! Schema::hasTable('sla_policies') || ! Schema::hasTable('sla_breaches')) {
            $this->warn('SLA tables missing; skipping.');

            return 0;
        }

        $policies = SlaPolicy::where('active', true)->get();
        $created = 0;

        foreach ($policies as $policy) {
            $overdue = $this->overdueFor($policy);
            foreach ($overdue as $item) {
                $exists = SlaBreach::where('sla_policy_id', $policy->id)
                    ->where('related_type', $item['type'])
                    ->where('related_id', $item['id'])
                    ->whereNull('resolved_at')
                    ->exists();
                if ($exists) {
                    continue; // idempotent: unresolved breach already recorded
                }
                SlaBreach::create([
                    'sla_policy_id' => $policy->id,
                    'related_type' => $item['type'],
                    'related_id' => $item['id'],
                    'detected_at' => now(),
                    'notified' => false,
                ]);
                $created++;
            }
            if ($created > 0) {
                $this->notifyManagers($policy);
            }
        }

        SlaBreach::whereIn('sla_policy_id', $policies->pluck('id'))
            ->where('notified', false)
            ->update(['notified' => true]);

        $this->info("Checked {$policies->count()} policies; created {$created} breaches.");

        return 0;
    }

    /**
     * @return array<int, array{type: string, id: int}>
     */
    protected function overdueFor(SlaPolicy $policy): array
    {
        $cutoff = now()->subHours(max(0, (int) $policy->hours));
        $event = strtolower((string) $policy->event);

        return match ($event) {
            'new_lead' => Lead::where('status', 'NEW')->where('created_at', '<', $cutoff)
                ->pluck('id')->map(fn ($id) => ['type' => Lead::class, 'id' => $id])->all(),
            'new_application' => Application::where('status', 'DRAFT')->where('created_at', '<', $cutoff)
                ->pluck('id')->map(fn ($id) => ['type' => Application::class, 'id' => $id])->all(),
            'missing_document' => $this->missingDocumentApplications($cutoff),
            'task_overdue' => Task::whereDate('due_date', '<', now()->toDateString())
                ->whereNotIn('status', ['completed', 'COMPLETED', 'cancelled', 'CANCELLED'])
                ->pluck('id')->map(fn ($id) => ['type' => Task::class, 'id' => $id])->all(),
            default => [],
        };
    }

    protected function missingDocumentApplications($cutoff): array
    {
        if (! class_exists(\App\Services\DocumentService::class)
            || ! method_exists(\App\Services\DocumentService::class, 'checklist')) {
            return [];
        }
        $apps = Application::where('created_at', '<', $cutoff)->get();
        $out = [];
        foreach ($apps as $app) {
            try {
                $checklist = \App\Services\DocumentService::checklist($app);
            } catch (\Throwable $e) {
                continue;
            }
            $percent = $checklist['percent'] ?? 100;
            if ($percent < 100) {
                $out[] = ['type' => Application::class, 'id' => $app->id];
            }
        }

        return $out;
    }

    protected function notifyManagers(SlaPolicy $policy): void
    {
        if (! class_exists(\App\Notifications\RecordNotification::class)) {
            return;
        }
        $managers = User::whereHas('role', fn ($q) => $q->where('name', 'manager'))->get();
        foreach ($managers as $manager) {
            try {
                $manager->notify(new \App\Notifications\RecordNotification(
                    'SLA breach: '.$policy->name,
                    'Policy "'.$policy->name.'" ('.$policy->event.') has overdue records.',
                    ['sla_policy_id' => $policy->id]
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
