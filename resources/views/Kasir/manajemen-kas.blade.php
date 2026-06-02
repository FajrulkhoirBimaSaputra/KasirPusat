<x-app-layout>
    <div class="max-w-4xl mx-auto p-4 sm:p-6">
        
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Kas</h1>
            <a href="{{ route('kasir.ringkasan') }}" class="text-gray-500 hover:text-gray-700 underline font-medium transition hover:text-primary">Kembali ke Ringkasan</a>
        </div>

        @if(session('success'))
            <div id="alert-success" class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- FORM INPUT KAS --}}
            <div class="md:col-span-1 bg-white rounded-xl shadow p-5 h-fit border border-gray-100">
                <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Catat Kas Baru</h3>
                
                {{-- Tambahkan novalidate agar popup default browser mati, dan id="formKas" untuk JS --}}
                <form id="formKas" action="{{ route('kasir.store-kas') }}" method="POST" novalidate>
                    @csrf
                    
                    {{-- Input Jenis --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <select id="jenis" name="jenis" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary shadow-sm transition outline-none">
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="pemasukan" {{ old('jenis') == 'pemasukan' ? 'selected' : '' }}>Pemasukan (Modal/Kembalian)</option>
                            <option value="pengeluaran" {{ old('jenis') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran (Beli Bahan, dll)</option>
                        </select>
                        {{-- Teks Error Kustom (Hidden by default) --}}
                        <p id="error-jenis" class="hidden text-red-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Jenis kas wajib dipilih!
                        </p>
                    </div>

                    {{-- Input Nominal --}}
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Nominal (Rp) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">Rp</span>
                            <input id="nominal" type="number" name="nominal" min="1" value="{{ old('nominal') }}"
                                class="w-full pl-10 rounded-lg border-gray-300 focus:border-primary focus:ring-primary shadow-sm transition outline-none" placeholder="0">
                        </div>
                        {{-- Teks Error Kustom (Hidden by default) --}}
                        <p id="error-nominal" class="hidden text-red-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Nominal uang wajib diisi!
                        </p>
                    </div>

                    {{-- Input Keterangan --}}
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="2" 
                            class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary shadow-sm transition outline-none" placeholder="Misal: Beli es batu, modal awal...">{{ old('keterangan') }}</textarea>
                        {{-- Teks Error Kustom (Hidden by default) --}}
                        <p id="error-keterangan" class="hidden text-red-500 text-xs mt-1.5 flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Keterangan wajib diisi!
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-4 rounded-lg shadow transition flex justify-center items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Simpan Data Kas
                    </button>
                </form>
            </div>

            {{-- TABEL RIWAYAT HARI INI --}}
            <div class="md:col-span-2 bg-white rounded-xl shadow p-5 border border-gray-100">
                <h3 class="font-bold text-lg mb-4 text-gray-800 border-b pb-2">Riwayat Kas Hari Ini</h3>
                
                @if($kasHariIni->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                        <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="font-medium">Belum ada catatan kas hari ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 text-sm">
                                    <th class="p-3 border-b font-semibold">Waktu</th>
                                    <th class="p-3 border-b font-semibold">Jenis</th>
                                    <th class="p-3 border-b font-semibold">Keterangan</th>
                                    <th class="p-3 border-b font-semibold text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($kasHariIni as $kas)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-3 text-sm text-gray-500 whitespace-nowrap">{{ $kas->created_at->format('H:i') }}</td>
                                        <td class="p-3">
                                            @if($kas->jenis == 'pemasukan')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs bg-green-100 text-green-700 rounded-full font-bold">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                                    Masuk
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs bg-red-100 text-red-700 rounded-full font-bold">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                                    Keluar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-sm text-gray-700">{{ $kas->keterangan }}</td>
                                        <td class="p-3 text-right font-bold {{ $kas->jenis == 'pemasukan' ? 'text-green-600' : 'text-red-600' }} whitespace-nowrap">
                                            {{ $kas->jenis == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($kas->nominal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- SCRIPT VALIDASI CUSTOM --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formKas');
            const fields = ['jenis', 'nominal', 'keterangan'];

            form.addEventListener('submit', function(e) {
                let isValid = true;

                fields.forEach(field => {
                    const input = document.getElementById(field);
                    const errorText = document.getElementById('error-' + field);

                    // Cek jika kosong
                    if (!input.value.trim()) {
                        // Tambahkan border merah muda dan background merah super tipis
                        input.classList.add('border-red-500', 'ring-2', 'ring-red-100', 'bg-red-50');
                        input.classList.remove('focus:border-primary', 'focus:ring-primary', 'border-gray-300');
                        
                        // Munculkan tulisan error
                        errorText.classList.remove('hidden');
                        isValid = false;
                    }
                });

                // Jika ada yang kosong, hentikan form agar tidak ke-refresh
                if (!isValid) {
                    e.preventDefault(); 
                }
            });

            // Script untuk menghilangkan efek merah saat user mulai mengetik/memilih lagi
            fields.forEach(field => {
                const input = document.getElementById(field);
                const errorText = document.getElementById('error-' + field);

                input.addEventListener('input', function() {
                    input.classList.remove('border-red-500', 'ring-2', 'ring-red-100', 'bg-red-50');
                    input.classList.add('focus:border-primary', 'focus:ring-primary', 'border-gray-300');
                    errorText.classList.add('hidden');
                });
            });

            // Notifikasi sukses hilang otomatis dalam 3 detik
            setTimeout(() => {
                const alert = document.getElementById('alert-success');
                if(alert){
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 500);
                }
            }, 3000);
        });
    </script>
</x-app-layout>