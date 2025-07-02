<!--
    Checkbox Form Component (checkbox.blade.php)
    ------------------------------------------------
    This Blade component renders a styled checkbox input for forms.
    - Accepts props for name, id, label, value, checked state, disabled state, helper text, and error message.
    - Automatically sets the input id and checked state based on old input or default value.
    - Displays a label and helper text if provided.
    - Shows error messages if validation fails.
    - Uses Tailwind CSS classes for consistent styling.
-->

@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'helper' => null,
    'error' => null
])

@php
    // Determine the input id and checked state
    $inputId = $id ?? $name;
    $isChecked = old($name, $checked);
@endphp

<div class="mb-4">
    <div class="flex items-start">
        <div class="flex items-center h-5">
            <!-- Checkbox input element -->
            <input 
                type="checkbox"
                name="{{ $name }}"
                id="{{ $inputId }}"
                value="{{ $value }}"
                {{ $isChecked ? 'checked' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->merge(['class' => 'w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent dark:bg-gray-700 dark:border-gray-600 ' . ($error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '')]) }}
            >
        </div>
        <div class="ml-3 text-sm">
            <!-- Checkbox label if provided -->
            @if($label)
                <label for="{{ $inputId }}" class="font-medium">{{ $label }}</label>
            @endif
            
            <!-- Helper text if provided -->
            @if($helper)
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $helper }}</p>
            @endif
        </div>
    </div>
    
    <!-- Error message if validation fails -->
    @if($error)
        <p class="mt-1 text-xs text-red-500">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="mt-1 text-xs text-red-500">{{ $errors->first($name) }}</p>
    @endif
</div>