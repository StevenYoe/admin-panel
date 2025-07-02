<!--
    Button Component (button.blade.php)
    ------------------------------------------------
    This Blade component renders a styled button or anchor link for use throughout the application.
    - Accepts props for type, variant, href, and size.
    - Dynamically applies Tailwind CSS classes based on the variant and size.
    - If an href is provided, renders an anchor tag; otherwise, renders a button element.
    - Supports primary, secondary, success, danger, and outline variants.
    - Supports small, medium, and large sizes.
-->

@props([
    'type' => 'button',
    'variant' => 'primary',
    'href' => null,
    'size' => 'md'
])

@php
    // Base classes for all buttons
    $baseClasses = 'inline-flex items-center justify-center rounded-md font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    // Variant-specific classes
    $variantClasses = [
        'primary' => 'bg-accent hover:bg-red-700 text-white focus:ring-accent',
        'secondary' => 'bg-gray-500 hover:bg-gray-600 text-white focus:ring-gray-500',
        'success' => 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-600',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-600',
        'outline' => 'bg-transparent border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-gray-300 dark:focus:ring-gray-600',
    ];
    
    // Size-specific classes
    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    
    // Combine all classes
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
@endphp

@if($href)
    <!-- Render as anchor link if href is provided -->
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <!-- Render as button if no href is provided -->
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif