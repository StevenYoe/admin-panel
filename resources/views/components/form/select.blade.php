<!--
    Select Form Component (select.blade.php)
    ------------------------------------------------
    This Blade component renders a styled select dropdown for forms.
    - Accepts props for name, id, label, options, selected value, required/disabled state, placeholder, helper text, and error message.
    - Automatically sets the input id and selected value based on old input or default value.
    - Displays a label and required indicator if provided.
    - Shows helper text and error messages if validation fails.
    - Uses Tailwind CSS classes for consistent styling.
-->

@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Select an option',
    'helper' => null,
    'error' => null
])

@php
    // Determine the input id
    $inputId = $id ?? $name;
@endphp

<div class="mb-4">
    @if($label)
        <!-- Select label with required indicator if needed -->
        <label for="{{ $inputId }}" class="block mb-2 text-sm font-medium">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <!-- Select dropdown element -->
    <select 
        name="{{ $name }}"
        id="{{ $inputId }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-accent focus:border-accent dark:bg-gray-700 dark:text-white ' . ($error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '')]) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $label)
            <option value="{{ $value }}" {{ old($name, $selected) == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
    
    <!-- Helper text if provided -->
    @if($helper)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $helper }}</p>
    @endif
    
    <!-- Error message if validation fails -->
    @if($error)
        <p class="mt-1 text-xs text-red-500">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="mt-1 text-xs text-red-500">{{ $errors->first($name) }}</p>
    @endif
</div>