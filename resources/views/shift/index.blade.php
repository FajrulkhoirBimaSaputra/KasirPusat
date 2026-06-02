<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 bg-white rounded-xl shadow">

        {{-- ALERT SUCCESS --}}
        @if(session('success'))
            <div id="alert-success"
                class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex gap-2">
                <a href="?month={{ $date->copy()->subMonth()->format('Y-m') }}"
                    class="px-3 py-2 bg-slate-700 text-white rounded-lg">‹</a>

                <a href="?month={{ now()->format('Y-m') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm font-semibold">
                    {{ $date->translatedFormat('F') }}
                </a>

                <a href="?month={{ $date->copy()->addMonth()->format('Y-m') }}"
                    class="px-3 py-2 bg-slate-700 text-white rounded-lg">›</a>
            </div>

            <h2 class="text-2xl font-semibold">
                {{ $date->translatedFormat('Y') }}
            </h2>
        </div>

        <form method="POST" action="{{ route('shift.store') }}">
            @csrf

            {{-- PEMETAAN WARNA KASIR --}}
            @php
                $colorPalettes = [
                    ['bg' => 'bg-blue-200',   'badge' => 'bg-blue-600',   'text' => 'text-white'],
                    ['bg' => 'bg-green-200',  'badge' => 'bg-green-600',  'text' => 'text-white'],
                    ['bg' => 'bg-yellow-200', 'badge' => 'bg-yellow-500', 'text' => 'text-gray-900'],
                    ['bg' => 'bg-purple-200', 'badge' => 'bg-purple-600', 'text' => 'text-white'],
                    ['bg' => 'bg-pink-200',   'badge' => 'bg-pink-600',   'text' => 'text-white'],
                    ['bg' => 'bg-orange-200', 'badge' => 'bg-orange-600', 'text' => 'text-white'],
                    ['bg' => 'bg-teal-200',   'badge' => 'bg-teal-600',   'text' => 'text-white'],
                    ['bg' => 'bg-indigo-200', 'badge' => 'bg-indigo-600', 'text' => 'text-white'],
                ];

                $userColors = [];
                $colorIndex = 0;
                foreach($kasirs as $kasir) {
                    $userColors[$kasir->id] = $colorPalettes[$colorIndex % count($colorPalettes)];
                    $colorIndex++;
                }
            @endphp

            {{-- PILIH KASIR --}}
            <div class="max-w-xs mb-4">
                <label class="text-sm font-semibold mb-1 block">
                    Pilih Kasir
                </label>
                <select id="kasir" class="w-full rounded-lg border-gray-300">
                    <option value="">-- Pilih Kasir --</option>
                    @foreach($kasirs as $kasir)
                        <option value="{{ $kasir->id }}">{{ $kasir->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- GRID KALENDER --}}
            <div class="border border-gray-300 rounded overflow-hidden">

                {{-- HEADER HARI --}}
                <div class="grid grid-cols-7 border-b">
                    @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $day)
                        <div class="text-center font-semibold py-2">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                {{-- TANGGAL --}}
                <div class="grid grid-cols-7">
                    @foreach($period as $day)

                        @php
                            $tanggal = $day->format('Y-m-d');
                            $shift = $shifts[$tanggal] ?? null;
                            $inMonth = $day->month === $date->month;
                            
                            // Menggunakan perbandingan string format Y-m-d agar lebih akurat (tidak terpengaruh jam)
                            $isToday = $tanggal === now()->format('Y-m-d');
                            $isPast = $tanggal < now()->format('Y-m-d');

                            // Warna Background Default
                            $cellBg = $inMonth ? 'bg-white' : 'bg-gray-50 text-gray-300';
                            $badgeBg = '';
                            $badgeText = '';
                            
                            if ($shift) {
                                $shiftColor = $userColors[$shift->user->id] ?? ['bg' => 'bg-red-200', 'badge' => 'bg-red-500', 'text' => 'text-white'];
                                $cellBg = $shiftColor['bg'];
                                $badgeBg = $shiftColor['badge'];
                                $badgeText = $shiftColor['text'];
                            }

                            // Modifikasi jika hari sudah lewat (Past Day)
                            $pastClasses = '';
                            if ($isPast) {
                                // Buat transparan dan matikan event klik
                                $pastClasses = 'opacity-60 pointer-events-none disabled-cell ';
                                
                                if (!$shift) {
                                    $cellBg = 'bg-gray-200'; // Abu-abu gelap jika kosong
                                } else {
                                    $pastClasses .= 'grayscale'; // Jadikan warna shift menjadi abu-abu jika sudah lewat
                                }
                            }
                        @endphp

                        <div 
                            data-date="{{ $tanggal }}"
                            class="calendar-cell h-32 border relative transition {{ $cellBg }} {{ $pastClasses }}
                            {{ $isToday ? 'ring-2 ring-blue-500 z-10' : '' }}
                            {{ !$isPast && $inMonth ? 'cursor-pointer hover:brightness-95' : '' }}"
                        >

                            <span class="absolute top-2 right-2 text-sm font-semibold">
                                {{ $day->day }}
                            </span>

                            @if($shift)
                                <div class="absolute bottom-2 left-2 right-2 text-xs {{ $badgeBg }} {{ $badgeText }} font-medium rounded px-2 py-1 text-center truncate">
                                    {{ $shift->user->name }}
                                </div>
                            @endif

                        </div>

                    @endforeach
                </div>
            </div>

            {{-- INPUT SHIFT --}}
            <div id="shift-inputs"></div>

            <div class="mt-6 flex justify-end">
                <button onclick="submitShift(event)"
                    class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">
                    Simpan Jadwal Shift
                </button>
            </div>

        </form>
    </div>

    {{-- STYLE SELECTED --}}
    <style>
        .calendar-cell.selected {
            background-color: #bfdbfe !important;
        }
    </style>

    <script>

        const selectedDates = new Set();

        document.querySelectorAll('.calendar-cell').forEach(cell => {

            cell.addEventListener('click', () => {

                // Mencegah klik pada hari yang sudah lewat
                if(cell.classList.contains('disabled-cell')) return;

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

            if (!kasir || selectedDates.size === 0) {
                alert('Pilih kasir dan tanggal terlebih dahulu');
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

        {{-- ALERT HILANG OTOMATIS --}}
        setTimeout(() => {
            const alert = document.getElementById('alert-success');
            if(alert){
                alert.style.display = 'none';
            }
        }, 3000);

    </script>
</x-app-layout>