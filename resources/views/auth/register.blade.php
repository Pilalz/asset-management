<x-guest-layout>
    @push('styles')
        <style>
            p{
                font-size:12px !important;
                color:gray !important;
            }
        </style>
    @endpush

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="flex flex-col text-center justify-center mb-2">
            <img src="{{ asset('images/logo.svg') }}" class="h-8 me-3 mt-2" alt="Asset Management Logo" />
            <h1 class="font-bold text-lg mt-4">Welcome To Asset Management</h1>
            <p class="font-xs">Create your account.</p>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-700 border border-gray-300 rounded-md font-semibold text-xs shadow-sm hover:bg-gray-900 transition ease-in-out duration-150">
                {{ __('Register') }}
            </x-primary-button>
        </div>

        <div class="text-sm text-center text-gray-500 mt-4">
            Already registered?
            <a class="font-medium text-blue-600 hover:underline dark:text-blue-500" href="{{ route('login') }}">
                {{ __('Sign in') }}
            </a>
        </div>
    </form>
</x-guest-layout>
