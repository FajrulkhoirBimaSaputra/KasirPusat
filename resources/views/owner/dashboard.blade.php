<x-app-layout>
  <x-slot name="header">
    <h2 class="font-bold text-xl text-gray-800 leading-tight">
      Dashboard Eksekutif Owner
    </h2>
  </x-slot>

  <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

    {{-- FILTER TANGGAL --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
      <form method="GET" action="{{ route('owner.dashboard') }}" class="flex flex-wrap items-center gap-4">
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
          class="px-6 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">
          Terapkan Filter
        </button>
        @if ($startDate->format('Y-m-d') != \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'))
          <a href="{{ route('owner.dashboard') }}"
            class="text-xs text-red-500 hover:text-red-700 underline font-medium">Reset ke Bulan Ini</a>
        @endif
      </form>
    </div>

    {{-- KARTU KEUANGAN MENDETAIL --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- Kartu Pemasukan --}}
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-emerald-500">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Pemasukan</p>
            <h3 class="text-3xl font-black text-emerald-600 mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </h3>
          </div>
          <div class="p-3 bg-emerald-50 rounded-xl text-emerald-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
          </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-4">
          <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase">Dari Transaksi Kasir (POS)</p>
            <p class="text-sm font-bold text-gray-800">Rp {{ number_format($totalPendapatanPOS, 0, ',', '.') }}</p>
          </div>
          <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase">Dari Kas Masuk Ekstra</p>
            <p class="text-sm font-bold text-gray-800">Rp {{ number_format($uangMasukKas, 0, ',', '.') }}</p>
          </div>
        </div>
      </div>

      {{-- Kartu Pengeluaran & Laba --}}
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-red-500">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Pengeluaran Kas</p>
            <h3 class="text-3xl font-black text-red-600 mt-1">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </h3>
          </div>
          <div class="p-3 bg-red-50 rounded-xl text-red-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
          </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100">
          <p class="text-[10px] font-bold text-gray-400 uppercase">Estimasi Arus Kas Bersih (Pemasukan - Pengeluaran)
          </p>
          <p
            class="text-lg font-black {{ $totalPemasukan - $totalPengeluaran >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
            Rp {{ number_format($totalPemasukan - $totalPengeluaran, 0, ',', '.') }}
          </p>
        </div>
      </div>
    </div>

    {{-- GRID 2 KOLOM: TOP MENU & BOTTOM MENU --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Top Menu --}}
      <div class="bg-white shadow-sm rounded-2xl border border-gray-100">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
          <h3 class="text-sm font-bold text-emerald-600 uppercase tracking-wider">Top 5 Menu Laris</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
          <table class="w-full text-left text-sm">
            <tbody class="divide-y divide-gray-50">
              @forelse($itemLaris as $index => $item)
                <tr class="hover:bg-gray-50">
                  <td class="px-5 py-3 w-10 text-center font-black text-gray-400">{{ $index + 1 }}</td>
                  <td class="px-5 py-3 font-bold text-gray-800">{{ $item->nama }}</td>
                  <td class="px-5 py-3 text-right"><span
                      class="bg-emerald-50 text-emerald-700 px-2 py-1 rounded text-xs font-bold">{{ $item->total_terjual }}
                      porsi</span></td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="px-5 py-6 text-center text-gray-400">Data belum tersedia.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      {{-- Bottom Menu --}}
      <div class="bg-white shadow-sm rounded-2xl border border-gray-100">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
          <h3 class="text-sm font-bold text-red-500 uppercase tracking-wider">5 Menu Kurang Laku</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar">
          <table class="w-full text-left text-sm">
            <tbody class="divide-y divide-gray-50">
              @foreach ($itemTidakLaris as $index => $item)
                <tr class="hover:bg-gray-50">
                  <td class="px-5 py-3 w-10 text-center font-black text-gray-400">{{ $index + 1 }}</td>
                  <td class="px-5 py-3 font-bold text-gray-800">{{ $item->nama }}</td>
                  <td class="px-5 py-3 text-right"><span
                      class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">{{ $item->total_terjual }}
                      porsi</span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- TABEL 1: RIWAYAT TRANSAKSI HARIAN --}}
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
      <div class="p-5 border-b border-gray-100 bg-gray-50">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Riwayat Transaksi (Pemasukan & Pengeluaran
          Harian)</h3>
      </div>
      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-sm border-collapse">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-bold">
              <th class="px-6 py-4">Tanggal</th>
              <th class="px-6 py-4 text-right">Penjualan POS</th>
              <th class="px-6 py-4 text-right">Kas Masuk Ekstra</th>
              <th class="px-6 py-4 text-right text-red-600">Total Pengeluaran</th>
              <th class="px-6 py-4 text-right">Arus Bersih</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($riwayatHarianPaginated as $row)
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-bold text-gray-800">
                  {{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d F Y') }}</td>
                <td class="px-6 py-4 text-right text-emerald-600 font-medium">Rp
                  {{ number_format($row['pemasukan_pos'], 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-right text-emerald-600 font-medium">Rp
                  {{ number_format($row['pemasukan_kas'], 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-right text-red-600 font-bold">Rp
                  {{ number_format($row['total_pengeluaran'], 0, ',', '.') }}</td>
                <td
                  class="px-6 py-4 text-right font-black {{ $row['laba_bersih'] >= 0 ? 'text-gray-800' : 'text-red-600' }}">
                  Rp {{ number_format($row['laba_bersih'], 0, ',', '.') }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada riwayat transaksi pada rentang
                  tanggal ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{-- Pagination Riwayat --}}
      @if ($riwayatHarianPaginated->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
          {{ $riwayatHarianPaginated->links() }}
        </div>
      @endif
    </div>

    {{-- GRID 2 KOLOM: TABEL STOK & TABEL MUTASI --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      {{-- TABEL 2: STOK MAKANAN --}}
      <div class="bg-white shadow-sm rounded-2xl border border-gray-100 flex flex-col">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
          <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Ketersediaan Stok Makanan</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar flex-1">
          <table class="w-full text-left text-sm border-collapse">
            <thead class="sticky top-0 bg-white shadow-sm">
              <tr class="border-b border-gray-100 text-xs uppercase text-gray-500 font-bold">
                <th class="px-5 py-3">Nama Makanan (Bahan)</th>
                <th class="px-5 py-3 text-right">Sisa Stok</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              @forelse($stokMakanan as $stok)
                <tr class="hover:bg-gray-50">
                  <td class="px-5 py-3 font-bold text-gray-800">{{ $stok->nama }}</td>
                  <td class="px-5 py-3 text-right">
                    @if ($stok->stok <= 10)
                      <span
                        class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-bold border border-red-200 animate-pulse">Sisa
                        {{ $stok->stok }} (Kritis)</span>
                    @else
                      <span
                        class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full text-xs font-bold">{{ $stok->stok }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="px-5 py-6 text-center text-gray-400">Data stok makanan kosong.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        {{-- Pagination Stok --}}
        @if ($stokMakanan->hasPages())
          <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $stokMakanan->links() }}
          </div>
        @endif
      </div>

      {{-- TABEL 3: LOG MUTASI STOK --}}
      <div class="bg-white shadow-sm rounded-2xl border border-gray-100 flex flex-col">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
          <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Log Mutasi Stok Terakhir</h3>
        </div>
        <div class="overflow-x-auto custom-scrollbar flex-1">
          <table class="w-full text-left text-sm border-collapse">
            <thead class="sticky top-0 bg-white shadow-sm z-10">
              <tr class="border-b border-gray-100 text-[10px] uppercase text-gray-400 font-black tracking-wider">
                <th class="px-5 py-4">Bahan / Makanan</th>
                <th class="px-5 py-4 text-center">Total Mutasi</th>
                <th class="px-5 py-4 text-left">Diubah Oleh</th>
                <th class="px-5 py-4 text-right">Waktu</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50/80">
              @forelse($histories as $history)
                <tr class="hover:bg-gray-50/80 transition-colors duration-200 group">

                  {{-- Kolom Nama Bahan & Info Stok (Awal -> Akhir) --}}
                  <td class="px-5 py-3.5">
                    <p class="font-bold text-gray-800 group-hover:text-primary transition-colors">
                      {{ $history->ingredient->nama ?? 'Dihapus' }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-0.5 text-[10px] font-medium text-gray-400">
                      <span>Stok: <b class="text-gray-500">{{ $history->old_stok }}</b></span>
                      <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                      </svg>
                      <b class="text-gray-900">{{ $history->new_stok }}</b>
                    </div>
                  </td>

                  {{-- Kolom Badge Mutasi (+/-) --}}
                  <td class="px-5 py-3.5 text-center">
                    @if ($history->difference > 0)
                      <span
                        class="inline-flex items-center justify-center min-w-[3rem] text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-md text-xs font-black shadow-sm">
                        +{{ $history->difference }}
                      </span>
                    @elseif ($history->difference < 0)
                      <span
                        class="inline-flex items-center justify-center min-w-[3rem] text-red-700 bg-red-50 border border-red-100 px-2 py-1 rounded-md text-xs font-black shadow-sm">
                        {{ $history->difference }}
                      </span>
                    @else
                      <span
                        class="inline-flex items-center justify-center min-w-[3rem] text-gray-700 bg-gray-50 border border-gray-200 px-2 py-1 rounded-md text-xs font-black shadow-sm">
                        0
                      </span>
                    @endif
                  </td>

                  {{-- Kolom User dengan Avatar Initial --}}
                  <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2.5">
                      <span
                        class="text-xs font-bold text-gray-700">{{ $history->user->name ?? 'Tidak Diketahui' }}</span>
                    </div>
                  </td>

                  {{-- Kolom Tanggal & Jam (Aktivitas Terakhir di Hari Itu) --}}
                  <td class="px-5 py-3.5 text-right">
                    <p class="text-xs font-bold text-gray-600">
                      {{ \Carbon\Carbon::parse($history->tanggal)->translatedFormat('d M Y') }}</p>
                    <p class="text-[10px] font-semibold text-gray-400 mt-0.5">
                      {{ \Carbon\Carbon::parse($history->created_at)->format('H:i') }} WIB</p>
                  </td>

                </tr>
              @empty
                {{-- Empty State --}}
                <tr>
                  <td colspan="4" class="px-5 py-12 text-center">
                    <div class="flex flex-col items-center justify-center text-gray-400">
                      <svg class="w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                      </svg>
                      <p class="text-sm font-medium">Belum ada mutasi stok.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        {{-- Pagination Mutasi --}}
        @if ($histories->hasPages())
          <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            {{ $histories->links() }}
          </div>
        @endif
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
