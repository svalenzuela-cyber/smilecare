<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">Services</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <!-- Success message after add / edit / delete -->
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Search box and Add button -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <form method="GET" action="{{ route('admin.services.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search services"
                           class="border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                    <x-primary-button type="submit">Search</x-primary-button>
                </form>

                <a href="{{ route('admin.services.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-teal-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-teal-700">
                    + Add service
                </a>
            </div>

            <!-- Services table -->
            <div class="bg-white rounded-2xl shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-teal-50 text-teal-800 text-left">
                        <tr>
                            <th class="px-4 py-3">Service</th>
                            <th class="px-4 py-3">Price</th>
                            <th class="px-4 py-3">Duration</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($services as $service)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-800">{{ $service->name }}</p>
                                    <p class="text-gray-500 text-xs">{{ $service->description }}</p>
                                </td>
                                <td class="px-4 py-3">₱{{ number_format($service->price, 2) }}</td>
                                <td class="px-4 py-3">{{ $service->duration_minutes }} min</td>
                                <td class="px-4 py-3">
                                    @if ($service->is_active)
                                        <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs bg-gray-200 text-gray-700">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="text-teal-700 hover:underline font-semibold">Edit</a>

                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="inline"
                                          onsubmit="return confirm('Delete this service?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ms-3 text-red-600 hover:underline font-semibold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    No services yet. Click "Add service" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Page numbers (10 services per page) -->
            {{ $services->links() }}

        </div>
    </div>
</x-app-layout>