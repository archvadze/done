@extends('layouts.app')

@section('title', __('Support Ticket') . ' #' . $ticket->id)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Navigation -->
            <div class="mb-6">
                <a href="{{ route('support.tickets.index') }}" class="text-blue-500 hover:text-blue-600 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to My Tickets') }}
                </a>
            </div>

            <!-- Ticket Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                #{{ $ticket->id }} - {{ $ticket->subject }}
                            </h1>

                            <!-- Status Badge -->
                            @php
                                $statusClass = match ($ticket->status) {
                                    'open' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-400',
                                    'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-400',
                                    'waiting'
                                        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-400',
                                    'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                                @switch($ticket->status)
                                    @case('open')
                                        <i class="fas fa-circle-dot mr-1"></i>{{ __('Open') }}
                                    @break

                                    @case('in_progress')
                                        <i class="fas fa-cog mr-1"></i>{{ __('In Progress') }}
                                    @break

                                    @case('waiting')
                                        <i class="fas fa-clock mr-1"></i>{{ __('Waiting for Response') }}
                                    @break

                                    @case('closed')
                                        <i class="fas fa-check-circle mr-1"></i>{{ __('Closed') }}
                                    @break

                                    @default
                                        {{ ucfirst($ticket->status) }}
                                @endswitch
                            </span>

                            <!-- Priority Badge -->
                            @php
                                $priorityClass = match ($ticket->priority) {
                                    'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                    'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
                                    'normal' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                    'low' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                };
                            @endphp
                            <span
                                class="inline-flex items-center px-2 py-1 rounded text-sm font-medium {{ $priorityClass }}">
                                {{ ucfirst($ticket->priority) }} {{ __('Priority') }}
                            </span>
                        </div>

                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4 mb-4">
                            <span>
                                <i class="fas fa-tag mr-1"></i>{{ ucfirst($ticket->category) }}
                            </span>
                            <span>
                                <i class="fas fa-calendar mr-1"></i>{{ __('Created') }}
                                {{ $ticket->created_at->format('M j, Y \a\t g:i A') }}
                            </span>
                            @if ($ticket->assignedTo)
                                <span>
                                    <i class="fas fa-user mr-1"></i>{{ __('Assigned to') }}
                                    {{ $ticket->assignedTo->name }}
                                </span>
                            @endif
                            @if ($ticket->updated_at->gt($ticket->created_at))
                                <span>
                                    <i class="fas fa-clock mr-1"></i>{{ __('Last updated') }}
                                    {{ $ticket->updated_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        @if ($ticket->status !== 'closed')
                            <form action="{{ route('support.tickets.close', $ticket) }}" method="POST"
                                class="inline-block">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('{{ __('Are you sure you want to close this ticket?') }}')"
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                                    <i class="fas fa-times mr-1"></i>{{ __('Close Ticket') }}
                                </button>
                            </form>
                        @else
                            <form action="{{ route('support.tickets.reopen', $ticket) }}" method="POST"
                                class="inline-block">
                                @csrf
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm">
                                    <i class="fas fa-undo mr-1"></i>{{ __('Reopen Ticket') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ticket Content and Replies -->
            <div class="space-y-6">
                <!-- Original Message -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-4">
                            <img src="{{ $ticket->user->avatar ?? asset('images/default-avatar.png') }}"
                                alt="{{ $ticket->user->name }}" class="w-10 h-10 rounded-full mr-3">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $ticket->user->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('Ticket Creator') }}</div>
                            </div>
                            <div class="ml-auto text-sm text-gray-500 dark:text-gray-400">
                                {{ $ticket->created_at->format('M j, Y \a\t g:i A') }}
                            </div>
                        </div>

                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($ticket->description)) !!}
                        </div>

                        <!-- System Information -->
                        @if ($ticket->browser || $ticket->operating_system)
                            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('System Information') }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    @if ($ticket->browser)
                                        <div><strong>{{ __('Browser') }}:</strong> {{ $ticket->browser }}</div>
                                    @endif
                                    @if ($ticket->operating_system)
                                        <div><strong>{{ __('OS') }}:</strong> {{ $ticket->operating_system }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Bug Report Details -->
                        @if ($ticket->category === 'bug' && ($ticket->steps || $ticket->expected_behavior || $ticket->actual_behavior))
                            <div class="mt-4 p-3 bg-red-50 dark:bg-red-900 rounded-lg">
                                <h4 class="text-sm font-medium text-red-700 dark:text-red-300 mb-2">
                                    {{ __('Bug Report Details') }}</h4>
                                @if ($ticket->steps)
                                    <div class="mb-2">
                                        <strong
                                            class="text-red-700 dark:text-red-300">{{ __('Steps to Reproduce') }}:</strong>
                                        <div class="text-sm text-red-600 dark:text-red-400 mt-1">{!! nl2br(e($ticket->steps)) !!}
                                        </div>
                                    </div>
                                @endif
                                @if ($ticket->expected_behavior || $ticket->actual_behavior)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        @if ($ticket->expected_behavior)
                                            <div>
                                                <strong
                                                    class="text-red-700 dark:text-red-300">{{ __('Expected') }}:</strong>
                                                <div class="text-red-600 dark:text-red-400 mt-1">{!! nl2br(e($ticket->expected_behavior)) !!}
                                                </div>
                                            </div>
                                        @endif
                                        @if ($ticket->actual_behavior)
                                            <div>
                                                <strong
                                                    class="text-red-700 dark:text-red-300">{{ __('Actual') }}:</strong>
                                                <div class="text-red-600 dark:text-red-400 mt-1">{!! nl2br(e($ticket->actual_behavior)) !!}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Attachments -->
                        @if ($ticket->attachments && count($ticket->attachments) > 0)
                            <div class="mt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Attachments') }}</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($ticket->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                            <i class="fas fa-paperclip mr-1"></i>
                                            {{ basename($attachment) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Replies -->
                @if ($ticket->replies && $ticket->replies->count() > 0)
                    @foreach ($ticket->replies as $reply)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center mb-4">
                                    <img src="{{ $reply->user->avatar ?? asset('images/default-avatar.png') }}"
                                        alt="{{ $reply->user->name }}" class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $reply->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if ($reply->user->id === $ticket->user_id)
                                                {{ __('Ticket Creator') }}
                                            @elseif($reply->is_staff)
                                                {{ __('Support Staff') }}
                                            @else
                                                {{ __('User') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-auto text-sm text-gray-500 dark:text-gray-400">
                                        {{ $reply->created_at->format('M j, Y \a\t g:i A') }}
                                    </div>
                                </div>

                                <div class="prose dark:prose-invert max-w-none">
                                    {!! nl2br(e($reply->message)) !!}
                                </div>

                                @if ($reply->attachments && count($reply->attachments) > 0)
                                    <div class="mt-4">
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('Attachments') }}</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($reply->attachments as $attachment)
                                                <a href="{{ asset('storage/' . $attachment) }}" target="_blank"
                                                    class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                    <i class="fas fa-paperclip mr-1"></i>
                                                    {{ basename($attachment) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- Reply Form -->
                @if ($ticket->status !== 'closed')
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Add Reply') }}</h3>

                        <form action="{{ route('support.tickets.reply', $ticket) }}" method="POST"
                            enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label for="message"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Your Message') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea name="message" id="message" rows="6" required placeholder="{{ __('Type your reply here...') }}"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reply_attachments"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('Attachments') }}
                                </label>
                                <input type="file" name="attachments[]" id="reply_attachments" multiple
                                    accept="image/*,.pdf,.doc,.docx,.txt,.log"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('attachments') border-red-500 @enderror">
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ __('Max 5MB per file, up to 3 files.') }}</p>
                                @error('attachments')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-4">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                    <i class="fas fa-reply mr-2"></i>{{ __('Send Reply') }}
                                </button>
                                <button type="button" onclick="document.getElementById('message').value = ''"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                    {{ __('Clear') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 text-center">
                        <div class="text-gray-400 text-4xl mb-4">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Ticket Closed') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ __('This ticket has been closed. You cannot add new replies.') }}</p>
                        <form action="{{ route('support.tickets.reopen', $ticket) }}" method="POST"
                            class="inline-block">
                            @csrf
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <i class="fas fa-undo mr-2"></i>{{ __('Reopen Ticket') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
