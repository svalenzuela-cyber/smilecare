@php
    // Color for each status
    $classes = match ($status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'no_show' => 'bg-gray-200 text-gray-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span class="px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap {{ $classes }}">
    {{ \App\Models\Appointment::STATUSES[$status] ?? ucfirst($status) }}
</span>