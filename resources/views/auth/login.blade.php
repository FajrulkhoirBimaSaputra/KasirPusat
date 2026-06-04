<x-guest-layout>
    <!-- Background Wrapper -->
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-200 relative px-4 sm:px-6 lg:px-8">

        <!-- Ambient Background Effects -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-primary/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-dark/10 blur-3xl"></div>
        </div>

        <!-- Login Card -->
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 sm:p-10 relative z-10 border border-gray-100">

            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-32 h-auto mx-auto mb-4 drop-shadow-sm">

                <p class="text-sm text-gray-500 font-medium">
                    LOGIN DULU BOLO! ☕
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status
                class="mb-5 text-emerald-600 bg-emerald-50 p-3 rounded-xl text-sm font-medium border border-emerald-100"
                :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Username -->
                <div>
                    <x-input-label for="username" value="Username" class="text-gray-700 font-semibold mb-1" />
                    <div class="relative">
                        <!-- Icon User -->
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <x-text-input id="username" name="username" type="text"
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200"
                            required autofocus placeholder="Masukkan username" />
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Password" class="text-gray-700 font-semibold mb-1" />
                    <div class="relative">
                        <!-- Icon Lock -->
                        <div
                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>

                        <!-- Input Password dengan pr-12 agar tidak nabrak icon mata -->
                        <x-text-input id="password" type="password" name="password"
                            class="block w-full pl-11 pr-12 py-3 bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200"
                            required placeholder="••••••••" />

                        <!-- Toggle Eye Icon -->
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-primary transition-colors focus:outline-none">
                            <!-- Icon Mata Terbuka (Default disembunyikan) -->
                            <svg id="eye-open" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Icon Mata Tertutup -->
                            <svg id="eye-closed" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember & Forgot Password -->
                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember"
                            class="w-4.5 h-4.5 rounded border-gray-300 text-primary focus:ring-primary/50 transition duration-200">
                        <span
                            class="ml-2 text-sm text-gray-600 font-medium group-hover:text-gray-900 transition-colors">Ingat
                            Saya</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-semibold text-primary hover:text-primary-dark hover:underline transition duration-200">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <!-- Button -->
                <x-primary-button
                    class="w-full flex justify-center py-3.5 px-4 mt-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 transform hover:-translate-y-0.5">
                    Log In Sistem
                </x-primary-button>
            </form>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <p class="text-center text-xs text-gray-400 font-medium">
                    © {{ date('Y') }} Kasir Pusat. All rights reserved.
                </p>
            </div>

        </div>
    </div>

    <!-- Script untuk Toggle Password -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            }
        }
    </script>
</x-guest-layout>
