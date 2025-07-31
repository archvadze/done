@extends('layouts.app')

@section('title', __('My Support Tickets'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('My Support Tickets') }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('Track and manage your support requests') }}</p>
                </div>
                <a href="{{ route('support.tickets.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>{{ __('New Ticket') }}
                </a>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center space-x-2">
                    <label for="status-filter" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}:</label>
                    <select id="status-filter" 
                            onchange="filterTickets()"
                            class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">{{ __('All') }}</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('Open') }}</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                        <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>{{ __('Waiting for Response') }}</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                    </select>
                </div>
                <a href="{{ route('support.index') }}" class="text-blue-500 hover:text-blue-600 text-sm font-medium">
                    <i class="fas fa-arrow-left mr-1"></i>{{ __('Back to Support') }}
                </a>
            </div>
        </div>

        @if($tickets->count() > 0)
            <!-- Tickets List -->
            <div class="space-y-4">
                @foreach($tickets as $ticket)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-l-4 {{ 
                        $ticket->status === 'closed' ? 'border-gray-400' : 
                        ($ticket->priority === 'urgent' ? 'border-red-500' : 
                        ($ticket->priority === 'high' ? 'border-orange-500' : 
                        ($ticket->priority === 'normal' ? 'border-blue-500' : 'border-green-500'))) 
                    }} hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            <a href="{{ route('support.tickets.show', $ticket) }}" class="hover:text-blue-600 dark:hover:text-blue-400">
                                                #{{ $ticket->id }} - {{ $ticket->subject }}
                                            </a>
                                        </h3>
                                        
                                        <!-- Status Badge -->
                                        @php
                                            $statusClass = match($ticket->status) {
                                                'open' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-400',
                                                'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-400',
                                                'waiting' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-400',
                                                'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            @switch($ticket->status)
                                                @case('open')
                                                    <i class="fas fa-circle-dot mr-1"></i>{{ __('Open') }}
                                                    @break
                                                @case('in_progress')
                                                    <i class="fas fa-cog mr-1"></i>{{ __('In Progress') }}
                                                    @break
                                                @case('waiting')
                                                    <i class="fas fa-clock mr-1"></i>{{ __('Waiting') }}
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
                                            $priorityClass = match($ticket->priority) {
                                                'urgent' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                                'high' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
                                                'normal' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                                'low' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                                default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $priorityClass }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </div>

                                    <p class="text-gray-600 dark:text-gray-400 mb-3">
                                        {{ Str::limit($ticket->description, 200) }}
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                                            <span>
                                                <i class="fas fa-tag mr-1"></i>{{ ucfirst($ticket->category) }}
                                            </span>
                                            <span>
                                                <i class="fas fa-calendar mr-1"></i>{{ $ticket->created_at->format('M j, Y') }}
                                            </span>
                                            @if($ticket->assignedTo)
                                                <span>
                                                    <i class="fas fa-user mr-1"></i>{{ __('Assigned to') }} {{ $ticket->assignedTo->name }}
                                                </span>
                                            @endif
                                            @if($ticket->replies_count > 0)
                                                <span>
                                                    <i class="fas fa-comments mr-1"></i>{{ $ticket->replies_count }} {{ trans_choice('reply|replies', $ticket->replies_count) }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            @if($ticket->status !== 'closed')
                                                <form action="{{ route('support.tickets.close', $ticket) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" 
                                                            onclick="return confirm('{{ __('Are you sure you want to close this ticket?') }}')"
                                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-sm"
                                                            title="{{ __('Close Ticket') }}">
                                                        <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('support.tickets.reopen', $ticket) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                                                            title="{{ __('Reopen Ticket') }}">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <a href="{{ route('support.tickets.show', $ticket) }}" 
                                               class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
                                               title="{{ __('View Ticket') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Last Activity -->
                            @if($ticket->updated_at->gt($ticket->created_at))
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('Last updated') }}: {{ $ticket->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $tickets->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    @if(request('status'))
                        {{ __('No tickets found') }}
                    @else
                        {{ __('No Support Tickets') }}
                    @endif
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    @if(request('status'))
                        {{ __('No tickets match the selected status filter.') }}
                    @else
                        {{ __('You haven\'t created any support tickets yet. When you need help, create a ticket and our team will assist you.') }}
                    @endif
                </p>
                <div class="space-x-4">
                    <a href="{{ route('support.tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>{{ __('Create Ticket') }}
                    </a>
                    @if(request('status'))
                        <a href="{{ route('support.tickets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            {{ __('View All Tickets') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Quick Stats -->
        @if($tickets->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
                @php
                    $allTickets = auth()->user()->supportTickets;
                    $openCount = $allTickets->where('status', 'open')->count();
                    $inProgressCount = $allTickets->where('status', 'in_progress')->count();
                    $waitingCount = $allTickets->where('status', 'waiting')->count();
                    $closedCount = $allTickets->where('status', 'closed')->count();
                @endphp
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-green-100 dark:bg-green-900">
                            <i class="fas fa-circle-dot text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Open') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $openCount }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900">
                            <i class="fas fa-cog text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('In Progress') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $inProgressCount }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-yellow-100 dark:bg-yellow-900">
                            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Waiting') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $waitingCount }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-gray-100 dark:bg-gray-700">
                            <i class="fas fa-check-circle text-gray-600 dark:text-gray-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Closed') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $closedCount }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterTickets() {
    const status = document.getElementById('status-filter').value;
    const url = new URL(window.location);
    
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    
    window.location.href = url.toString();
}
</script>
@endpush
