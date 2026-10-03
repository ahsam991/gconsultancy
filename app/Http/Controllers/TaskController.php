<?php

namespace App\Http\Controllers;

use App\Models\ActionNote;
use App\Models\Appointment;
use App\Models\Candidate;
use App\Models\Communication;
use App\Models\Task;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        abort_if($request->user()->role?->name === 'candidate', 403);
        $query = Task::with(['candidate', 'application', 'assignedUser']);
        if ($request->user()->role?->name === 'staff') {
            $query->where('assigned_user_id', $request->user()->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        $tasks = $query->orderBy('due_date')->paginate(15)->withQueryString();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $candidates = Candidate::orderBy('first_name')->limit(500)->get();
        $users = User::orderBy('name')->get();
        return view('tasks.create', compact('candidates', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'candidate_id' => 'nullable|exists:candidates,id',
            'application_id' => 'nullable|exists:applications,id',
            'assigned_user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'due_date' => 'required|date',
            'notify_to' => 'nullable|string|max:255',
        ]);
        $task = Task::create($data);
        AuditService::log('task.created', null, null, $task->toArray());
        return redirect()->route('tasks.show', $task)->with('status', 'Task created.');
    }

    public function show(Task $task)
    {
        $task->load(['candidate', 'application', 'assignedUser']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $candidates = Candidate::orderBy('first_name')->limit(500)->get();
        $users = User::orderBy('name')->get();
        return view('tasks.edit', compact('task', 'candidates', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'due_date' => 'nullable|date',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);
        $task->update($data);
        AuditService::log('task.updated', null, null, $task->toArray());
        return redirect()->route('tasks.show', $task)->with('status', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        AuditService::log('task.deleted', null);
        return redirect()->route('tasks.index')->with('status', 'Task deleted.');
    }

    public function noteStore(Request $request)
    {
        $data = $request->validate([
            'candidate_id' => 'nullable|exists:candidates,id',
            'application_id' => 'nullable|exists:applications,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
        ]);
        $data['assigned_to'] = $request->user()->id;
        $note = ActionNote::create($data);
        AuditService::log('note.created', null, null, $note->toArray());
        return redirect()->back()->with('status', 'Note added.');
    }

    public function noteDestroy(ActionNote $note)
    {
        $note->delete();
        return redirect()->back()->with('status', 'Note deleted.');
    }

    public function appointmentsIndex(Request $request)
    {
        abort_if($request->user()->role?->name === 'candidate', 403);
        $query = Appointment::with(['candidate', 'staff']);
        if ($request->user()->role?->name === 'staff') {
            $query->where('staff_id', $request->user()->id);
        }
        $appointments = $query->orderBy('appointment_date')->paginate(15);
        return view('appointments.index', compact('appointments'));
    }

    public function appointmentCreate()
    {
        $candidates = Candidate::orderBy('first_name')->limit(500)->get();
        $staff = User::orderBy('name')->get();
        return view('appointments.create', compact('candidates', 'staff'));
    }

    public function appointmentStore(Request $request)
    {
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'staff_id' => 'required|exists:users,id',
            'type' => 'required|string|max:100',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'location' => 'nullable|string|max:500',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        $appointment = Appointment::create($data);
        AuditService::log('appointment.created', null, null, $appointment->toArray());
        return redirect()->route('appointments.show', $appointment)->with('status', 'Appointment created.');
    }

    public function appointmentShow(Appointment $appointment)
    {
        $appointment->load(['candidate', 'staff']);
        return view('appointments.show', compact('appointment'));
    }

    public function appointmentEdit(Appointment $appointment)
    {
        $candidates = Candidate::orderBy('first_name')->limit(500)->get();
        $staff = User::orderBy('name')->get();
        return view('appointments.edit', compact('appointment', 'candidates', 'staff'));
    }

    public function appointmentUpdate(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'type' => 'sometimes|string|max:100',
            'appointment_date' => 'sometimes|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'location' => 'nullable|string|max:500',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        $appointment->update($data);
        return redirect()->route('appointments.show', $appointment)->with('status', 'Appointment updated.');
    }

    public function appointmentDestroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('status', 'Appointment deleted.');
    }

    public function communicationStore(Request $request)
    {
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'application_id' => 'nullable|exists:applications,id',
            'channel' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);
        $data['user_id'] = $request->user()->id;
        $data['direction'] = $request->get('direction', 'outbound');
        $comm = Communication::create($data);
        AuditService::log('communication.created', null, null, $comm->toArray());
        return redirect()->back()->with('status', 'Communication logged.');
    }
}
