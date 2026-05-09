@props([
    'id' => 'modal-' . uniqid(),
    'title' => null,
    'show' => false,
    'size' => 'md',
    'closeOnBackdrop' => true,
    'class' => '',
])

@php
$sizeClasses = match($size) {
    'xs' => 'max-w-xs',
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    '6xl' => 'max-w-6xl',
    'full' => 'max-w-full',
    default => 'max-w-md',
};

$allClasses = trim(implode(' ', array_filter(['relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl', $sizeClasses, $class])));
@endphp

<!-- Modal Backdrop -->
<div id="{{ $id }}-backdrop" 
     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity {{ $show ? '' : 'hidden' }}"
     {{ $closeOnBackdrop ? "onclick=\"closeModal('{$id}')\"" : '' }}
></div>

<!-- Modal Panel -->
<div id="{{ $id }}" 
     class="fixed inset-0 z-50 overflow-y-auto {{ $show ? '' : 'hidden' }}"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="{{ $id }}-title">
    
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="{{ $allClasses }}">
            <!-- Modal Header -->
            @if ($title)
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 id="{{ $id }}-title" class="text-lg font-medium text-gray-900">
                        {{ $title }}
                    </h3>
                    <button type="button" 
                            class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-600 transition-colors"
                            onclick="closeModal('{{ $id }}')">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @endif
            
            <!-- Modal Body -->
            <div class="px-6 py-4 {{ $title ? '' : 'pt-6' }}">
                {{ $slot }}
            </div>
            
            <!-- Modal Footer (optional) -->
            @if (isset($footer))
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = document.getElementById(modalId + '-backdrop');
    
    if (modal && backdrop) {
        modal.classList.remove('hidden');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const backdrop = document.getElementById(modalId + '-backdrop');
    
    if (modal && backdrop) {
        modal.classList.add('hidden');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Auto-open if show is true
@if ($show)
    setTimeout(() => openModal('{{ $id }}'), 100);
@endif
</script>
