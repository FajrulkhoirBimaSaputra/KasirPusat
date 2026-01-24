<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary to-dark px-4">
        <div class="w-full max-w-md bg-secondary rounded-2xl shadow-xl p-8">

            <!-- Logo / Title -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-primary">
                    TOKO KOPI PUSAT
                </h1>
                <p class="text-sm text-dark/70 mt-1">
                    LOGIN DULU BOLO!
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-primary" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Username -->
                <div>
                    <x-input-label for="username" value="Username" />
                    <x-text-input id="username" name="username" type="text" class="block w-full" required autofocus />
                    <x-input-error :messages="$errors->get('username')" />

                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password" class="text-dark" />
                    <div class="relative mt-1">
                        <x-text-input id="password" type="password" name="password"
                            class="block w-full pl-10 border-dark/20 focus:border-primary focus:ring-primary"
                            required />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-primary" />
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-sm text-dark/80">
                        <input type="checkbox" name="remember"
                            class="rounded border-dark/30 text-primary focus:ring-primary">
                        <span class="ml-2">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-primary hover:text-primary-dark hover:underline">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Button -->
                <x-primary-button class="w-full justify-center py-3 text-base bg-primary hover:bg-primary-dark">
                    Log in
                </x-primary-button>
            </form>

            <!-- Footer -->
            <p class="text-center text-xs text-dark/60 mt-6">
                © {{ date('Y') }} Kasir Pusat. All rights reserved.
            </p>
        </div>
    </div>
</x-guest-layout>