<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">Dentists</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Search box and Add button -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <form method="GET" action="{{ route('admin.dentists.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search name or specialization"
                           class="border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                    <x-primary-button type="submit">Search</x-primary-button>
                </form>

                <a href="{{ route('admin.dentists.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-teal-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700">
                    + Add dentist
                </a>
            </div>

            <!-- Dentists table -->
            <div class="bg-white rounded-2xl shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-teal-50 text-teal-800 text-left">
                        <tr>
                            <th class="px-4 py-3">Dentist</th>
                            <th class="px-4 py-3">Working days</th>
                            <th class="px-4 py-3">Hours</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($dentists as $dentist)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-800">{{ $dentist->name }}</p>
                                    <p class="text-teal-700 text-xs">{{ $dentist->specialization }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $dentist->workingDaysLabel() }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $dentist->workingHoursLabel() }}</td>
                                <td class="px-4 py-3">
                                    @if ($dentist->is_active)
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs bg-gray-200 text-gray-700">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.dentists.edit', $dentist) }}" class="text-teal-700 hover:underline font-semibold">Edit</a>

                                    <form method="POST" action="{{ route('admin.dentists.destroy', $dentist) }}" class="inline"
                                          onsubmit="return confirm('Delete this dentist?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ms-3 text-red-600 hover:underline font-semibold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No dentists yet. Click "Add dentist" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $dentists->links() }}

        </div>
    </div>
</x-app-layout>