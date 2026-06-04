<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Tambah Pengguna Baru
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-2">

        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <p class="text-sm text-gray-500 font-medium">Lengkapi form di bawah ini untuk menambahkan akses admin atau
                kasir ke dalam sistem.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="name" value="Nama Lengkap" class="text-gray-700 font-semibold mb-1" />
                    <x-text-input id="name"
                        class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring-primary/20 rounded-xl transition duration-200"
                        type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                        placeholder="Contoh: Bima" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="username" value="Username" class="text-gray-700 font-semibold mb-1" />
                    <x-text-input id="username"
                        class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring-primary/20 rounded-xl transition duration-200"
                        type="text" name="username" :value="old('username')" required autocomplete="username"
                        placeholder="Contoh: bima_kasir" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="role" value="Hak Akses (Role)" class="text-gray-700 font-semibold mb-1" />
                <select id="role" name="role"
                    class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring-primary/20 rounded-xl shadow-sm transition duration-200 text-gray-700">
                    <option value="kasir">Kasir</option>
                    <option value="admin">Admin</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Pengaturan Password</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <x-input-label for="password" value="Password" class="text-gray-700 font-semibold mb-1" />
                        <div class="relative">
                            <x-text-input id="password"
                                class="block w-full pr-12 bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring-primary/20 rounded-xl transition duration-200"
                                type="password" name="password" required autocomplete="new-password"
                                placeholder="Minimal 8 karakter" />

                            <button type="button" onclick="togglePassword('password', 'eye-open-1', 'eye-closed-1')"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-primary transition-colors focus:outline-none">
                                <svg id="eye-open-1" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-closed-1" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password"
                            class="text-gray-700 font-semibold mb-1" />
                        <div class="relative">
                            <x-text-input id="password_confirmation"
                                class="block w-full pr-12 bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring-primary/20 rounded-xl transition duration-200"
                                type="password" name="password_confirmation" required autocomplete="new-password"
                                placeholder="Ketik ulang password" />

                            <button type="button"
                                onclick="togglePassword('password_confirmation', 'eye-open-2', 'eye-closed-2')"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-primary transition-colors focus:outline-none">
                                <svg id="eye-open-2" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-closed-2" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 gap-3">
                <a href="{{ route('admin.users.index') }}"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">
                    Batal
                </a>

                <x-primary-button
                    class="py-2.5 px-6 rounded-xl shadow-md text-sm font-bold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Simpan Pengguna
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId, openIconId, closedIconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeOpen = document.getElementById(openIconId);
            const eyeClosed = document.getElementById(closedIconId);

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
</x-app-layout>
