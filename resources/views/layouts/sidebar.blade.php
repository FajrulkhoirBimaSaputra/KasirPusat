{{-- DESKTOP SIDEBAR --}}
<aside
    class="fixed inset-y-0 left-0 bg-white border-r border-gray-100 shadow-sm transition-all duration-300 z-40 hidden sm:flex flex-col"
    :class="sidebarOpen ? 'w-64' : 'w-16'">

    {{-- LOGO & TOGGLE --}}
    <div class="h-16 flex items-center justify-center border-b border-gray-100 p-2 bg-white shrink-0">
        <a href="{{ route('dashboard') }}" x-show="sidebarOpen" x-transition
            class="flex items-center justify-center h-full">
            <img src="{{ asset('/images/logo.png') }}?v={{ time() }}" alt="Logo"
                class="h-10 w-auto object-contain">
        </a>

        <button @click="sidebarOpen = true" x-show="!sidebarOpen" x-transition
            class="p-2 text-primary hover:bg-primary/10 rounded-xl transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    {{-- MENU --}}
    <nav class="flex-1 px-3 py-4 space-y-1.5 text-sm overflow-y-auto font-medium">

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3" />
            </svg>
            <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>Dashboard</span>
        </a>

        @if (auth()->user()->role === 'admin')
            <div x-show="sidebarOpen" class="px-3 pt-4 pb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
                Admin Area
            </div>

            <a href="{{ route('admin.users.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 10-6 0" />
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>User Management</span>
            </a>

            <a href="{{ route('menu.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('menu.*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0l-8 5-8-5" />
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>Daftar Menu</span>
            </a>

            <a href="{{ route('shift.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('shift.*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2v-8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>Jadwal Shift</span>
            </a>

            <a href="{{ route('laporan.bulanan') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('laporan.bulanan') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>Laporan Bulanan</span>
            </a>

            <a href="{{ route('admin.stok.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.stok.*') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>Manajemen Stok</span>
            </a>
        @endif

        @if (auth()->user()->role === 'kasir')
            <div x-show="sidebarOpen" class="px-3 pt-4 pb-2 text-xs font-bold text-gray-400 uppercase tracking-wider">
                Kasir Area
            </div>

            <a href="{{ route('kasir.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('kasir.index') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>Kasir</span>
            </a>

            <a href="{{ route('kasir.riwayat') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('kasir.riwayat') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4l3 3M5 3v4h4M19 3v4h-4M5 21v-4h4M19 21v-4h-4" />
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>Riwayat Transaksi</span>
            </a>

            <a href="{{ route('kasir.stok') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('kasir.stok') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>Cek Stok</span>
            </a>
        @endif
    </nav>

    {{-- USER INFO DESKTOP (Footer Sidebar) --}}
    <div class="border-t border-gray-100 p-3 text-sm bg-gray-50/50 shrink-0">
        <div x-show="sidebarOpen" x-transition.opacity.duration.200ms class="px-2 mb-3">
            <div class="font-bold text-gray-800 truncate">{{ Auth::user()->name }}</div>
            <div class="text-gray-500 text-xs truncate">{{ Auth::user()->username }}</div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button
                class="w-full flex items-center justify-center gap-3 py-2 rounded-lg text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors font-semibold"
                :class="sidebarOpen ? 'px-3' : 'px-0'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span x-show="sidebarOpen" x-transition.opacity.duration.200ms>Keluar Aplikasi</span>
            </button>
        </form>
    </div>
</aside>

{{-- MOBILE SIDEBAR --}}
<aside x-show="mobileOpen" x-transition:enter="transition transform ease-out duration-300"
    x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
    x-transition:leave="transition transform ease-in duration-300" x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 w-72 bg-white border-r shadow-2xl z-50 flex flex-col sm:hidden"
    style="display: none;">

    <div class="h-16 flex items-center justify-between px-5 border-b border-gray-100 shrink-0">
        <img src="{{ asset('/images/logo.png') }}" alt="Logo" class="h-8 w-auto">
        <button @click="mobileOpen = false"
            class="p-2 -mr-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 text-sm overflow-y-auto font-medium">
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3" />
            </svg>
            Dashboard
        </a>

        @if (auth()->user()->role === 'admin')
            <div class="pt-4 pb-2 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Admin Area</div>
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 10-6 0" />
                </svg>
                User Management
            </a>
            <a href="{{ route('menu.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('menu.*') ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0l-8 5-8-5" />
                </svg>
                Daftar Menu
            </a>
            <a href="{{ route('shift.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('shift.*') ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2v-8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Jadwal Shift
            </a>
            <a href="{{ route('laporan.bulanan') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('laporan.bulanan') ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Laporan Bulanan
            </a>
            <a href="{{ route('admin.stok.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.stok.*') ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Manajemen Stok
            </a>
        @endif

        @if (auth()->user()->role === 'kasir')
            <div class="pt-4 pb-2 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Kasir Area</div>
            <a href="{{ route('kasir.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('kasir.index') ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Kasir
            </a>
            <a href="{{ route('kasir.riwayat') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('kasir.riwayat') ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4l3 3M5 3v4h4M19 3v4h-4M5 21v-4h4M19 21v-4h-4" />
                </svg>
                Riwayat Transaksi
            </a>
            <a href="{{ route('kasir.stok') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('kasir.stok') ? 'bg-primary text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Cek Stok
            </a>
        @endif
    </nav>

    {{-- USER INFO MOBILE (Footer) --}}
    <div class="border-t border-gray-100 p-5 bg-gray-50 shrink-0">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div>
                <div class="font-bold text-gray-800">{{ Auth::user()->name }}</div>
                <div class="text-gray-500 text-xs">{{ Auth::user()->username }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-100 text-red-600 font-semibold rounded-xl hover:bg-red-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

{{-- MOBILE OVERLAY --}}
<div x-show="mobileOpen" x-transition.opacity.duration.300ms @click="mobileOpen = false"
    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 sm:hidden" style="display: none;">
</div>
