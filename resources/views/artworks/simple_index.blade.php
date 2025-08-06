@extends('layouts.app')

@section('title', 'Artworks Test')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Artworks Gallery (TEST VERSION)</h1>
    
    <p>Total artworks: {{ $artworks->total() ?? 'Unknown' }}</p>
    <p>Current page artworks: {{ $artworks->count() ?? 'Unknown' }}</p>
    
    @if(isset($artworks) && $artworks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-8">
            @foreach($artworks as $artwork)
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="font-semibold">
                        {{ $artwork->title ?? 'No Title' }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        ID: {{ $artwork->id ?? 'Unknown' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        By: {{ $artwork->user->name ?? 'Unknown Artist' }}
                    </p>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $artworks->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <p class="text-xl text-gray-600">No artworks found</p>
            <p class="text-sm text-gray-500">Debug: {{ count(get_defined_vars()) }} variables available</p>
        </div>
    @endif
</div>
@endsection
