<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-teal-800 leading-tight">Book an appointment</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($services->isEmpty() || $dentists->isEmpty())
                <div class="bg-white rounded-2xl shadow p-6 text-gray-700">
                    Online booking is not available yet. Please check back later.
                </div>
            @else
                <form method="POST" action="{{ route('appointments.store') }}" class="space-y-6">
                    @csrf

                    <!-- STEP 1: Service -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <p class="text-sm font-semibold text-teal-700">Step 1</p>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Choose a service</h3>

                        <select name="service_id" required
                                class="block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm">
                            <option value="">Select a service</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->getKey() }}" @selected(old('service_id') == $service->getKey())>
                                    {{ $service->name }} (₱{{ number_format($service->price, 2) }}, {{ $service->duration_minutes }} min)
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                    </div>

                    <!-- STEP 2: Dentist -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <p class="text-sm font-semibold text-teal-700">Step 2</p>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Choose your dentist</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Any available dentist -->
                            <label class="block border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:border-teal-300 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50">
                                <input type="radio" name="dentist_id" value="any" class="text-teal-600 focus:ring-teal-500"
                                       @checked(old('dentist_id', 'any') === 'any')>
                                <span class="ms-2 font-semibold text-gray-800">Any available dentist</span>
                                <p class="text-sm text-gray-500 mt-1 ms-6">We'll assign the first dentist who is free at your chosen time.</p>
                            </label>

                            <!-- Each dentist -->
                            @foreach ($dentists as $dentist)
                                <label class="block border-2 border-gray-200 rounded-xl p-4 cursor-pointer hover:border-teal-300 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50">
                                    <input type="radio" name="dentist_id" value="{{ $dentist->getKey() }}" class="text-teal-600 focus:ring-teal-500"
                                           @checked(old('dentist_id') == $dentist->getKey())>
                                    <span class="ms-2 font-semibold text-gray-800">{{ $dentist->name }}</span>
                                    <div class="ms-6 mt-1 space-y-1">
                                        <p class="text-sm text-teal-700">{{ $dentist->specialization }}</p>
                                        @if ($dentist->bio)
                                            <p class="text-sm text-gray-600">{{ $dentist->bio }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">
                                            {{ $dentist->workingDaysLabel() }} · {{ $dentist->workingHoursLabel() }}
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('dentist_id')" class="mt-2" />
                    </div>

                    <!-- STEP 3: Date and time -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <p class="text-sm font-semibold text-teal-700">Step 3</p>
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Choose a date and time</h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <x-input-label for="date" value="Date" />
                                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full"
                                              :value="old('date')" min="{{ now()->toDateString() }}" required />
                                <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="time" value="Time" />
                                <x-text-input id="time" name="time" type="time" step="900" class="mt-1 block w-full"
                                              :value="old('time')" required />
                            </div>
                        </div>

                        <!-- Availability problems (wrong day, outside hours, double booking) appear here -->
                        @error('time')
                            <div class="mt-4 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <x-input-label for="notes" value="Notes for the dentist (optional)" />
                        <textarea id="notes" name="notes" rows="3" maxlength="500"
                                  class="mt-1 block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm"
                                  placeholder="Pain on the lower left tooth">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>Book appointment</x-primary-button>
                        <a href="{{ route('appointments.index') }}" class="text-sm text-gray-600 hover:text-teal-700 underline">Cancel</a>
                    </div>
                </form>
            @endif

        </div>
    </div>
</x-app-layout>