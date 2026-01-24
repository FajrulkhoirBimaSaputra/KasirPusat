{{-- DESKTOP SIDEBAR --}}
<aside class="fixed inset-y-0 left-0 bg-white border-r shadow
           transition-all duration-300 z-40
           hidden sm:flex flex-col" :class="sidebarOpen ? 'w-64' : 'w-16'">

    {{-- LOGO --}}
    <div class="h-16 flex items-center justify-center border-b">
        <span class="font-bold text-primary text-lg" x-show="sidebarOpen" x-transition>
            {{ config('app.name') }}
        </span>
        <span x-show="!sidebarOpen" class="font-bold">☰</span>
    </div>

    {{-- MENU --}}
    <nav class="flex-1 p-2 space-y-1 text-sm">

        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded
   {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
            {{-- Dashboard --}}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3" />
            </svg>
            <span x-show="sidebarOpen" x-transition>Dashboard</span>
        </a>


        @if(auth()->user()->role === 'admin')
            {{-- User Management --}}
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center gap-3 px-3 py-2 rounded
                   {{ request()->routeIs('admin.users.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-4a4 4 0 11-8 0 4 4 0 018 0zm6 4a3 3 0 10-6 0" />
                </svg>
                <span x-show="sidebarOpen" x-transition>User Management</span>
            </a>


            {{-- Daftar Menu --}}
            <a href="{{ route('menu.index') }}" class="flex items-center gap-3 px-3 py-2 rounded
                   {{ request()->routeIs('menu.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0l-8 5-8-5" />
                </svg>
                <span x-show="sidebarOpen" x-transition>Daftar Menu</span>
            </a>

            {{-- Jadwal Shift --}}
            <a href="{{ route('shift.index') }}" class="flex items-center gap-3 px-3 py-2 rounded
                   {{ request()->routeIs('shift.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2v-8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span x-show="sidebarOpen" x-transition>Jadwal Shift</span>
            </a>

        @endif
        @if(auth()->user()->role === 'kasir')
            {{-- Kasir --}}
            <a href="{{ route('kasir.index') }}" class="flex items-center gap-3 px-3 py-2 rounded
                {{ request()->routeIs('kasir.*') ? 'bg-primary text-white' : 'hover:bg-gray-100 text-gray-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span x-show="sidebarOpen" x-transition>Kasir</span>
            </a>

        @endif
    </nav>

    {{-- USER --}}
    <div class="border-t p-3 text-xs">
        <div x-show="sidebarOpen" x-transition>
            <div class="font-medium">{{ Auth::user()->name }}</div>
            <div class="text-gray-500">{{ Auth::user()->username }}</div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button class="text-red-600 hover:underline w-full text-left">
                Logout
            </button>
        </form>
    </div>
</aside>

{{-- MOBILE SIDEBAR --}}
<aside x-show="mobileOpen" x-transition class="fixed inset-y-0 left-0 w-64 bg-white border-r shadow z-50 sm:hidden">
    <div class="h-16 flex items-center justify-between px-4 border-b">
        <span class="font-bold text-lg">{{ config('app.name') }}</span>
        <button @click="mobileOpen = false" class="text-xl">✕</button>
    </div>

    <nav class="p-4 space-y-2 text-sm">
        <a href="{{ route('dashboard') }}" class="block hover:text-primary">
            Dashboard
        </a>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.users.index') }}" class="block hover:text-primary">
                User Management
            </a>
            <a href="{{ route('menu.index') }}" class="block hover:text-primary">
                Daftar Menu
            </a>
            <a href="{{ route('menu.index') }}" class="block hover:text-primary">
                Jadwal Shift
            </a>
        @endif
        @if(auth()->user()->role === 'kasir')
            <a href="{{ route('kasir.index') }}" class="block hover:text-primary">
                Kasir
            </a>
        @endif
    </nav>
</aside>

{{-- MOBILE OVERLAY --}}
<div x-show="mobileOpen" @click="mobileOpen = false" class="fixed inset-0 bg-black bg-opacity-40 z-40 sm:hidden"></div>