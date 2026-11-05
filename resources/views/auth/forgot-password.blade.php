<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Forgot your password? Enter the email you used to register and choose a new password.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- New password -->
        <div class="mt-4">
            <x-input-label for="password" value="New password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm new password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirm new password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-teal-700">Back to login</a>
            <x-primary-button>Change password</x-primary-button>
        </div>
    </form>
</x-guest-layout>