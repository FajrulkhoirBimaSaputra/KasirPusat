<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kasir Pusat') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50">

    <div x-data="{ sidebarOpen: true, mobileOpen: false }" class="flex h-screen overflow-hidden bg-gray-50">

        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col transition-all duration-300 ease-in-out relative min-w-0 w-full overflow-x-hidden"
            :class="sidebarOpen ? 'sm:ml-64' : 'sm:ml-16'">

            <header
                class="bg-white border-b border-gray-100 shadow-sm h-16 flex items-center justify-between px-4 sm:px-6 z-10 shrink-0">

                <div class="flex items-center gap-3 sm:gap-4">

                    <button @click="sidebarOpen = !sidebarOpen"
                        class="hidden sm:inline-flex items-center justify-center p-2 text-gray-500 rounded-xl hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <button @click="mobileOpen = true"
                        class="sm:hidden inline-flex items-center justify-center p-2 text-gray-500 rounded-xl hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    @isset($header)
                        <div class="font-bold text-gray-800 tracking-tight flex items-center">
                            {{ $header }}
                        </div>
                    @else
                        <span class="font-bold text-gray-800 tracking-tight hidden sm:block">
                            {{ config('app.name', 'Kasir Pusat') }}
                        </span>
                    @endisset
                </div>

            </header>

            <main class="flex-1 overflow-y-auto p-0 sm:p-6 lg:p-8 bg-gray-50">
                <div class="w-full max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>

        </div>
    </div>
</body>

</html>
