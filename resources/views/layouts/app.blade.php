<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])


</head>

<body class="font-sans antialiased bg-gray-100">
<div 
    x-data="{ sidebarOpen: true, mobileOpen: false }"
    class="flex min-h-screen overflow-hidden"
>

    @include('layouts.sidebar')

    {{-- CONTENT AREA --}}
    <div 
    class="flex-1 flex flex-col transition-all duration-300
           ml-0 sm:ml-16"
    :class="sidebarOpen ? 'sm:ml-64' : 'sm:ml-16'"
>


        {{-- TOP BAR --}}
        <header class="bg-white shadow h-16 flex items-center justify-between px-4">

            {{-- DESKTOP TOGGLE --}}
            <button
                @click="sidebarOpen = !sidebarOpen"
                class="hidden sm:inline-flex items-center justify-center p-2 rounded hover:bg-gray-100"
            >
                ☰
            </button>

            {{-- MOBILE TOGGLE --}}
            <button
                @click="mobileOpen = true"
                class="sm:hidden p-2 rounded hover:bg-gray-100"
            >
                ☰
            </button>

            <span class="font-semibold text-gray-700">
                {{ config('app.name') }}
            </span>
        </header>

        {{-- PAGE HEADER --}}
        @isset($header)
            <header class="bg-white shadow">
                <div class="px-6 py-4">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- PAGE CONTENT --}}
        <main class="flex-1 p-6">
            {{ $slot }}
        </main>

    </div>
</div>
</body>
</html>
