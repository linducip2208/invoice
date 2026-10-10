<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())->latest()->paginate(15);
        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'  => 'required|string|max:255',
            'priority' => 'required|in:low,normal,high,urgent',
            'message'  => 'required|string|max:5000',
        ]);

        $ticket = SupportTicket::create([
            'workspace_id' => workspace()?->id,
            'user_id'      => auth()->id(),
            'subject'      => $validated['subject'],
            'priority'     => $validated['priority'],
            'status'       => 'open',
        ]);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'body'      => $validated['message'],
        ]);

        return redirect()->route('support.show', $ticket)->with('success', 'Ticket dibuat.');
    }

    public function show(SupportTicket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id() || auth()->user()->isSuperadmin(), 403);
        $ticket->load('messages.user');
        return view('support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id() || auth()->user()->isSuperadmin(), 403);
        $validated = $request->validate(['body' => 'required|string|max:5000']);

        SupportMessage::create([
            'ticket_id'      => $ticket->id,
            'user_id'        => auth()->id(),
            'is_admin_reply' => auth()->user()->isSuperadmin(),
            'body'           => $validated['body'],
        ]);

        if ($ticket->status === 'resolved') $ticket->update(['status' => 'open']);
        return back()->with('success', 'Reply sent.');
    }
}
