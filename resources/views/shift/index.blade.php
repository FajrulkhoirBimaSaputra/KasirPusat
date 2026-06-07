<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Jadwal Shift Kasir
            </h2>
        </div>
    </x-slot>

    {{-- CONTAINER UNTUK TOAST NOTIFICATION VALIDASI JS --}}
    <div id="toast-container"
        class="fixed top-5 left-1/2 transform -translate-x-1/2 z-[100] flex flex-col gap-2 pointer-events-none"></div>

    <div>

        {{-- ALERT SUCCESS SUBMIT PHP BEBAS REFRESH --}}
        @if (session('success'))
            <div id="alert-success"
                class="mb-6 flex items-center p-4 text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 shadow-sm transition-all duration-500"
                role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <div class="text-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">

            {{-- NAVIGASI BULAN --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-100">
                    <a href="?month={{ $date->copy()->subMonth()->format('Y-m') }}"
                        class="p-2 text-gray-500 hover:text-primary hover:bg-white rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <a href="?month={{ now()->format('Y-m') }}"
                        class="px-4 py-2 bg-white text-gray-800 rounded-lg shadow-sm text-sm font-bold tracking-wide border border-gray-100 hover:text-primary transition-colors">
                        {{ $date->translatedFormat('F') }}
                    </a>

                    <a href="?month={{ $date->copy()->addMonth()->format('Y-m') }}"
                        class="p-2 text-gray-500 hover:text-primary hover:bg-white rounded-lg transition-all focus:outline-none focus:ring-2 focus:ring-primary/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    {{ $date->translatedFormat('Y') }}
                </h2>
            </div>

            <form method="POST" action="{{ route('shift.store') }}">
                @csrf

                {{-- PEMETAAN WARNA KASIR --}}
                @php
                    $colorPalettes = [
                        ['bg' => 'bg-blue-100', 'badge' => 'bg-blue-500', 'text' => 'text-white'],
                        ['bg' => 'bg-emerald-100', 'badge' => 'bg-emerald-500', 'text' => 'text-white'],
                        ['bg' => 'bg-amber-100', 'badge' => 'bg-amber-500', 'text' => 'text-white'],
                        ['bg' => 'bg-purple-100', 'badge' => 'bg-purple-500', 'text' => 'text-white'],
                        ['bg' => 'bg-rose-100', 'badge' => 'bg-rose-500', 'text' => 'text-white'],
                        ['bg' => 'bg-cyan-100', 'badge' => 'bg-cyan-500', 'text' => 'text-white'],
                        ['bg' => 'bg-indigo-100', 'badge' => 'bg-indigo-500', 'text' => 'text-white'],
                        ['bg' => 'bg-fuchsia-100', 'badge' => 'bg-fuchsia-500', 'text' => 'text-white'],
                    ];

                    $userColors = [];
                    $colorIndex = 0;
                    foreach ($kasirs as $kasir) {
                        $userColors[$kasir->id] = $colorPalettes[$colorIndex % count($colorPalettes)];
                        $colorIndex++;
                    }
                @endphp

                {{-- TOP CONTROLS: PILIH KASIR & LEGENDA WARNA --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-6">

                    {{-- Input Pilih Kasir --}}
                    <div class="w-full md:w-64">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Pilih Kasir untuk di-assign:
                        </label>
                        <select id="kasir"
                            class="block w-full bg-gray-50 border-gray-200 focus:bg-white focus:border-primary focus:ring focus:ring-primary/20 rounded-xl transition duration-200 font-medium text-gray-700">
                            <option value="">-- Pilih Kasir --</option>
                            @foreach ($kasirs as $kasir)
                                <option value="{{ $kasir->id }}">{{ $kasir->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Legenda Warna --}}
                    <div
                        class="flex-1 bg-gray-50 p-3 rounded-xl border border-gray-100 flex flex-wrap items-center gap-3">
                        <span class="text-xs font-bold text-gray-500 tracking-wider mr-2">Mark:</span>
                        @foreach ($kasirs as $kasir)
                            @php $c = $userColors[$kasir->id]; @endphp
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full {{ $c['badge'] }}"></span>
                                <span class="text-xs font-semibold text-gray-700">{{ $kasir->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- GRID KALENDER --}}
                <div class="border border-gray-200 rounded-2xl overflow-hidden shadow-sm">

                    {{-- HEADER HARI --}}
                    <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                        @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                            <div class="text-center font-bold text-xs text-gray-500 uppercase tracking-wider py-3">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    {{-- TANGGAL --}}
                    <div class="grid grid-cols-7 bg-gray-200 gap-[1px]">
                        @foreach ($period as $day)
                            @php
                                $tanggal = $day->format('Y-m-d');
                                $shift = $shifts[$tanggal] ?? null;
                                $inMonth = $day->month === $date->month;

                                $isToday = $tanggal === now()->format('Y-m-d');
                                $isPast = $tanggal < now()->format('Y-m-d');

                                // Default Colors
                                $cellBg = $inMonth ? 'bg-white' : 'bg-gray-50/50 text-gray-400';
                                $badgeBg = '';
                                $badgeText = '';

                                if ($shift) {
                                    $shiftColor = $userColors[$shift->user->id] ?? [
                                        'bg' => 'bg-gray-200',
                                        'badge' => 'bg-gray-500',
                                        'text' => 'text-white',
                                    ];
                                    $cellBg = $shiftColor['bg'];
                                    $badgeBg = $shiftColor['badge'];
                                    $badgeText = $shiftColor['text'];
                                }

                                // Modifikasi Past Day
                                $pastClasses = '';
                                if ($isPast) {
                                    $pastClasses = 'opacity-50 pointer-events-none disabled-cell';
                                    if (!$shift) {
                                        $cellBg = 'bg-gray-100';
                                    } else {
                                        $pastClasses .= ' grayscale';
                                    }
                                }
                            @endphp

                            <div data-date="{{ $tanggal }}"
                                class="calendar-cell h-28 sm:h-32 relative transition-all duration-200 ease-in-out {{ $cellBg }} {{ $pastClasses }}
                                {{ $isToday ? 'ring-2 ring-inset ring-primary z-10' : '' }}
                                {{ !$isPast && $inMonth ? 'cursor-pointer hover:bg-gray-50' : '' }}">
                                @if ($isToday)
                                    <div
                                        class="absolute top-2 left-2 bg-primary text-white text-[9px] font-bold uppercase px-1.5 py-0.5 rounded shadow-sm hidden sm:block">
                                        Hari Ini
                                    </div>
                                @endif

                                <span
                                    class="absolute top-2 right-2 text-sm font-bold {{ $isToday ? 'text-primary' : 'text-gray-700' }} {{ !$inMonth ? 'text-opacity-40' : '' }}">
                                    {{ $day->day }}
                                </span>

                                @if ($shift)
                                    <div
                                        class="absolute bottom-2 left-2 right-2 text-[10px] sm:text-xs {{ $badgeBg }} {{ $badgeText }} font-bold rounded-lg px-2 py-1.5 text-center truncate shadow-sm">
                                        {{ $shift->user->name }}
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>
                </div>

                <p class="text-xs text-gray-500 mt-3 font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Klik kotak pada kalender (hari yang belum lewat) untuk menandai jadwal, lalu klik "Simpan Jadwal
                    Shift".
                </p>

                {{-- INPUT SHIFT --}}
                <div id="shift-inputs"></div>

                {{-- TOMBOL SUBMIT --}}
                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                    <button onclick="submitShift(event)"
                        class="flex items-center justify-center py-3 px-8 rounded-xl shadow-md text-sm font-bold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-300 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Jadwal Shift
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- STYLE SELECTED MENGGUNAKAN WARNA PRIMARY --}}
    <style>
        .calendar-cell.selected {
            background-color: rgb(var(--color-primary) / 0.1) !important;
            box-shadow: inset 0 0 0 2px rgb(var(--color-primary)) !important;
            z-index: 20;
        }
    </style>

    <script>
        // FUNGSI UNTUK MENAMPILKAN TOAST NOTIFICATION MODERN (MENGGANTIKAN ALERT)
        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            toast.className =
                `transform transition-all duration-300 -translate-y-10 opacity-0 flex items-center p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl shadow-lg pointer-events-auto`;
            toast.innerHTML = `
                <svg class="w-6 h-6 mr-3 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold text-sm">${message}</span>
            `;

            container.appendChild(toast);

            // Muncul turun
            setTimeout(() => {
                toast.classList.remove('-translate-y-10', 'opacity-0');
            }, 10);

            // Hilang naik & hapus
            setTimeout(() => {
                toast.classList.add('-translate-y-10', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3500);
        }

        const selectedDates = new Set();

        document.querySelectorAll('.calendar-cell').forEach(cell => {
            cell.addEventListener('click', () => {

                // Mencegah klik pada hari yang sudah lewat
                if (cell.classList.contains('disabled-cell')) return;

                const date = cell.dataset.date;

                if (selectedDates.has(date)) {
                    cell.classList.remove('selected');
                    selectedDates.delete(date);
                } else {
                    cell.classList.add('selected');
                    selectedDates.add(date);
                }
            });
        });

        function submitShift(e) {
            e.preventDefault();
            const kasir = document.getElementById('kasir').value;

            // Validasi menggunakan Toast Custom (Bukan Alert)
            if (!kasir) {
                showToast('Silakan pilih nama kasir terlebih dahulu!');
                document.getElementById('kasir').focus();
                return;
            }

            if (selectedDates.size === 0) {
                showToast('Pilih minimal satu tanggal pada kalender!');
                return;
            }

            const box = document.getElementById('shift-inputs');
            box.innerHTML = '';

            selectedDates.forEach(date => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `shifts[${date}]`;
                input.value = kasir;
                box.appendChild(input);
            });

            e.target.closest('form').submit();
        }

        {{-- ALERT SUCCESS PHP (BEKAS SAVE) MENGHILANG OTOMATIS --}}
        setTimeout(() => {
            const alert = document.getElementById('alert-success');
            if (alert) {
                alert.classList.add('opacity-0', '-translate-y-4'); // Efek fade out + geser ke atas
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);
    </script>
</x-app-layout>
