@php
    $dentist = $dentist ?? null;
    // New dentist: Monday to Friday is pre-selected for convenience
    $selectedDays = old('working_days', $dentist?->working_days ?? ['mon', 'tue', 'wed', 'thu', 'fri']);
@endphp

<div class="space-y-5">
    <!-- Name and Specialization -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="name" value="Full name" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                          :value="old('name', $dentist?->name)" required maxlength="100" placeholder="Dr. Maria Cruz" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="specialization" value="Specialization" />
            <x-text-input id="specialization" name="specialization" type="text" class="mt-1 block w-full"
                          :value="old('specialization', $dentist?->specialization)" required maxlength="100" placeholder="General Dentistry" />
            <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
        </div>
    </div>

    <!-- Bio -->
    <div>
        <x-input-label for="bio" value="Short bio (optional, shown to patients)" />
        <textarea id="bio" name="bio" rows="3" maxlength="500"
                  class="mt-1 block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm"
                  placeholder="10 years of experience. Gentle with nervous patients.">{{ old('bio', $dentist?->bio) }}</textarea>
        <x-input-error :messages="$errors->get('bio')" class="mt-2" />
    </div>

    <!-- Working days -->
    <div>
        <x-input-label value="Working days" />
        <div class="mt-2 flex flex-wrap gap-3">
            @foreach (\App\Models\Dentist::DAYS as $code => $label)
                <label class="inline-flex items-center px-3 py-2 border border-gray-200 rounded-lg cursor-pointer hover:bg-teal-50">
                    <input type="checkbox" name="working_days[]" value="{{ $code }}"
                           class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500"
                           @checked(in_array($code, $selectedDays))>
                    <span class="ms-2 text-sm text-gray-700">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('working_days')" class="mt-2" />
    </div>

    <!-- Working hours -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="start_time" value="Start time" />
            <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full"
                          :value="old('start_time', $dentist?->start_time ?? '09:00')" required />
            <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="end_time" value="End time" />
            <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full"
                          :value="old('end_time', $dentist?->end_time ?? '17:00')" required />
            <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
        </div>
    </div>

    <!-- Active checkbox -->
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500"
               @checked(old('is_active', $dentist?->is_active ?? true))>
        <span class="ms-2 text-sm text-gray-700">Active (patients can book this dentist)</span>
    </label>
</div>