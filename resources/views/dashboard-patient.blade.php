<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome banner -->
            <div class="bg-teal-600 rounded-2xl p-6 flex items-center justify-between shadow">
                <div>
                    <p class="text-white text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</p>
                    <p class="text-teal-100 mt-1">Book and track your dental appointments here.</p>
                </div>
                <x-application-logo class="w-16 h-16 fill-current text-teal-200" />
            </div>

            <!-- Summary cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Upcoming</p>
                    <p class="text-3xl font-bold text-blue-700 mt-1">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Completed visits</p>
                    <p class="text-3xl font-bold text-green-700 mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-red-500">
                    <p class="text-sm text-gray-500">Cancelled or missed</p>
                    <p class="text-3xl font-bold text-red-700 mt-1">{{ $stats['cancelled'] }}</p>
                </div>
            </div>

            <!-- Next appointment -->
            <div class="bg-white rounded-2xl shadow p-6">
                <p class="font-semibold text-gray-800 mb-3">Your next appointment</p>

                @if ($next)
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <p class="text-lg font-semibold text-teal-800">{{ $next->service_name }}</p>
                                @include('appointments._status-badge', ['status' => $next->status])
                            </div>
                            <p class="text-gray-700">{{ $next->dateLabel() }} · {{ $next->timeLabel() }}</p>
                            <p class="text-sm text-teal-700">with {{ $next->dentist_name }}</p>
                        </div>
                        <a href="{{ route('appointments.index') }}" class="text-sm font-semibold text-teal-700 hover:underline">View my appointments</a>
                    </div>
                @else
                    <p class="text-gray-600">You have no upcoming appointments.</p>
                    <a href="{{ route('appointments.create') }}"
                       class="mt-4 inline-flex items-center px-4 py-2 bg-teal-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700">
                        + Book appointment
                    </a>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>