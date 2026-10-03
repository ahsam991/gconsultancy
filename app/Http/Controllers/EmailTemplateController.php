<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Services\AuditService;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('name')->paginate(15);
        return view('email_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('email_templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:email_templates,slug',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|string',
        ]);
        $template = EmailTemplate::create($data + ['active' => true]);
        AuditService::log('email_template.created', null, null, $template->toArray());
        return redirect()->route('email-templates.show', $template)->with('status', 'Template created.');
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return view('email_templates.show', ['template' => $emailTemplate]);
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('email_templates.edit', ['template' => $emailTemplate]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:email_templates,slug,'.$emailTemplate->id,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);
        $emailTemplate->update($data);
        AuditService::log('email_template.updated', null, null, $emailTemplate->toArray());
        return redirect()->route('email-templates.show', $emailTemplate)->with('status', 'Template updated.');
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();
        return redirect()->route('email-templates.index')->with('status', 'Template deleted.');
    }
}
