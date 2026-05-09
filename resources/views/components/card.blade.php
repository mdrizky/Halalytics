@props([
    'padding' => 'normal',
    'shadow' => 'normal',
    'border' => true,
    'rounded' => 'lg',
    'hover' => false,
    'class' => '',
])

@php
$baseClasses = 'bg-white';
$paddingClasses = match($padding) {
    'none' => '',
    'sm' => 'p-3',
    'normal' => 'p-6',
    'lg' => 'p-8',
    'xl' => 'p-10',
    default => 'p-6',
};

$shadowClasses = match($shadow) {
    'none' => '',
    'sm' => 'shadow-sm',
    'normal' => 'shadow-md',
    'lg' => 'shadow-lg',
    'xl' => 'shadow-xl',
    default => 'shadow-md',
};

$borderClasses = $border ? 'border border-gray-200' : '';
$roundedClasses = match($rounded) {
    'none' => '',
    'sm' => 'rounded-sm',
    'normal' => 'rounded',
    'lg' => 'rounded-lg',
    'xl' => 'rounded-xl',
    'full' => 'rounded-full',
    default => 'rounded-lg',
};

$hoverClasses = $hover ? 'hover:shadow-lg transition-shadow duration-200' : '';

$allClasses = trim(implode(' ', array_filter([$baseClasses, $paddingClasses, $shadowClasses, $borderClasses, $roundedClasses, $hoverClasses, $class])));
@endphp

<div class="{{ $allClasses }}">
    {{ $slot }}
</div>
