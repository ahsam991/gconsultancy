<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Services\AuditService;
use App\Services\UIDService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        abort_if($request->user()->role?->name === 'candidate', 403);
        $query = Lead::with('source');
        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->get('status')));
        }
        if ($request->filled('source')) {
            $query->whereHas('source', fn ($q) => $q->where('name', $request->get('source')));
        }
        if ($s = $request->get('q')) {
            $query->where(function ($qq) use ($s) {
                $qq->where('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }
        $leads = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('leads.index', compact('leads'));
    }

    public function create()
    {
        $sources = LeadSource::where('active', true)->get();
        return view('leads.create', compact('sources'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:leads,email',
            'phone' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:100',
            'destination' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);
        $lead = Lead::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'source_id' => isset($data['source']) ? LeadSource::firstOrCreate(['name' => $data['source']], ['active' => true])->id : null,
            'preferred_destination' => $data['destination'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'NEW',
        ]);
        AuditService::log('lead.created', null, null, $lead->toArray());
        return redirect()->route('leads.show', $lead)->with('status', 'Lead created.');
    }

    public function show(Lead $lead)
    {
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $sources = LeadSource::where('active', true)->get();
        return view('leads.edit', compact('lead', 'sources'));
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:100',
            'destination' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);
        $payload = [];
        foreach (['first_name', 'last_name', 'phone', 'notes', 'assigned_to'] as $f) {
            if (array_key_exists($f, $data)) $payload[$f] = $data[$f];
        }
        if (isset($data['source'])) $payload['source_id'] = LeadSource::firstOrCreate(['name' => $data['source']], ['active' => true])->id;
        if (isset($data['destination'])) $payload['preferred_destination'] = $data['destination'];
        if (isset($data['status'])) $payload['status'] = strtoupper($data['status']);
        $lead->update($payload);
        if (! empty($payload['assigned_to'])) {
            \App\Services\NotifyService::leadAssigned($lead->fresh());
        }
        AuditService::log('lead.updated', null, null, $lead->toArray());
        return redirect()->route('leads.show', $lead)->with('status', 'Lead updated.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('status', 'Lead deleted.');
    }

    public function convert(Request $request, Lead $lead)
    {
        if (Candidate::where('email', $lead->email)->exists()) {
            return redirect()->back()->withErrors(['email' => 'A candidate with this email already exists.']);
        }
        $candidate = Candidate::create([
            'uid' => UIDService::candidateUid(),
            'first_name' => $lead->first_name,
            'last_name' => $lead->last_name,
            'email' => $lead->email,
            'phone' => $lead->phone ?? ('lead-'.$lead->id.'-'.uniqid()),
            'preferred_destination' => $lead->preferred_destination,
            'status' => 'NEW',
            'created_by' => $request->user()->id,
        ]);
        $lead->update(['status' => 'CONVERTED']);
        AuditService::log('lead.converted', $candidate, $lead->toArray(), $candidate->toArray());
        return redirect()->route('candidates.show', $candidate)->with('status', 'Lead converted to candidate '.$candidate->uid);
    }

    public function enquiryStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'destination' => 'nullable|string|max:100',
            'message' => 'nullable|string',
        ]);
        $parts = preg_split('/\s+/', trim($data['name']), 2);
        Lead::create([
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? '',
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'preferred_destination' => $data['destination'] ?? null,
            'notes' => $data['message'] ?? null,
            'status' => 'NEW',
        ]);
        return redirect()->back()->with('status', 'Enquiry received. We will contact you soon.');
    }
}
