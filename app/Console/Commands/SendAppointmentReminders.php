<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SendAppointmentReminders extends Command
{
    protected $signature = 'app:send-appointment-reminders';

    protected $description = 'Notify staff/candidates of appointments in the next 25 hours.';

    public function handle(): int
    {
        $hasFlag = Schema::hasColumn('appointments', 'reminder_sent_at');

        $query = Appointment::with(['staff', 'candidate'])
            ->whereBetween('appointment_date', [now(), now()->addHours(25)])
            ->whereNotIn('status', ['CANCELLED', 'cancelled']);
        if ($hasFlag) {
            $query->whereNull('reminder_sent_at'); // idempotent via flag
        }
        $appointments = $query->get();

        $sent = 0;
        $skipped = 0;
        foreach ($appointments as $appointment) {
            if (! $hasFlag && $appointment->staff && $this->alreadyNotified($appointment->staff->id, $appointment->id)) {
                $skipped++; // idempotent without flag column: recent reminder exists
                continue;
            }
            if (! class_exists(\App\Notifications\RecordNotification::class)) {
                $skipped++;
                continue;
            }
            $when = optional($appointment->appointment_date)->format('d M Y H:i') ?? 'soon';
            try {
                if ($appointment->staff) {
                    $appointment->staff->notify(new \App\Notifications\RecordNotification(
                        'Appointment reminder',
                        'Appointment with '.trim(($appointment->candidate->first_name ?? '').' '.($appointment->candidate->last_name ?? '')).' at '.$when.'.',
                        ['appointment_id' => $appointment->id, 'reminder' => 'appointment_25h']
                    ));
                }
                if ($hasFlag) {
                    $appointment->forceFill(['reminder_sent_at' => now()])->save();
                }
                $sent++;
            } catch (\Throwable $e) {
                report($e);
                $skipped++;
            }
        }

        $this->info("Appointment reminders: sent {$sent}, skipped {$skipped}.");

        return 0;
    }

    protected function alreadyNotified(int $userId, int $appointmentId): bool
    {
        if (! Schema::hasTable('notifications')) {
            return false;
        }
        $rows = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('created_at', '>=', now()->subHours(25))
            ->pluck('data');
        foreach ($rows as $raw) {
            $data = is_string($raw) ? json_decode($raw, true) : (array) $raw;
            if (($data['appointment_id'] ?? null) == $appointmentId && ($data['reminder'] ?? null) === 'appointment_25h') {
                return true;
            }
        }

        return false;
    }
}
