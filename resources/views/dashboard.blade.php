<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome banner -->
            <div class="bg-teal-600 rounded-2xl p-6 flex items-center justify-between shadow">
                <div>
                    <p class="text-white text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</p>
                    <p class="text-teal-100 mt-1">
                        {{ $stats['today'] }} appointment(s) today · {{ $stats['pending_all'] }} waiting for confirmation
                    </p>
                </div>
                <x-application-logo class="w-16 h-16 fill-current text-teal-200" />
            </div>

            <!-- Date filter and export -->
            <form method="GET" action="{{ route('dashboard') }}" class="bg-white rounded-2xl shadow p-4 flex flex-col sm:flex-row gap-3 sm:items-end">
                <div>
                    <x-input-label for="from" value="From" />
                    <x-text-input id="from" name="from" type="date" class="mt-1" :value="$from" required />
                </div>
                <div>
                    <x-input-label for="to" value="To" />
                    <x-text-input id="to" name="to" type="date" class="mt-1" :value="$to" required />
                </div>
                <x-primary-button type="submit">Apply</x-primary-button>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-teal-700 underline self-center">This month</a>

                <a href="{{ route('admin.reports.export', ['from' => $from, 'to' => $to]) }}"
                   class="sm:ms-auto inline-flex items-center justify-center px-4 py-2 border border-teal-600 rounded-md font-semibold text-xs text-teal-700 uppercase tracking-widest hover:bg-teal-50">
                    Export CSV
                </a>
            </form>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">{{ $errors->first() }}</div>
            @endif

            <!-- KPI cards (for the chosen date range) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Total appointments</p>
                    <p class="text-3xl font-bold text-blue-700 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Completed</p>
                    <p class="text-3xl font-bold text-green-700 mt-1">{{ $stats['completed'] }}</p>
                    <p class="text-xs text-gray-500">{{ $stats['completion_rate'] }}% completion rate</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-red-500">
                    <p class="text-sm text-gray-500">Cancelled or no-show</p>
                    <p class="text-3xl font-bold text-red-700 mt-1">{{ $stats['cancelled'] }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-purple-500">
                    <p class="text-sm text-gray-500">Revenue (completed)</p>
                    <p class="text-3xl font-bold text-purple-700 mt-1">₱{{ number_format($stats['revenue'], 2) }}</p>
                </div>
            </div>

            <!-- Chart 1: appointments per day -->
            <div class="bg-white rounded-2xl shadow p-6">
                <p class="font-semibold text-gray-800 mb-4">Appointments per day</p>
                <div class="h-64"><canvas id="dailyChart"></canvas></div>
            </div>

            <!-- Charts 2 to 4 -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl shadow p-6">
                    <p class="font-semibold text-gray-800 mb-4">Status breakdown</p>
                    <div class="h-64"><canvas id="statusChart"></canvas></div>
                </div>
                <div class="bg-white rounded-2xl shadow p-6">
                    <p class="font-semibold text-gray-800 mb-4">Top services</p>
                    <div class="h-64"><canvas id="serviceChart"></canvas></div>
                </div>
                <div class="bg-white rounded-2xl shadow p-6">
                    <p class="font-semibold text-gray-800 mb-4">Appointments per dentist</p>
                    <div class="h-64"><canvas id="dentistChart"></canvas></div>
                </div>
            </div>

            <!-- Upcoming appointments -->
            <div class="bg-white rounded-2xl shadow overflow-x-auto">
                <div class="flex items-center justify-between px-6 pt-5">
                    <p class="font-semibold text-gray-800">Upcoming appointments</p>
                    <a href="{{ route('admin.appointments.index') }}" class="text-sm text-teal-700 hover:underline">View all</a>
                </div>
                <table class="min-w-full text-sm mt-3">
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($upcoming as $appointment)
                            <tr>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <p class="font-semibold text-gray-800">{{ $appointment->dateLabel() }}</p>
                                    <p class="text-xs text-gray-500">{{ $appointment->timeLabel() }}</p>
                                </td>
                                <td class="px-6 py-3">{{ $appointment->patient_name }}</td>
                                <td class="px-6 py-3">{{ $appointment->service_name }}</td>
                                <td class="px-6 py-3">{{ $appointment->dentist_name }}</td>
                                <td class="px-6 py-3">@include('appointments._status-badge', ['status' => $appointment->status])</td>
                            </tr>
                        @empty
                            <tr><td class="px-6 py-6 text-center text-gray-500">No upcoming appointments.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Chart.js library and the four charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const baseOptions = { responsive: true, maintainAspectRatio: false };

        new Chart(document.getElementById('dailyChart'), {
            type: 'bar',
            data: {
                labels: @json($dailyLabels),
                datasets: [{ label: 'Appointments', data: @json($dailyCounts), backgroundColor: '#14b8a6', borderRadius: 6 }]
            },
            options: { ...baseOptions, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: @json(array_values(\App\Models\Appointment::STATUSES)),
                datasets: [{ data: @json($byStatus->values()), backgroundColor: ['#facc15', '#3b82f6', '#22c55e', '#ef4444', '#9ca3af'] }]
            },
            options: { ...baseOptions, plugins: { legend: { position: 'bottom' } } }
        });

        new Chart(document.getElementById('serviceChart'), {
            type: 'bar',
            data: {
                labels: @json($byService->keys()),
                datasets: [{ label: 'Bookings', data: @json($byService->values()), backgroundColor: '#8b5cf6', borderRadius: 6 }]
            },
            options: { ...baseOptions, indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        new Chart(document.getElementById('dentistChart'), {
            type: 'bar',
            data: {
                labels: @json($byDentist->keys()),
                datasets: [{ label: 'Appointments', data: @json($byDentist->values()), backgroundColor: '#3b82f6', borderRadius: 6 }]
            },
            options: { ...baseOptions, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    </script>
</x-app-layout>