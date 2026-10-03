<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected function isCandidate(Request $request): bool
    {
        return $request->user()?->role?->name === 'candidate';
    }

    protected function ownCandidate(Request $request): ?Candidate
    {
        return Candidate::where('user_id', $request->user()->id)->first();
    }

    /**
     * Thread list. Staff see all candidates with unread counts;
     * candidates see only their own thread.
     */
    public function index(Request $request)
    {
        if ($this->isCandidate($request)) {
            $candidate = $this->ownCandidate($request);
            if (! $candidate) {
                abort(404, 'Candidate profile not found.');
            }

            return redirect()->route('messages.show', $candidate);
        }

        abort_if($request->user()?->role?->name === 'candidate', 403);

        $candidates = Candidate::withCount(['messages as unread_count' => function ($q) use ($request) {
            $q->whereNull('read_at')->where('sender_id', '!=', $request->user()->id);
        }])->with(['messages' => function ($q) {
            $q->latest()->limit(1);
        }])->orderByDesc('updated_at')->paginate(15);

        return view('messages.index', compact('candidates'));
    }

    /**
     * Thread with a candidate. Staff see internal + visible notes,
     * candidates see visible messages only. Marks incoming as read.
     */
    public function show(Request $request, Candidate $candidate)
    {
        if ($this->isCandidate($request)) {
            $own = $this->ownCandidate($request);
            if (! $own || $own->id !== $candidate->id) {
                abort(403);
            }
            $messages = Message::where('candidate_id', $candidate->id)
                ->where('is_internal', false)
                ->with('sender')
                ->orderBy('created_at')
                ->get();
            Message::where('candidate_id', $candidate->id)
                ->where('is_internal', false)
                ->whereNull('read_at')
                ->where('sender_id', '!=', $request->user()->id)
                ->update(['read_at' => now()]);

            return view('messages.show', compact('candidate', 'messages'));
        }

        abort_if($request->user()?->role?->name === 'candidate', 403);

        $messages = Message::where('candidate_id', $candidate->id)
            ->with('sender')
            ->orderBy('created_at')
            ->get();
        Message::where('candidate_id', $candidate->id)
            ->whereNull('read_at')
            ->where('sender_id', '!=', $request->user()->id)
            ->update(['read_at' => now()]);

        return view('messages.show', compact('candidate', 'messages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'body' => 'required|string|max:5000',
            'is_internal' => 'nullable|boolean',
        ]);

        $isCandidate = $this->isCandidate($request);

        if ($isCandidate) {
            $own = $this->ownCandidate($request);
            if (! $own || (int) $data['candidate_id'] !== $own->id) {
                abort(403);
            }
            // Candidates can never post internal notes.
            $data['is_internal'] = false;
        } else {
            abort_if($request->user()?->role?->name === 'candidate', 403);
            $data['is_internal'] = (bool) ($data['is_internal'] ?? false);
        }

        $message = Message::create([
            'candidate_id' => $data['candidate_id'],
            'sender_id' => $request->user()->id,
            'body' => $data['body'],
            'is_internal' => $data['is_internal'],
        ]);

        if ($isCandidate) {
            return redirect()->route('messages.show', $message->candidate_id)->with('status', 'Message sent.');
        }

        return redirect()->route('messages.show', $message->candidate_id)->with('status', 'Message sent.');
    }
}
