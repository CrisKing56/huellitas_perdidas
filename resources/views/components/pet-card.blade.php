@props(['name', 'type', 'description', 'location', 'image', 'status', 'tagColor', 'btnText'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
    <div class="relative h-64">
        <img src="{{ $image }}" class="w-full h-full object-cover">
        
        <span class="absolute top-4 left-4 text-white text-xs font-semibold px-3 py-1 rounded-full {{ $tagColor }}">
            {{ $status }}
        </span>
        
        <button class="absolute top-4 right-4 bg-white p-2 rounded-full shadow text-gray-500 hover:text-primary transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
        </button>
    </div>
    
    <div class="p-5">
        <h3 class="font-bold text-lg text-gray-900">{{ $name }}</h3>
        <p class="text-xs text-gray-500 mb-2">{{ $type }}</p>
        <p class="text-sm text-gray-600 mb-3">{{ $description }}</p>
        
        <div class="flex items-center gap-1 text-xs {{ $tagColor == 'bg-primary' ? 'text-gray-500' : 'text-primary' }} mb-4">
            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            {{ $location }}
        </div>
        
        <a href="#" class="flex justify-between items-center text-sm font-semibold text-gray-800 hover:text-primary transition group">
            {{ $btnText }}
            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
</div>