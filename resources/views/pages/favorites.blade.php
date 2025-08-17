@extends('layouts.app')

@section('content')
    <div class="px-5 mt-5">
        <h1 class="text-xl font-semibold mb-4">Favorites - {{ $store->name }}</h1>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @forelse ($favorites as $product)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="{{ asset('storage/'.$product->thumbnail) }}" class="w-full h-40 object-cover" alt="{{ $product->name }}">
                    <div class="p-3">
                        <h3 class="font-semibold text-gray-800 text-sm">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-500">Rp {{ number_format($product->price,0,',','.') }}</p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No favorites found.</p>
            @endforelse
        </div>
    </div>
    @include('includes.navigation')
@endsection