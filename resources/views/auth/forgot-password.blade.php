<x-guest-layout>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-200 relative px-4 sm:px-6 lg:px-8">

        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-primary/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-dark/10 blur-3xl"></div>
        </div>

        <div x-data="whatsappForm()"
            class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 sm:p-10 relative z-10 border border-gray-100 text-center">

            <div
                class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 border-4 border-red-100 mb-6 text-red-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>

            <h2 class="text-2xl font-extrabold text-gray-900 mb-3 tracking-tight">Lupa Password?</h2>

            <p class="text-sm text-gray-500 mb-6 leading-relaxed font-medium">
                Reset password hanya dapat dilakukan oleh pusat. Masukkan nama/username Anda agar Admin bisa
                memprosesnya.
            </p>

            <div class="mb-8 text-left relative">
                <label for="username"
                    class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Username / Nama Kasir
                    <span class="text-red-500">*</span></label>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <input type="text" id="username" x-model="username"
                        class="block w-full pl-10 pr-3 py-3 rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-900 focus:bg-white focus:border-primary focus:ring-primary transition-all shadow-sm"
                        placeholder="Contoh: Budi, Kasir1..." required>
                </div>
            </div>

            <div class="space-y-4">

                <a :href="generateWaLink()" target="_blank"
                    :class="username.trim() === '' ? 'opacity-50 cursor-not-allowed pointer-events-none' :
                        'hover:-translate-y-0.5 shadow-md hover:bg-[#128C7E]'"
                    class="w-full flex items-center justify-center py-3.5 px-4 border border-transparent rounded-xl text-sm font-bold text-white bg-[#25D366] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#25D366] transition-all duration-300 transform">
                    <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    Kirim Permintaan ke Admin
                </a>

                <a href="{{ route('login') }}"
                    class="w-full flex items-center justify-center py-3.5 px-4 border-2 border-gray-200 rounded-xl shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all duration-300">
                    Kembali ke Halaman Login
                </a>
            </div>

            <p class="text-[10px] text-gray-400 font-medium mt-6">*Tombol WhatsApp akan aktif setelah Username diisi.
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('whatsappForm', () => ({
                username: '',
                waNumber: '6281225312127',
                generateWaLink() {
                    // Jika kosong, kembalikan javascript void agar link mati
                    if (this.username.trim() === '') return 'javascript:void(0)';

                    // Rangkai pesan dengan memasukkan username
                    let message =
                        `Halo Admin, saya lupa password akun sistem Kasir Pusat.\n\nUsername/Nama Kasir: *${this.username}*\n\nMohon bantuannya untuk melakukan reset password. Terima kasih.`;

                    // Ubah jadi URL format lalu gabung dengan nomor WA
                    return `https://wa.me/${this.waNumber}?text=${encodeURIComponent(message)}`;
                }
            }))
        })
    </script>
</x-guest-layout>
