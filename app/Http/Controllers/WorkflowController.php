<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\StatusTransition;
use App\Models\WorkflowTemplate;
use App\Services\AuditService;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    protected function ensureAdmin(): void
    {
        if (auth()->user()?->role?->name !== 'admin') {
            abort(403, 'Admin access only.');
        }
    }

    // ---- Templates ----

    public function templates()
    {
        $this->ensureAdmin();
        $templates = WorkflowTemplate::withCount('transitions')->orderBy('name')->paginate(15);

        return view('workflow.templates', compact('templates'));
    }

    public function templateStore(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:workflow_templates,name',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active', true);
        $template = WorkflowTemplate::create($data);
        AuditService::log('workflow.template.created', null, null, $template->toArray());

        return redirect()->route('workflow.templates')->with('status', 'Workflow template created.');
    }

    public function templateUpdate(Request $request, WorkflowTemplate $template)
    {
        $this->ensureAdmin();
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:workflow_templates,name,'.$template->id,
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');
        $template->update($data);
        AuditService::log('workflow.template.updated', null, null, $template->toArray());

        return redirect()->route('workflow.templates')->with('status', 'Workflow template updated.');
    }

    public function templateDestroy(WorkflowTemplate $template)
    {
        $this->ensureAdmin();
        $template->delete();
        AuditService::log('workflow.template.deleted', null, null, ['id' => $template->id]);

        return redirect()->route('workflow.templates')->with('status', 'Workflow template deleted.');
    }

    // ---- Transitions ----

    public function transitions(Request $request)
    {
        $this->ensureAdmin();
        $query = StatusTransition::with('template')->orderByDesc('created_at');
        if ($request->filled('workflow_template_id')) {
            $query->where('workflow_template_id', $request->integer('workflow_template_id'));
        }
        $transitions = $query->paginate(15)->withQueryString();
        $templates = WorkflowTemplate::orderBy('name')->get();

        return view('workflow.transitions', compact('transitions', 'templates'));
    }

    protected function transitionRules(?StatusTransition $transition = null): array
    {
        return [
            'workflow_template_id' => 'required|exists:workflow_templates,id',
            'from_status' => 'required|string|max:255',
            'to_status' => 'required|string|max:255',
            'required_permission' => 'nullable|string|max:255',
            'automation' => 'nullable|string',
            'active' => 'nullable|boolean',
        ];
    }

    protected function decodeAutomation(?string $raw): ?array
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }
        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            abort(422, 'Automation must be valid JSON.');
        }

        return $decoded;
    }

    public function transitionStore(Request $request)
    {
        $this->ensureAdmin();
        $data = $request->validate($this->transitionRules());
        $transition = StatusTransition::create([
            'workflow_template_id' => $data['workflow_template_id'],
            'from_status' => $data['from_status'],
            'to_status' => $data['to_status'],
            'required_permission' => $data['required_permission'] ?? null,
            'automation' => $this->decodeAutomation($data['automation'] ?? null),
            'active' => $request->boolean('active', true),
        ]);
        AuditService::log('workflow.transition.created', null, null, $transition->toArray());

        return redirect()->route('workflow.transitions')->with('status', 'Status transition created.');
    }

    public function transitionUpdate(Request $request, StatusTransition $transition)
    {
        $this->ensureAdmin();
        $data = $request->validate($this->transitionRules($transition));
        $transition->update([
            'workflow_template_id' => $data['workflow_template_id'],
            'from_status' => $data['from_status'],
            'to_status' => $data['to_status'],
            'required_permission' => $data['required_permission'] ?? null,
            'automation' => $this->decodeAutomation($data['automation'] ?? null),
            'active' => $request->boolean('active'),
        ]);
        AuditService::log('workflow.transition.updated', null, null, $transition->toArray());

        return redirect()->route('workflow.transitions')->with('status', 'Status transition updated.');
    }

    public function transitionDestroy(StatusTransition $transition)
    {
        $this->ensureAdmin();
        $transition->delete();
        AuditService::log('workflow.transition.deleted', null, null, ['id' => $transition->id]);

        return redirect()->route('workflow.transitions')->with('status', 'Status transition deleted.');
    }

    // ---- Custom fields ----

    public function fields(Request $request)
    {
        $this->ensureAdmin();
        $fieldQuery = CustomField::orderBy('module')->orderBy('sort_order');
        if ($request->filled('module')) {
            $fieldQuery->where('module', $request->string('module'));
        }
        $fields = $fieldQuery->paginate(15)->withQueryString();

        $valueQuery = CustomFieldValue::with('customField')->orderByDesc('created_at');
        if ($request->filled('module')) {
            $module = $request->string('module')->toString();
            $valueQuery->whereHas('customField', fn ($q) => $q->where('module', $module));
        }
        $values = $valueQuery->paginate(15, ['*'], 'values_page')->withQueryString();
        $modules = CustomField::select('module')->distinct()->orderBy('module')->pluck('module');

        return view('workflow.fields', compact('fields', 'values', 'modules'));
    }

    protected function fieldPayload(Request $request): array
    {
        $data = $request->validate([
            'module' => 'required|string|max:100',
            'name' => 'required|string|max:100',
            'label' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'options' => 'nullable|string',
            'required' => 'nullable|boolean',
            'sort' => 'nullable|integer|min:0|max:9999',
            'active' => 'nullable|boolean',
        ]);

        $optionsRaw = trim((string) ($data['options'] ?? ''));
        $options = null;
        if ($optionsRaw !== '') {
            $decoded = json_decode($optionsRaw, true);
            $options = json_last_error() === JSON_ERROR_NONE
                ? $decoded
                : array_values(array_filter(array_map('trim', explode(',', $optionsRaw))));
        }

        return [
            'module' => $data['module'],
            'name' => $data['name'],
            'label' => $data['label'],
            'field_type' => $data['type'],
            'options' => $options,
            'is_required' => $request->boolean('required'),
            'sort_order' => $data['sort'] ?? 0,
            'active' => $request->boolean('active', true),
        ];
    }

    public function fieldStore(Request $request)
    {
        $this->ensureAdmin();
        $field = CustomField::create($this->fieldPayload($request));
        AuditService::log('workflow.field.created', null, null, $field->toArray());

        return redirect()->route('workflow.fields')->with('status', 'Custom field created.');
    }

    public function fieldUpdate(Request $request, CustomField $field)
    {
        $this->ensureAdmin();
        $field->update($this->fieldPayload($request));
        AuditService::log('workflow.field.updated', null, null, $field->toArray());

        return redirect()->route('workflow.fields')->with('status', 'Custom field updated.');
    }

    public function fieldDestroy(CustomField $field)
    {
        $this->ensureAdmin();
        $field->delete();
        AuditService::log('workflow.field.deleted', null, null, ['id' => $field->id]);

        return redirect()->route('workflow.fields')->with('status', 'Custom field deleted.');
    }
}
