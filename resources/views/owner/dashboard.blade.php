<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Dashboard Eksekutif Owner') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

      {{-- HEADER: FILTER (Tombol Log Stok Dihapus) --}}
      <div
        class="flex flex-col lg:flex-row lg:items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('owner.dashboard') }}" class="flex flex-wrap items-center gap-3">
          <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Dari:</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
              class="border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm px-3 py-2">
          </div>
          <div class="flex items-center gap-2">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Sampai:</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
              class="border-gray-200 rounded-xl shadow-sm focus:ring-primary focus:border-primary text-sm px-3 py-2">
          </div>
          <button type="submit"
            class="px-5 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">
            Terapkan Filter
          </button>

          @if (
              $startDate->format('Y-m-d') != \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') ||
                  $endDate->format('Y-m-d') != \Carbon\Carbon::now()->endOfDay()->format('Y-m-d'))
            <a href="{{ route('owner.dashboard') }}"
              class="text-xs text-red-500 hover:text-red-700 underline font-medium px-2">
              Reset ke Bulan Ini
            </a>
          @endif
        </form>
      </div>

      {{-- KOTAK RINGKASAN KEUANGAN & STOK --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Laporan Keuangan (POS + Kas) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
          <div class="absolute top-0 right-0 p-4 opacity-10">
            <svg class="w-16 h-16 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.64-2.25 1.64-1.74 0-2.1-.96-2.17-1.92H8c.11 1.71 1.35 2.85 2.9 3.22V19h2.34v-1.68c1.65-.27 2.88-1.28 2.88-2.91 0-2.23-1.89-2.92-3.81-3.27z" />
            </svg>
          </div>
          <p class="text-gray-500 text-sm font-medium">Pendapatan Transaksi POS</p>
          <h3 class="text-2xl font-black text-gray-800 mt-1">
            Rp {{ number_format($totalPendapatanPOS, 0, ',', '.') }}
          </h3>

          {{-- Breakdown Histori Kas --}}
          <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-center">
            <div>
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kas Masuk</p>
              <p class="text-xs font-black text-emerald-600 mt-0.5">+ Rp {{ number_format($uangMasukKas, 0, ',', '.') }}
              </p>
            </div>
            <div class="text-right">
              <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kas Keluar</p>
              <p class="text-xs font-black text-red-600 mt-0.5">- Rp {{ number_format($uangKeluarKas, 0, ',', '.') }}
              </p>
            </div>
          </div>
        </div>

        {{-- Total Transaksi --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
          <div class="absolute top-0 right-0 p-4 opacity-10">
            <svg class="w-16 h-16 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
            </svg>
          </div>
          <p class="text-gray-500 text-sm font-medium">Total Transaksi Lunas</p>
          <h3 class="text-2xl font-black text-gray-800 mt-1">
            {{ number_format($totalTransaksi, 0, ',', '.') }} <span
              class="text-sm font-medium text-gray-400">struk</span>
          </h3>
          <p class="text-[10px] text-gray-400 mt-2 font-medium">Rerata Nilai Struk: Rp
            {{ $totalTransaksi > 0 ? number_format($totalPendapatanPOS / $totalTransaksi, 0, ',', '.') : 0 }}</p>
        </div>

        {{-- Status Stok Kritis --}}
        <div
          class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden {{ $stokKritis->count() > 0 ? 'border-red-200 bg-red-50/30' : '' }}">
          <p class="text-gray-500 text-sm font-medium">Peringatan Stok Kritis</p>
          <div class="flex items-center gap-3 mt-1">
            <h3 class="text-2xl font-black {{ $stokKritis->count() > 0 ? 'text-red-600' : 'text-gray-800' }}">
              {{ $stokKritis->count() }} <span class="text-sm font-medium text-gray-400">item</span>
            </h3>

            @if ($stokKritis->count() > 0)
              <span
                class="flex items-center gap-1.5 text-[10px] font-bold text-red-600 bg-red-100 px-2.5 py-1 rounded-full border border-red-200">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span> Butuh Restock
              </span>
            @else
              <span
                class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                Aman
              </span>
            @endif
          </div>
          <p class="text-[10px] text-gray-400 mt-2 font-medium">Bahan/Menu dengan stok fisik &le; 10</p>
        </div>
      </div>

      {{-- AREA ANALITIK PRODUK --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top 5 Terlaris --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 flex flex-col">
          <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-sm font-bold text-emerald-600 uppercase tracking-wider flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
              Top 5 Menu Laris
            </h3>
          </div>
          <div class="p-0 flex-1">
            <div class="overflow-x-auto custom-scrollbar">
              <table class="w-full text-left border-collapse">
                <tbody class="divide-y divide-gray-50">
                  @forelse($itemLaris as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                      <td class="px-5 py-4 w-12 text-center">
                        @if ($index == 0)
                          <span class="text-xl">🥇</span>
                        @elseif($index == 1)
                          <span class="text-xl">🥈</span>
                        @elseif($index == 2)
                          <span class="text-xl">🥉</span>
                        @else
                          <span class="text-gray-400 font-black text-sm">{{ $index + 1 }}</span>
                        @endif
                      </td>
                      <td class="px-5 py-4 font-bold text-gray-800 text-sm">{{ $item->nama }}</td>
                      <td class="px-5 py-4 text-right">
                        <span
                          class="bg-emerald-50 border border-emerald-100 px-3 py-1 rounded-full text-xs font-bold text-emerald-700 whitespace-nowrap">
                          {{ $item->total_terjual }} porsi
                        </span>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="px-6 py-12 text-center text-gray-400 text-sm font-medium">Belum ada menu
                        yang laku di periode ini.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Bottom 5 Kurang Laku --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100 flex flex-col">
          <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-sm font-bold text-red-500 uppercase tracking-wider flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
              </svg>
              5 Menu Paling Sepi
            </h3>
          </div>
          <div class="p-0 flex-1">
            <div class="overflow-x-auto custom-scrollbar">
              <table class="w-full text-left border-collapse">
                <tbody class="divide-y divide-gray-50">
                  @foreach ($itemTidakLaris as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                      <td class="px-5 py-4 w-12 text-center text-gray-400 font-black text-sm">{{ $index + 1 }}</td>
                      <td class="px-5 py-4 font-bold text-gray-800 text-sm">{{ $item->nama }}</td>
                      <td class="px-5 py-4 text-right">
                        @if ($item->total_terjual == 0)
                          <span
                            class="bg-red-50 border border-red-100 px-3 py-1 rounded-full text-xs font-bold text-red-600 whitespace-nowrap">0
                            porsi</span>
                        @else
                          <span
                            class="bg-gray-100 border border-gray-200 px-3 py-1 rounded-full text-xs font-bold text-gray-600 whitespace-nowrap">{{ $item->total_terjual }}
                            porsi</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- 4. AREA HISTORI STOK TERKINI (BAGIAN BARU DI BAWAH) --}}
      <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
          <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
            </path>
          </svg>
          <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Histori Mutasi Stok Terkini</h3>
        </div>
        <div class="p-0">
          <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-sm border-collapse">
              <thead>
                <tr
                  class="bg-gray-50/50 border-b border-gray-100 text-xs uppercase text-gray-400 font-bold tracking-wider">
                  <th class="px-6 py-3">Waktu</th>
                  <th class="px-6 py-3">Bahan / Item</th>
                  <th class="px-6 py-3 text-center">Mutasi</th>
                  <th class="px-6 py-3">Keterangan</th>
                  <th class="px-6 py-3">Pelaku</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                @forelse($histories as $history)
                  <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 text-gray-500 whitespace-nowrap">
                      {{ $history->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-6 py-3 font-bold text-gray-800">{{ $history->ingredient->nama ?? 'Bahan Terhapus' }}
                    </td>
                    <td class="px-6 py-3 text-center font-bold">
                      @if ($history->jumlah > 0)
                        <span
                          class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">+{{ $history->jumlah }}</span>
                      @else
                        <span class="text-red-600 bg-red-50 px-2 py-0.5 rounded">{{ $history->jumlah }}</span>
                      @endif
                    </td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $history->keterangan ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-700 font-medium">{{ $history->user->name ?? 'Sistem' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">Belum ada catatan
                      mutasi stok.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

  <style>
    .custom-scrollbar::-webkit-scrollbar {
      width: 5px;
      height: 5px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #e2e8f0;
      border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #cbd5e1;
    }
  </style>
</x-app-layout>
