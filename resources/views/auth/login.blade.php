<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h1 class="text-2xl font-medium text-[#202124] mb-2">Welcome back</h1>
        <p class="text-gray-600">Sign in to your EngineerHub account</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[#202124] font-medium" />
            <x-text-input id="email" class="block mt-1 w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-[#202124] font-medium" />

            <x-text-input id="password" class="block mt-1 w-full border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2] text-[15px] leading-6"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#0A66C2] shadow-sm focus:ring-[#0A66C2]" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-[#0A66C2] hover:text-[#004182] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0A66C2] transition-colors duration-150" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <button type="submit" class="ms-3 bg-[#0A66C2] hover:bg-[#004182] text-white font-semibold rounded-lg px-4 py-2 transition-colors duration-150">
                {{ __('Log in') }}
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-[#0A66C2] hover:text-[#004182] font-medium transition-colors duration-150">Sign up</a>
        </p>
    </div>
</x-guest-layout>
