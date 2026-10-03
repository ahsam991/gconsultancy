<?php

namespace App\Http\Controllers;

use App\Mail\TemplateMail;
use App\Models\AutomationLog;
use App\Models\AutomationRule;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class AutomationController extends Controller
{
    protected function blockCandidates(Request $request): void
    {
        abort_if($request->user()?->role?->name === 'candidate', 403);
    }

    public function index(Request $request)
    {
        $this->blockCandidates($request);

        $rules = AutomationRule::withCount('logs')->orderByDesc('created_at')->paginate(15);
        $triggerEvents = ['status_changed', 'document_rejected', 'task_overdue', 'offer_received'];
        $actionTypes = ['create_task', 'notify_staff', 'email_candidate', 'set_followup'];

        return view('automation.rules', compact('rules', 'triggerEvents', 'actionTypes'));
    }

    public function store(Request $request)
    {
        $this->blockCandidates($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string|max:100',
            'trigger_status' => 'nullable|string|max:100',
            'action_type' => 'required|string|max:100',
            'action_config' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $config = $this->parseConfig($data['action_config'] ?? null, $request);

        AutomationRule::create([
            'name' => $data['name'],
            'trigger_event' => $data['trigger_event'],
            'trigger_status' => $data['trigger_status'] ?? null,
            'action_type' => $data['action_type'],
            'action_config' => $config,
            'active' => (bool) ($data['active'] ?? true),
        ]);

        return redirect()->route('automation.rules')->with('status', 'Automation rule created.');
    }

    public function update(Request $request, AutomationRule $rule)
    {
        $this->blockCandidates($request);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'trigger_event' => 'required|string|max:100',
            'trigger_status' => 'nullable|string|max:100',
            'action_type' => 'required|string|max:100',
            'action_config' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $rule->update([
            'name' => $data['name'],
            'trigger_event' => $data['trigger_event'],
            'trigger_status' => $data['trigger_status'] ?? null,
            'action_type' => $data['action_type'],
            'action_config' => $this->parseConfig($data['action_config'] ?? null, $request),
            'active' => (bool) ($data['active'] ?? false),
        ]);

        return redirect()->route('automation.rules')->with('status', 'Automation rule updated.');
    }

    public function destroy(Request $request, AutomationRule $rule)
    {
        $this->blockCandidates($request);

        $rule->delete();

        return redirect()->route('automation.rules')->with('status', 'Automation rule deleted.');
    }

    public function logs(Request $request)
    {
        $this->blockCandidates($request);

        $query = AutomationLog::with('rule')->orderByDesc('created_at');
        if ($request->filled('event')) {
            $query->where('trigger_event', $request->get('event'));
        }
        $logs = $query->paginate(20)->withQueryString();

        return view('automation.logs', compact('logs'));
    }

    protected function parseConfig(?string $raw, Request $request): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }
        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw \Illuminate\Validation\ValidationException::withMessages(['action_config' => 'Action config must be valid JSON.']);
        }

        return $decoded;
    }

    /**
     * Automation engine: evaluate all active rules for an event against a model.
     *
     * Supported triggers: status_changed, document_rejected, task_overdue, offer_received.
     * Supported actions: create_task, notify_staff, email_candidate, set_followup.
     * Every firing writes an automation_logs row.
     */
    public static function evaluateAutomation(string $event, $model): void
    {
        if (! Schema::hasTable('automation_rules') || ! Schema::hasTable('automation_logs')) {
            return;
        }

        $rules = AutomationRule::where('active', true)->where('trigger_event', $event)->get();
        foreach ($rules as $rule) {
            if ($rule->trigger_status && ! self::modelStatusMatches($model, $rule->trigger_status)) {
                continue;
            }
            try {
                $result = self::fireAction($rule, $event, $model);
            } catch (\Throwable $e) {
                report($e);
                $result = 'Failed: '.$e->getMessage();
            }
            AutomationLog::create([
                'automation_rule_id' => $rule->id,
                'trigger_event' => $event,
                'related_type' => is_object($model) ? get_class($model) : null,
                'related_id' => is_object($model) && isset($model->id) ? $model->id : null,
                'result' => mb_substr((string) $result, 0, 2000),
            ]);
        }
    }

    protected static function modelStatusMatches($model, string $status): bool
    {
        if (! is_object($model)) {
            return false;
        }
        foreach (['status', 'verification_status'] as $attr) {
            if (isset($model->{$attr}) && strcasecmp((string) $model->{$attr}, $status) === 0) {
                return true;
            }
        }

        return false;
    }

    protected static function fireAction(AutomationRule $rule, string $event, $model): string
    {
        $config = $rule->action_config ?? [];

        return match ($rule->action_type) {
            'create_task' => self::actionCreateTask($config, $event, $model),
            'notify_staff' => self::actionNotifyStaff($config, $event, $model),
            'email_candidate' => self::actionEmailCandidate($config, $event, $model),
            'set_followup' => self::actionSetFollowup($config, $event, $model),
            default => 'Skipped: unknown action type '.$rule->action_type,
        };
    }

    protected static function resolveCandidateId(array $config, $model): ?int
    {
        if (! empty($config['candidate_id'])) {
            return (int) $config['candidate_id'];
        }
        if (is_object($model)) {
            if (isset($model->candidate_id)) {
                return (int) $model->candidate_id;
            }
            if ($model instanceof \App\Models\Candidate) {
                return (int) $model->id;
            }
        }

        return null;
    }

    protected static function resolveApplicationId(array $config, $model): ?int
    {
        if (! empty($config['application_id'])) {
            return (int) $config['application_id'];
        }
        if (is_object($model)) {
            if (isset($model->application_id)) {
                return (int) $model->application_id;
            }
            if ($model instanceof \App\Models\Application) {
                return (int) $model->id;
            }
        }

        return null;
    }

    protected static function resolveAssigneeId(array $config, $model): ?int
    {
        foreach (['assigned_to', 'assignee_id', 'staff_id'] as $key) {
            if (! empty($config[$key])) {
                return (int) $config[$key];
            }
        }
        if (is_object($model)) {
            foreach (['assigned_to', 'assigned_staff_id'] as $attr) {
                if (! empty($model->{$attr})) {
                    return (int) $model->{$attr};
                }
            }
            if (! empty($model->candidate) && ! empty($model->candidate->assigned_staff_id)) {
                return (int) $model->candidate->assigned_staff_id;
            }
        }

        return null;
    }

    protected static function actionCreateTask(array $config, string $event, $model): string
    {
        $assigneeId = self::resolveAssigneeId($config, $model);
        if (! $assigneeId) {
            return 'Skipped: create_task needs assigned_to (config or model).';
        }
        $dueDays = (int) ($config['due_days'] ?? 2);
        $task = Task::create([
            'title' => $config['title'] ?? ('Automated follow-up ('.$event.')'),
            'description' => $config['description'] ?? ('Created by automation for event '.$event.'.'),
            'candidate_id' => self::resolveCandidateId($config, $model),
            'application_id' => self::resolveApplicationId($config, $model),
            'assigned_to' => $assigneeId,
            'priority' => $config['priority'] ?? 'Medium',
            'status' => 'NEW',
            'due_date' => now()->addDays(max(0, $dueDays))->toDateString(),
        ]);

        return 'Created task #'.$task->id.' assigned to user #'.$assigneeId.'.';
    }

    protected static function actionNotifyStaff(array $config, string $event, $model): string
    {
        if (! class_exists(\App\Notifications\RecordNotification::class)) {
            return 'Skipped: RecordNotification class missing.';
        }
        $assigneeId = self::resolveAssigneeId($config, $model);
        if (! $assigneeId) {
            return 'Skipped: notify_staff found no assigned staff.';
        }
        $user = \App\Models\User::find($assigneeId);
        if (! $user) {
            return 'Skipped: notify_staff user #'.$assigneeId.' not found.';
        }
        $title = $config['title'] ?? ('Automation: '.$event);
        $message = $config['message'] ?? ('Event '.$event.' fired for '.(is_object($model) ? get_class($model).'#'.($model->id ?? '?') : 'record').'.');
        $user->notify(new \App\Notifications\RecordNotification($title, $message, [
            'event' => $event,
            'related_type' => is_object($model) ? get_class($model) : null,
            'related_id' => is_object($model) && isset($model->id) ? $model->id : null,
        ]));

        return 'Notified user #'.$assigneeId.'.';
    }

    protected static function actionEmailCandidate(array $config, string $event, $model): string
    {
        $email = $config['email'] ?? null;
        if (! $email && is_object($model)) {
            $email = $model->email
                ?? $model->candidate->email
                ?? null;
        }
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Skipped: email_candidate found no valid candidate email.';
        }
        $subject = $config['subject'] ?? ('Update on your application ('.$event.')');
        $body = $config['body'] ?? 'There is an update on your application. Please log in to your portal for details.';

        // Prefer NotifyService when it exposes a generic mail helper, else queue directly.
        if (class_exists(\App\Services\NotifyService::class)) {
            foreach (['sendMail', 'send', 'mail', 'emailCandidate', 'generic'] as $method) {
                if (method_exists(\App\Services\NotifyService::class, $method)) {
                    try {
                        \App\Services\NotifyService::$method($email, $subject, $body);
                    } catch (\Throwable $e) {
                        report($e);
                        Mail::to($email)->queue(new TemplateMail($subject, nl2br(e($body))));

                        return 'Queued via NotifyService fallback to Mail for '.$email.'.';
                    }

                    return 'Sent via NotifyService::'.$method.' to '.$email.'.';
                }
            }
        }

        Mail::to($email)->queue(new TemplateMail($subject, nl2br(e($body))));

        return 'Queued email to '.$email.'.';
    }

    protected static function actionSetFollowup(array $config, string $event, $model): string
    {
        $assigneeId = self::resolveAssigneeId($config, $model);
        if (! $assigneeId) {
            return 'Skipped: set_followup needs assigned_to (config or model).';
        }
        $task = Task::create([
            'title' => $config['title'] ?? ('Follow up ('.$event.')'),
            'description' => $config['description'] ?? ('Automatic follow-up for event '.$event.'.'),
            'candidate_id' => self::resolveCandidateId($config, $model),
            'application_id' => self::resolveApplicationId($config, $model),
            'assigned_to' => $assigneeId,
            'priority' => $config['priority'] ?? 'Medium',
            'status' => 'NEW',
            'due_date' => now()->addDays(2)->toDateString(),
        ]);

        return 'Created follow-up task #'.$task->id.' due in 2 days.';
    }
}
