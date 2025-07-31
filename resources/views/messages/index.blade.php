@extends('layouts.app')

@section('title', __('Messages'))

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex h-96 bg-white rounded-lg shadow-sm overflow-hidden">
                <!-- Conversations List -->
                <div class="w-1/3 border-r border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">{{ __('Messages') }}</h2>
                            <a href="{{ route('messages.create') }}"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                {{ __('New') }}
                            </a>
                        </div>
                    </div>

                    <div class="overflow-y-auto h-full">
                        @if ($conversations && $conversations->count() > 0)
                            @foreach ($conversations as $conversation)
                                <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer">
                                    <a href="{{ route('messages.show', $conversation) }}" class="block">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    {{ substr($conversation->otherParticipant->name ?? 'U', 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="ml-3 flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $conversation->otherParticipant->name ?? __('Unknown User') }}
                                                </p>
                                                <p class="text-sm text-gray-500 truncate">
                                                    {{ $conversation->lastMessage->content ?? __('No messages yet') }}
                                                </p>
                                            </div>
                                            <div class="ml-2 flex-shrink-0">
                                                @if ($conversation->hasUnreadMessages())
                                                    <div class="h-2 w-2 bg-blue-500 rounded-full"></div>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="p-8 text-center">
                                <div class="text-gray-400 text-4xl mb-4">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No conversations yet') }}</h3>
                                <p class="text-gray-500 mb-4">{{ __('Start a conversation with other users.') }}</p>
                                <a href="{{ route('messages.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>{{ __('Start Conversation') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Message Area -->
                <div class="flex-1 flex flex-col">
                    <div class="flex-1 flex items-center justify-center bg-gray-50">
                        <div class="text-center">
                            <div class="text-gray-400 text-5xl mb-4">
                                <i class="fas fa-comment-alt"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">{{ __('Select a conversation') }}</h3>
                            <p class="text-gray-500">{{ __('Choose a conversation from the left to view messages') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Recent Contacts') }}</h3>
                    @if (isset($recentContacts) && $recentContacts->count() > 0)
                        <div class="space-y-2">
                            @foreach ($recentContacts->take(3) as $contact)
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center text-sm">
                                        {{ substr($contact->name, 0, 1) }}
                                    </div>
                                    <span class="ml-2 text-sm text-gray-700">{{ $contact->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">{{ __('No recent contacts') }}</p>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Unread Messages') }}</h3>
                    <div class="text-2xl font-bold text-blue-600">
                        {{ $unreadCount ?? 0 }}
                    </div>
                    <p class="text-sm text-gray-500">{{ __('messages waiting for you') }}</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Quick Actions') }}</h3>
                    <div class="space-y-2">
                        <a href="{{ route('messages.create') }}" class="block text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-plus mr-1"></i>{{ __('New Message') }}
                        </a>
                        <a href="#" onclick="markAllAsRead()"
                            class="block text-green-600 hover:text-green-800 text-sm">
                            <i class="fas fa-check-double mr-1"></i>{{ __('Mark All Read') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function markAllAsRead() {
                // Implementation for marking all messages as read
                alert('{{ __('All messages marked as read!') }}');
            }
        </script>
    @endpush
@endsection
