<?php

namespace App\Services;

use App\Mail\TemplateMail;
use App\Models\Application;
use App\Models\CandidateDocument;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Notifications\RecordNotification;
use Illuminate\Support\Facades\Mail;

class NotifyService
{
    protected static function render(string $slug, array $vars, array $fallback): array
    {
        $tpl = EmailTemplate::where('slug', $slug)->where('active', true)->first();
        $subject = $tpl->subject ?? $fallback['subject'];
        $body = $tpl->body ?? $fallback['body'];
        foreach ($vars as $k => $v) {
            $subject = str_replace('{{'.$k.'}}', (string) $v, $subject);
            $body = str_replace('{{'.$k.'}}', e((string) $v), $body);
        }
        return [$subject, nl2br($body)];
    }

    protected static function mail(string $email, string $slug, array $vars, array $fallback): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        [$subject, $html] = self::render($slug, $vars, $fallback);
        try {
            Mail::to($email)->queue(new TemplateMail($subject, $html));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public static function applicationStatusChanged(Application $app): void
    {
        $app->loadMissing(['candidate', 'university', 'course', 'assignedStaff']);
        $c = $app->candidate;
        $vars = [
            'name' => trim(($c->first_name ?? '').' '.($c->last_name ?? '')),
            'app_uid' => $app->uid,
            'status' => ucwords(strtolower(str_replace('_', ' ', $app->status))),
            'course' => $app->course->name ?? '',
            'university' => $app->university->name ?? '',
        ];
        self::mail($c->email ?? '', 'application-status', $vars, [
            'subject' => "Your application {$app->uid} status: {$app->status}",
            'body' => "Hi {$vars['name']}, your application {$app->uid} moved to {$app->status}.",
        ]);
        foreach (array_filter([$app->assignedStaff, $c->user ?? null]) as $user) {
            try {
                $user->notify(new RecordNotification(
                    'Status changed: '.$app->uid,
                    trim(($c->first_name ?? '').' '.($c->last_name ?? '')).' → '.$app->status,
                    ['application_id' => $app->id]
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    public static function leadAssigned(Lead $lead): void
    {
        $staff = $lead->assignee;
        if (! $staff) {
            return;
        }
        try {
            $staff->notify(new RecordNotification(
                'New lead assigned',
                trim($lead->first_name.' '.$lead->last_name).' ('.$lead->email.')',
                ['lead_id' => $lead->id]
            ));
        } catch (\Throwable $e) {
            report($e);
        }
        self::mail($staff->email ?? '', 'lead-assigned', [
            'name' => trim($lead->first_name.' '.$lead->last_name),
            'email' => $lead->email ?? '',
        ], [
            'subject' => 'New lead assigned',
            'body' => 'A new lead was assigned to you.',
        ]);
    }

    public static function documentVerified(CandidateDocument $doc): void
    {
        $doc->loadMissing(['candidate', 'documentType']);
        $c = $doc->candidate;
        if (! $c) {
            return;
        }
        self::mail($c->email ?? '', 'document-verified', [
            'name' => trim(($c->first_name ?? '').' '.($c->last_name ?? '')),
            'document' => $doc->documentType->name ?? $doc->original_filename,
        ], [
            'subject' => 'Your document was verified',
            'body' => 'One of your documents has been verified.',
        ]);
    }
}
