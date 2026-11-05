<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">Appointments</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ $errors->first() }}</div>
            @endif

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.appointments.index') }}" class="bg-white rounded-2xl shadow p-4 flex flex-col sm:flex-row gap-3 sm:items-end">
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                        <option value="">All</option>
                        @foreach (\App\Models\Appointment::STATUSES as $code => $label)
                            <option value="{{ $code }}" @selected(($filters['status'] ?? '') === $code)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="dentist_id" value="Dentist" />
                    <select id="dentist_id" name="dentist_id" class="mt-1 border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                        <option value="">All</option>
                        @foreach ($dentists as $dentist)
                            <option value="{{ $dentist->getKey() }}" @selected(($filters['dentist_id'] ?? '') == $dentist->getKey())>{{ $dentist->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="date" value="Date" />
                    <x-text-input id="date" name="date" type="date" class="mt-1" :value="$filters['date'] ?? ''" />
                </div>
                <div class="flex gap-2">
                    <x-primary-button type="submit">Filter</x-primary-button>
                    <a href="{{ route('admin.appointments.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-teal-700 underline self-center">Clear</a>
                </div>
            </form>

            <!-- Appointments table -->
            <div class="bg-white rounded-2xl shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-teal-50 text-teal-800 text-left">
                        <tr>
                            <th class="px-4 py-3">Date and time</th>
                            <th class="px-4 py-3">Patient</th>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Dentist</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($appointments as $appointment)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="font-semibold text-gray-800">{{ $appointment->dateLabel() }}</p>
                                    <p class="text-gray-500 text-xs">{{ $appointment->timeLabel() }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-800">{{ $appointment->patient_name }}</p>
                                    @if ($appointment->notes)
                                        <p class="text-gray-500 text-xs">Notes: {{ $appointment->notes }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <p>{{ $appointment->service_name }}</p>
                                    <p class="text-gray-500 text-xs">₱{{ number_format($appointment->service_price, 2) }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $appointment->dentist_name }}</td>
                                <td class="px-4 py-3">@include('appointments._status-badge', ['status' => $appointment->status])</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    {{-- Only the allowed next statuses are shown as buttons --}}
                                    @foreach (\App\Models\Appointment::TRANSITIONS[$appointment->status] ?? [] as $next)
                                        <form method="POST" action="{{ route('admin.appointments.status', $appointment) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $next }}">
                                            <button type="submit"
                                                    class="ms-2 font-semibold hover:underline {{ $next === 'cancelled' || $next === 'no_show' ? 'text-red-600' : 'text-teal-700' }}">
                                                {{ $next === 'confirmed' ? 'Confirm' : \App\Models\Appointment::STATUSES[$next] }}
                                            </button>
                                        </form>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">No appointments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $appointments->links() }}

        </div>
    </div>
</x-app-layout>