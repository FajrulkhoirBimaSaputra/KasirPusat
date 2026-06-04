<x-app-layout>
    <!-- Header Halaman -->
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <!-- Icon User Edit -->
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Edit Data Pengguna
            </h2>
        </div>
    </x-slot>

    <!-- Kontainer Form -->
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-2">

        <!-- Deskripsi Singkat -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <p class="text-sm text-gray-500 font-medium">Perbarui informasi profil pengguna atau ubah hak aksesnya.
                Kosongkan kolom password jika tidak ingin mengubahnya.</p>
        </div>

        {{-- Error Validation Summary --}}
        @if ($errors->any())
            <div class="mx-6 mt-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <span class="font-bold text-sm">Gagal menyimpan data:</span>
                    <ul class="text-sm list-disc list-inside mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Grid 2 Kolom untuk Nama & Username -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1 text-sm">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200">
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-1 text-sm">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                        class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200">
                </div>
            </div>

            <!-- Role (Full Width) -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1 text-sm">Hak Akses (Role)</label>
                <select name="role" required
                    class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200 text-gray-700">
                    <option value="kasir" @selected(old('role', $user->role) === 'kasir')>Kasir</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                </select>
            </div>

            <!-- Garis Pemisah untuk Password -->
            <div class="border-t border-gray-100 pt-6 mt-6">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Ubah Password <span
                            class="text-xs font-normal lowercase">(Opsional)</span></h3>
                    <p class="text-xs text-gray-500 mt-1">Biarkan kolom di bawah ini kosong jika Anda tidak ingin
                        mengubah password akun ini.</p>
                </div>

                <!-- Grid 2 Kolom untuk Password -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Password Baru -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1 text-sm">Password Baru</label>
                        <div class="relative">
                            <input id="password" type="password" name="password"
                                class="block w-full pr-12 bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200"
                                placeholder="Minimal 8 karakter">

                            <!-- Toggle Eye Icon -->
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
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1 text-sm">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="block w-full pr-12 bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200"
                                placeholder="Ketik ulang password baru">

                            <!-- Toggle Eye Icon -->
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
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end pt-4 gap-3 border-t border-gray-50 mt-6">
                <!-- Tombol Batal -->
                <a href="{{ route('admin.users.index') }}"
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors">
                    Batal
                </a>

                <!-- Tombol Simpan -->
                <button type="submit"
                    class="flex items-center justify-center py-2.5 px-6 rounded-xl shadow-md text-sm font-bold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Script untuk Toggle Password -->
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
