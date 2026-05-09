@props([
    'type' => 'primary',
    'size' => 'sm',
    'pill' => false,
    'class' => '',
])

@php
$typeClasses = match($type) {
    'primary' => 'bg-emerald-100 text-emerald-800',
    'secondary' => 'bg-gray-100 text-gray-800',
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger' => 'bg-red-100 text-red-800',
    'info' => 'bg-blue-100 text-blue-800',
    'outline' => 'border border-emerald-300 text-emerald-700 bg-emerald-50',
    default => 'bg-emerald-100 text-emerald-800',
};

$sizeClasses = match($size) {
    'xs' => 'px-2 py-0.5 text-xs',
    'sm' => 'px-2.5 py-0.5 text-sm',
    'md' => 'px-3 py-1 text-sm',
    'lg' => 'px-4 py-1.5 text-base',
    'xl' => 'px-5 py-2 text-lg',
    default => 'px-2.5 py-0.5 text-sm',
};

$roundedClasses = $pill ? 'rounded-full' : 'rounded';
$allClasses = trim(implode(' ', array_filter(['inline-flex items-center font-medium', $typeClasses, $sizeClasses, $roundedClasses, $class])));
@endphp

<span class="{{ $allClasses }}">
    {{ $slot }}
</span>
