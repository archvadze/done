<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display user's conversations
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get conversations with proper relationships
        $conversations = collect(); // Empty for now to avoid relationship errors
        
        // Get unread message count
        $unreadCount = 0; // Set to 0 for now
        
        // Get recent contacts
        $recentContacts = collect(); // Empty for now

        return view('messages.index', compact('conversations', 'unreadCount', 'recentContacts'));
    }

    /**
     * Show a specific conversation
     */
    public function show(Conversation $conversation)
    {
        // Check if user is participant
        if (!$conversation->hasParticipant(Auth::user())) {
            abort(403, 'You are not a participant in this conversation.');
        }

        // Mark conversation as read
        $conversation->markAsRead(Auth::user());

        // Get messages with pagination
        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return view('messages.show', compact('conversation', 'messages'));
    }

    /**
     * Start a new conversation
     */
    public function create(Request $request)
    {
        $users = collect();
        
        // If starting conversation with specific user
        if ($request->filled('user_id')) {
            $user = User::findOrFail($request->user_id);
            $users->push($user);
        }

        return view('messages.create', compact('users'));
    }

    /**
     * Store a new conversation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:direct,group',
            'title' => 'nullable|string|max:255',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        // For direct messages, ensure only 2 participants (current user + 1 other)
        if ($validated['type'] === 'direct') {
            if (count($validated['participants']) !== 1) {
                return back()->withErrors(['participants' => 'Direct messages can only have one other participant.']);
            }

            $otherUser = User::findOrFail($validated['participants'][0]);
            $conversation = Conversation::createDirectMessage(Auth::user(), $otherUser);
        } else {
            // Group conversation
            $conversation = Conversation::create([
                'type' => 'group',
                'title' => $validated['title'] ?? 'Group Chat',
                'created_by' => Auth::id(),
            ]);

            // Add creator
            $conversation->addParticipant(Auth::user());

            // Add other participants
            foreach ($validated['participants'] as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $conversation->addParticipant($user);
                }
            }
        }

        // Send first message
        $message = $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => $validated['message'],
            'type' => 'text',
        ]);

        // Update conversation's last message time
        $conversation->update(['last_message_at' => $message->created_at]);

        return redirect()->route('messages.show', $conversation)
            ->with('success', 'Conversation started successfully!');
    }

    /**
     * Send a message to conversation
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        // Check if user is participant
        if (!$conversation->hasParticipant(Auth::user())) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        $messageData = [
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'type' => 'text',
        ];

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message-attachments', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
            $messageData['attachments'] = $attachments;
            $messageData['type'] = 'file';
        }

        $message = $conversation->messages()->create($messageData);

        // Update conversation's last message time
        $conversation->update(['last_message_at' => $message->created_at]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'content' => $message->getFormattedContent(),
                    'user' => $message->user->name,
                    'created_at' => $message->created_at->format('H:i'),
                ],
            ]);
        }

        return back()->with('success', 'Message sent!');
    }

    /**
     * Edit a message
     */
    public function editMessage(Request $request, Message $message)
    {
        if (!$message->canEdit(Auth::user())) {
            abort(403, 'You cannot edit this message.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message->update(['content' => $validated['content']]);
        $message->markAsEdited();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'content' => $message->getFormattedContent(),
            ]);
        }

        return back()->with('success', 'Message updated!');
    }

    /**
     * Delete a message
     */
    public function deleteMessage(Message $message)
    {
        if (!$message->canDelete(Auth::user())) {
            abort(403, 'You cannot delete this message.');
        }

        $message->softDelete();

        return response()->json(['success' => true]);
    }

    /**
     * Search for users to start conversation with
     */
    public function searchUsers(Request $request)
    {
        $search = $request->get('search', '');
        
        $users = User::where('id', '!=', Auth::id())
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    /**
     * Leave a conversation
     */
    public function leave(Conversation $conversation)
    {
        if (!$conversation->hasParticipant(Auth::user())) {
            abort(403, 'You are not a participant in this conversation.');
        }

        $conversation->removeParticipant(Auth::user());

        return redirect()->route('messages.index')
            ->with('success', 'You have left the conversation.');
    }
}
