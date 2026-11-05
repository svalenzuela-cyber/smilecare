<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">Add dentist</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-6">
                <form method="POST" action="{{ route('admin.dentists.store') }}">
                    @csrf

                    @include('admin.dentists._form')

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>Save dentist</x-primary-button>
                        <a href="{{ route('admin.dentists.index') }}" class="text-sm text-gray-600 hover:text-teal-700 underline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>