@props([
    'type' => 'text',
    'label' => null,
    'name' => null,
    'id' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'helper' => null,
    'icon' => null,
    'class' => '',
])

@php
$baseClasses = 'block w-full rounded-lg border border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 focus:ring-1 sm:text-sm';
$paddingClasses = match($type) {
    'text', 'email', 'password', 'tel', 'url' => 'px-3 py-2',
    'textarea' => 'px-3 py-2',
    default => 'px-3 py-2',
};

$iconPadding = $icon ? 'pl-10' : '';
$allClasses = trim(implode(' ', array_filter([$baseClasses, $paddingClasses, $iconPadding, $class])));
@endphp

<div class="space-y-1">
    @if ($label)
        <label for="{{ $id ?? $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if ($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-400 sm:text-sm">{{ $icon }}</span>
            </div>
        @endif
        
        @switch($type)
            @case('textarea')
                <textarea
                    name="{{ $name }}"
                    id="{{ $id ?? $name }}"
                    placeholder="{{ $placeholder }}"
                    {{ $required ? 'required' : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    class="{{ $allClasses }}"
                >{{ $value }}</textarea>
                @break
            
            @case('select')
                <select
                    name="{{ $name }}"
                    id="{{ $id ?? $name }}"
                    {{ $required ? 'required' : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    class="{{ $allClasses }}"
                >
                    {{ $slot }}
                </select>
                @break
            
            @default
                <input
                    type="{{ $type }}"
                    name="{{ $name }}"
                    id="{{ $id ?? $name }}"
                    value="{{ $value }}"
                    placeholder="{{ $placeholder }}"
                    {{ $required ? 'required' : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    class="{{ $allClasses }}"
                />
        @endswitch
    </div>
    
    @if ($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif
    
    @if ($helper)
        <p class="text-sm text-gray-500">{{ $helper }}</p>
    @endif
</div>
