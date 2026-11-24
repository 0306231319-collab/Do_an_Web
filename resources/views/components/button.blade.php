@props([
    'type' => 'button',
    'color' => 'green',
])

@php
    $colors = [
        'green' => 'bg-green-600 hover:bg-green-700',
        'red' => 'bg-red-600 hover:bg-red-700',
        'blue' => 'bg-blue-600 hover:bg-blue-700',
        'gray' => 'bg-gray-600 hover:bg-gray-700',
    ];

    $colorClass = $colors[$color] ?? $colors['green'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => "rounded-lg px-4 py-2 text-white transition {$colorClass}"
    ]) }}
>
    {{ $slot }}
</button>