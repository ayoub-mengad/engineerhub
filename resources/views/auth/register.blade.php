<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-medium text-[#202124] mb-2">Join EngineerHub</h1>
        <p class="text-gray-600">Connect with engineers worldwide</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-[#202124] font-medium" />
            <x-text-input id="name" class="block mt-1 w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-[#202124] font-medium" />
            <x-text-input id="email" class="block mt-1 w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-[#202124] font-medium" />

            <x-text-input id="password" class="block mt-1 w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-[#202124] font-medium" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-[#0A66C2] hover:text-[#004182] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0A66C2] transition-colors duration-150" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <button type="submit" class="ms-4 bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-4 py-2 transition-colors duration-150">
                {{ __('Register') }}
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">
            By registering, you agree to our Terms of Service and Privacy Policy
        </p>
    </div>
</x-guest-layout>
