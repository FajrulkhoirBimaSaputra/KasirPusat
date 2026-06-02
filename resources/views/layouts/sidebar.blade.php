{{-- DESKTOP SIDEBAR --}}
<aside class="fixed inset-y-0 left-0 bg-white border-r shadow transition-all duration-300 z-40 hidden sm:flex flex-col"
    :class="sidebarOpen ? 'w-64' : 'w-16'">

    {{-- LOGO --}}
    <div class="h-16 flex items-center justify-center border-b p-2 bg-white">
        <a href="{{ route('dashboard') }}" x-show="sidebarOpen" x-transition
            class="flex items-center justify-center h-full">
            <img src="{{ asset('/images/logo.png') }}?v={{ time() }}" alt="Logo" class="h-12  w-auto object-contain">
        </a>

        <span x-show="!sidebarOpen" x-transition class="font-bold text-primary text-xl cursor-pointer">
            ☰
        </span>
    </div>

    {{-- MENU --}}
    <nav class="flex-1 p-2 space-y-1 text-sm overflow-y-auto">

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3" />
            </svg>
            <span x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 10-6 0" />
                </svg>
                <span x-show="sidebarOpen" x-transition>User Management</span>
            </a>

            <a href="{{ route('menu.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('menu.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0l-8 5-8-5" />
                </svg>
                <span x-show="sidebarOpen" x-transition>Daftar Menu</span>
            </a>

            <a href="{{ route('shift.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('shift.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2v-8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span x-show="sidebarOpen" x-transition>Jadwal Shift</span>
            </a>

            {{-- MENU BARU: LAPORAN BULANAN (ADMIN) --}}
            <a href="{{ route('laporan.bulanan') }}"
                class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('laporan.bulanan') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span x-show="sidebarOpen" x-transition>Laporan Bulanan</span>
            </a>

            {{-- MENU BARU: MANAJEMEN STOK (ADMIN) --}}
            <a href="{{ route('admin.stok.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.stok.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span x-show="sidebarOpen" x-transition>Manajemen Stok</span>
            </a>
        @endif

        @if(auth()->user()->role === 'kasir')
            <a href="{{ route('kasir.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('kasir.index') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span x-show="sidebarOpen" x-transition>Kasir</span>
            </a>

            <a href="{{ route('kasir.riwayat') }}"
                class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('kasir.riwayat') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4l3 3M5 3v4h4M19 3v4h-4M5 21v-4h4M19 21v-4h-4" />
                </svg>
                <span x-show="sidebarOpen" x-transition>Riwayat Transaksi</span>
            </a>

            <a href="{{ route('kasir.stok') }}"
                class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('kasir.stok') ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <!-- Gunakan ikon Box atau Archive untuk Stok -->
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span>Cek Stok</span>
            </a>
        @endif
    </nav>

    {{-- USER INFO DESKTOP --}}
    <div class="border-t p-3 text-xs">
        <div x-show="sidebarOpen" x-transition>
            <div class="font-medium truncate">{{ Auth::user()->name }}</div>
            <div class="text-gray-500 truncate">{{ Auth::user()->username }}</div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-2" :class="!sidebarOpen ? 'text-center' : ''">
            @csrf
            <button class="text-red-600 hover:underline w-full" :class="sidebarOpen ? 'text-left' : 'text-center'">
                <span x-show="sidebarOpen">Logout</span>
                <span x-show="!sidebarOpen" title="Logout">🚪</span>
            </button>
        </form>
    </div>
</aside>

{{-- MOBILE SIDEBAR --}}
<aside x-show="mobileOpen" x-transition:enter="transition transform ease-out duration-300"
    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transition transform ease-in duration-300" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 w-64 bg-white border-r shadow-lg z-50 flex flex-col sm:hidden">

    <div class="h-16 flex items-center justify-between px-4 border-b">
        <span class="font-bold text-primary text-lg">{{ config('app.name') }}</span>
        <button @click="mobileOpen = false" class="text-2xl text-gray-500 hover:text-gray-800">&times;</button>
    </div>

    <nav class="flex-1 p-4 space-y-2 text-sm overflow-y-auto">
        <a href="{{ route('dashboard') }}"
            class="block px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
            Dashboard
        </a>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}"
                class="block px-3 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                User Management
            </a>
            <a href="{{ route('menu.index') }}"
                class="block px-3 py-2 rounded {{ request()->routeIs('menu.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                Daftar Menu
            </a>
            <a href="{{ route('shift.index') }}"
                class="block px-3 py-2 rounded {{ request()->routeIs('shift.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                Jadwal Shift
            </a>
            {{-- MENU BARU MOBILE: LAPORAN BULANAN --}}
            <a href="{{ route('laporan.bulanan') }}"
                class="block px-3 py-2 rounded {{ request()->routeIs('laporan.bulanan') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                Laporan Bulanan
            </a>
        @endif

        @if(auth()->user()->role === 'kasir')
            <a href="{{ route('kasir.index') }}"
                class="block px-3 py-2 rounded {{ request()->routeIs('kasir.index') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                Kasir
            </a>
            <a href="{{ route('kasir.riwayat') }}"
                class="block px-3 py-2 rounded {{ request()->routeIs('kasir.riwayat') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                Riwayat Transaksi
            </a>
        @endif
    </nav>

    {{-- USER INFO MOBILE --}}
    <div class="border-t p-4 text-sm bg-gray-50">
        <div class="font-medium text-gray-800">{{ Auth::user()->name }}</div>
        <div class="text-gray-500 text-xs mb-3">{{ Auth::user()->username }}</div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full text-center px-4 py-2 bg-red-100 text-red-600 rounded hover:bg-red-200 transition">
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- MOBILE OVERLAY --}}
<div x-show="mobileOpen" x-transition.opacity.duration.300ms @click="mobileOpen = false"
    class="fixed inset-0 bg-black bg-opacity-50 z-40 sm:hidden">
</div>