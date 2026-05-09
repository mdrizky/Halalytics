@props([
    'type' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'fullWidth' => false,
    'class' => '',
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

$typeClasses = match($type) {
    'primary' => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 disabled:bg-emerald-300',
    'secondary' => 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500 disabled:bg-gray-300',
    'success' => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 disabled:bg-green-300',
    'warning' => 'bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500 disabled:bg-yellow-300',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 disabled:bg-red-300',
    'outline' => 'border border-emerald-600 text-emerald-600 hover:bg-emerald-50 focus:ring-emerald-500 disabled:border-gray-300 disabled:text-gray-400',
    'ghost' => 'text-emerald-600 hover:bg-emerald-50 focus:ring-emerald-500 disabled:text-gray-400',
    default => 'bg-emerald-600 text-white hover:bg-emerald-700 focus:ring-emerald-500 disabled:bg-emerald-300',
};

$sizeClasses = match($size) {
    'xs' => 'px-2.5 py-1.5 text-xs',
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-6 py-3 text-base',
    'xl' => 'px-8 py-4 text-lg',
    default => 'px-4 py-2 text-sm',
};

$widthClass = $fullWidth ? 'w-full' : '';
$disabledClass = $disabled ? 'opacity-50 cursor-not-allowed' : '';
$loadingClass = $loading ? 'opacity-75 cursor-wait' : '';

$allClasses = trim(implode(' ', array_filter([$baseClasses, $typeClasses, $sizeClasses, $widthClass, $disabledClass, $loadingClass, $class])));
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $allClasses }}" {{ $disabled ? 'tabindex="-1" : '' }}>
        @if ($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        
        @if ($icon && $iconPosition === 'left')
            <span class="mr-2">{{ $icon }}</span>
        @endif
        
        {{ $slot }}
        
        @if ($icon && $iconPosition === 'right')
            <span class="ml-2">{{ $icon }}</span>
        @endif
    </a>
@else
    <button type="button" class="{{ $allClasses }}" {{ $disabled ? 'disabled' : '' }}>
        @if ($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        
        @if ($icon && $iconPosition === 'left')
            <span class="mr-2">{{ $icon }}</span>
        @endif
        
        {{ $slot }}
        
        @if ($icon && $iconPosition === 'right')
            <span class="ml-2">{{ $icon }}</span>
        @endif
    </button>
@endif
