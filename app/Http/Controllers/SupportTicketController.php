<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    /**
     * Display user's support tickets
     */
    public function index(Request $request)
    {
        $query = Auth::user()->supportTickets()
            ->with(['assignedTo'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'open') {
                $query->open();
            } elseif ($request->status === 'closed') {
                $query->closed();
            } else {
                $query->where('status', $request->status);
            }
        }

        $tickets = $query->paginate(10);

        return view('support.tickets.index', compact('tickets'));
    }

    /**
     * Show form for creating a new ticket
     */
    public function create()
    {
        return view('support.tickets.create');
    }

    /**
     * Store a new support ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'required|in:technical,billing,account,content,feature_request,bug_report,other',
            'priority' => 'required|in:low,normal,high,urgent',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        $ticketData = [
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
        ];

        $ticket = SupportTicket::create($ticketData);

        // Handle file attachments if any
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }

            // Add initial reply with attachments
            $ticket->addReply(Auth::user(), 'Attachments provided with initial ticket.', false, $attachments);
        }

        return redirect()->route('support.tickets.show', $ticket)
            ->with('success', 'Support ticket created successfully! Ticket #' . $ticket->ticket_number);
    }

    /**
     * Display a specific ticket
     */
    public function show(SupportTicket $ticket)
    {
        // Check if user can view this ticket
        if ($ticket->user_id !== Auth::id() && !Auth::user()->canManageSupport()) {
            abort(403, 'You can only view your own tickets.');
        }

        $ticket->load(['user', 'assignedTo', 'publicReplies.user']);

        return view('support.tickets.show', compact('ticket'));
    }

    /**
     * Add a reply to a ticket
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        // Check if user can reply to this ticket
        if ($ticket->user_id !== Auth::id() && !Auth::user()->canManageSupport()) {
            abort(403, 'You can only reply to your own tickets.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240',
            'is_internal' => 'boolean',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        $isInternal = $validated['is_internal'] ?? false;
        
        // Only staff can make internal replies
        if ($isInternal && !Auth::user()->canManageSupport()) {
            $isInternal = false;
        }

        $ticket->addReply(Auth::user(), $validated['message'], $isInternal, $attachments);

        return back()->with('success', 'Reply added successfully!');
    }

    /**
     * Close a ticket
     */
    public function close(SupportTicket $ticket)
    {
        // Check if user can close this ticket
        if ($ticket->user_id !== Auth::id() && !Auth::user()->canManageSupport()) {
            abort(403, 'You can only close your own tickets.');
        }

        $ticket->markAsClosed();

        // Add system message
        $ticket->addReply(
            Auth::user(),
            'Ticket has been closed.',
            false,
            []
        )->update(['is_system_message' => true]);

        return back()->with('success', 'Ticket closed successfully!');
    }

    /**
     * Reopen a ticket
     */
    public function reopen(SupportTicket $ticket)
    {
        // Check if user can reopen this ticket
        if ($ticket->user_id !== Auth::id() && !Auth::user()->canManageSupport()) {
            abort(403, 'You can only reopen your own tickets.');
        }

        $ticket->update(['status' => 'open']);

        // Add system message
        $ticket->addReply(
            Auth::user(),
            'Ticket has been reopened.',
            false,
            []
        )->update(['is_system_message' => true]);

        return back()->with('success', 'Ticket reopened successfully!');
    }
}
