<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">My appointments</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ session('error') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('appointments.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-teal-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700">
                    + Book appointment
                </a>
            </div>

            @forelse ($appointments as $appointment)
                <div class="bg-white rounded-2xl shadow p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3">
                            <p class="font-semibold text-gray-800">{{ $appointment->service_name }}</p>
                            @include('appointments._status-badge', ['status' => $appointment->status])
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ $appointment->dateLabel() }} · {{ $appointment->timeLabel() }}
                        </p>
                        <p class="text-sm text-teal-700">with {{ $appointment->dentist_name }}</p>
                        @if ($appointment->notes)
                            <p class="text-xs text-gray-500">Notes: {{ $appointment->notes }}</p>
                        @endif
                    </div>

                    @if (in_array($appointment->status, \App\Models\Appointment::ACTIVE_STATUSES) && $appointment->isUpcoming())
                        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}"
                              onsubmit="return confirm('Cancel this appointment?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Cancel appointment</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow p-8 text-center text-gray-500">
                    You have no appointments yet. Click "Book appointment" to make one.
                </div>
            @endforelse

            {{ $appointments->links() }}

        </div>
    </div>
</x-app-layout>