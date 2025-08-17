@extends('layouts.app')

@section('content')
    <div class="px-5 mt-5">
        <div id="TopNav" class="relative flex items-center justify-between px-5 py-3 bg-white">
            <a href="{{ route('index', $store->username) }}"
                class="w-12 h-12 flex items-center justify-center shrink-0 rounded-full overflow-hidden bg-[#F0F1F3]">
                <img src="{{asset('assets/images/icons/Arrow - Left.svg')}}" class="w-[28px] h-[28px]" alt="icon">
            </a>
            <p class="font-semibold">All Categories</p>
            <div class="dummy-btn w-12"></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @forelse ($categories as $category)
                <a href="{{ route('product.find-results', $store->username) . '?category=' . $category->slug }}"
                    class="flex flex-col items-center p-4 bg-white rounded-lg shadow">
                    <img src="{{ asset('storage/'.$category->icon) }}" class="w-16 h-16 object-contain" alt="icon">
                    <p class="mt-2 text-sm font-medium">{{ $category->name }}</p>
                </a>
            @empty
                <p class="text-gray-500">No categories found.</p>
            @endforelse
        </div>
    </div>
    @include('includes.navigation') 
@endsection