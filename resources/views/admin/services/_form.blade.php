@php($service = $service ?? null)

<div class="space-y-5">
    <!-- Name -->
    <div>
        <x-input-label for="name" value="Service name" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                      :value="old('name', $service?->name)" required maxlength="100" placeholder="Teeth Cleaning" />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Description -->
    <div>
        <x-input-label for="description" value="Description (optional)" />
        <textarea id="description" name="description" rows="3" maxlength="500"
                  class="mt-1 block w-full border-gray-300 focus:border-teal-500 focus:ring-teal-500 rounded-md shadow-sm"
                  placeholder="Removes plaque and tartar to keep teeth healthy">{{ old('description', $service?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <!-- Price and Duration side by side -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <x-input-label for="price" value="Price (₱)" />
            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full"
                          :value="old('price', $service?->price)" required placeholder="800" />
            <x-input-error :messages="$errors->get('price')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="duration_minutes" value="Duration (minutes)" />
            <x-text-input id="duration_minutes" name="duration_minutes" type="number" step="5" min="5" max="480" class="mt-1 block w-full"
                          :value="old('duration_minutes', $service?->duration_minutes)" required placeholder="30" />
            <x-input-error :messages="$errors->get('duration_minutes')" class="mt-2" />
        </div>
    </div>

    <!-- Active checkbox -->
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-500"
               @checked(old('is_active', $service?->is_active ?? true))>
        <span class="ms-2 text-sm text-gray-700">Active (patients can book this service)</span>
    </label>
</div>