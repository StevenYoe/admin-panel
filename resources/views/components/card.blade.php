<!--
    Card Component (card.blade.php)
    ------------------------------------------------
    This Blade component renders a styled card container for grouping content.
    - Accepts props for title and footer.
    - Displays a title section if provided.
    - Renders the main content in the slot area.
    - Displays a footer section if provided.
    - Uses Tailwind CSS classes for consistent styling and dark mode support.
-->

@props(['title' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden']) }}>
    @if($title)
        <!-- Card title section -->
        <div class="px-5 py-4 text-center border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium">{{ $title }}</h3>
        </div>
    @endif
    
    <!-- Card main content slot -->
    <div class="p-6">
        {{ $slot }}
    </div>
    
    @if($footer)
        <!-- Card footer section -->
        <div class="px-5 py-4 text-center bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
            {{ $footer }}
        </div>
    @endif
</div>