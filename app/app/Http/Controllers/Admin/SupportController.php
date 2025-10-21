<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupportController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $query = Ticket::with('client');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'client_name' => $ticket->client ? $ticket->client->name : 'N/A',
                    'created_at' => $ticket->created_at->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Admin/Support/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['search', 'status', 'priority']),
        ]);
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $clients = Client::select('id', 'first_name', 'last_name', 'company_name', 'email')
            ->orderBy('first_name')
            ->get()
            ->map(function ($client) {
                $name = trim($client->first_name . ' ' . $client->last_name);
                if ($client->company_name) {
                    $name = $client->company_name . ' (' . $name . ')';
                }
                return [
                    'id' => $client->id,
                    'name' => $name,
                    'email' => $client->email,
                ];
            });

        return Inertia::render('Admin/Support/Create', [
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,urgent',
            'department' => 'nullable|string',
            'message' => 'required|string',
        ]);

        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        $ticketNumber = 'TKT-' . str_pad(($lastTicket ? $lastTicket->id + 1 : 1), 6, '0', STR_PAD_LEFT);

        $ticket = Ticket::create([
            'client_id' => $validated['client_id'],
            'ticket_number' => $ticketNumber,
            'subject' => $validated['subject'],
            'status' => 'open',
            'priority' => $validated['priority'],
            'department' => $validated['department'] ?? 'General',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_admin' => true,
        ]);

        return redirect()->route('managit.support.show', $ticket->id)
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['client', 'messages.user']);

        return Inertia::render('Admin/Support/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'department' => $ticket->department ?? 'General',
                'created_at' => $ticket->created_at->format('Y-m-d H:i'),
                'client' => $ticket->client ? [
                    'id' => $ticket->client->id,
                    'name' => $ticket->client->name,
                    'email' => $ticket->client->email,
                ] : null,
            ],
            'messages' => $ticket->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'is_admin' => $message->is_admin,
                    'user_name' => $message->user ? $message->user->name : 'System',
                    'created_at' => $message->created_at->format('Y-m-d H:i'),
                ];
            }),
        ]);
    }

    /**
     * Store a new ticket message.
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_admin' => true,
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,waiting,closed',
        ]);

        $ticket->update(['status' => $validated['status']]);

        return back()->with('success', 'Ticket status updated.');
    }
}
