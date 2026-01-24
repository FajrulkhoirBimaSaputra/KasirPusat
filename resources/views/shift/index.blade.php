<x-app-layout>
    <div class="max-w-7xl mx-auto p-6 bg-white rounded-xl shadow">

        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex gap-2">
                <a href="?month={{ $date->copy()->subMonth()->format('Y-m') }}"
                    class="px-3 py-2 bg-slate-700 text-white rounded-lg">‹</a>

                <a href="?month={{ now()->format('Y-m') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm font-semibold">
                    Hari Ini
                </a>

                <a href="?month={{ $date->copy()->addMonth()->format('Y-m') }}"
                    class="px-3 py-2 bg-slate-700 text-white rounded-lg">›</a>
            </div>

            <h2 class="text-2xl font-semibold">
                {{ $date->translatedFormat('F Y') }}
            </h2>
        </div>

        <form method="POST" action="{{ route('shift.store') }}">
            @csrf

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
                                            $isToday = $day->isToday();
                                        @endphp


                                        <div data-date="{{ $tanggal }}" class="calendar-cell h-32 border cursor-pointer relative
                         {{ !$inMonth ? 'bg-gray-100 text-gray-400' : 'bg-white' }}
                         {{ $shift ? 'bg-red-200' : '' }}
                         {{ $isToday ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}">

                                            <span class="absolute top-2 right-2 text-sm font-semibold">
                                                {{ $day->day }}
                                            </span>

                                            @if($shift)
                                                <div class="absolute bottom-2 left-2 right-2 text-xs bg-red-500 text-white rounded px-2 py-1">
                                                    {{ $shift->user->name }}
                                                </div>
                                            @endif
                                        </div>
                    @endforeach
                </div>
            </div>

            {{-- HIDDEN INPUT --}}
            <div id="shift-inputs"></div>

            <div class="mt-6 flex justify-end">
                <button onclick="submitShift(event)" class="px-8 py-3 bg-primary text-white rounded-xl font-bold">
                    Simpan Jadwal Shift
                </button>
            </div>
        </form>
    </div>

    <script>
        const selectedDates = new Set();

        document.querySelectorAll('.calendar-cell').forEach(cell => {
            cell.addEventListener('click', () => {
                const date = cell.dataset.date;

                if (cell.classList.contains('bg-blue-200')) {
                    cell.classList.remove('bg-blue-200');
                    selectedDates.delete(date);
                } else {
                    cell.classList.add('bg-blue-200');
                    selectedDates.add(date);
                }
            });
        });

        function submitShift(e) {
            e.preventDefault();

            const kasir = document.getElementById('kasir').value;
            if (!kasir || selectedDates.size === 0) {
                alert('Pilih kasir dan tanggal');
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
    </script>
</x-app-layout>