@props(['title' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden']) }}>
    @if($title)
        <div class="px-5 py-4 text-center border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium">{{ $title }}</h3>
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>
    
    @if($footer)
        <div class="px-5 py-4 text-center bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
            {{ $footer }}
        </div>
    @endif
</div>