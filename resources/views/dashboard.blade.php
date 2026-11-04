<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome banner -->
            <div class="bg-teal-600 rounded-2xl p-6 flex items-center justify-between shadow">
                <div>
                    <p class="text-white text-2xl font-bold">Welcome, {{ Auth::user()->name }}!</p>
                    <p class="text-teal-100 mt-1">
                        You are logged in as <span class="font-semibold uppercase">{{ Auth::user()->role }}</span>.
                    </p>
                </div>
                <x-application-logo class="w-16 h-16 fill-current text-teal-200" />
            </div>

            <!-- Summary cards (numbers will be connected to real appointments later) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500">Today's appointments</p>
                    <p class="text-3xl font-bold text-blue-700 mt-1">0</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-purple-500">
                    <p class="text-sm text-gray-500">This month</p>
                    <p class="text-3xl font-bold text-purple-700 mt-1">0</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500">Completed</p>
                    <p class="text-3xl font-bold text-green-700 mt-1">0</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-red-500">
                    <p class="text-sm text-gray-500">Cancelled</p>
                    <p class="text-3xl font-bold text-red-700 mt-1">0</p>
                </div>
            </div>

            <!-- Placeholder for charts -->
            <div class="bg-white rounded-2xl shadow p-6">
                <p class="font-semibold text-gray-800">Charts and reports</p>
                <p class="text-gray-600 text-sm mt-1">
                    Charts will appear here once appointments are added to the system.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>