<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- CSRF Token — untuk fetch/AJAX manual --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Title --}}
    <title>@yield('title', 'YPTQ Asy-Syams')</title>

    {{-- Deskripsi default (bisa di-override via @section('description')) --}}
    <meta name="description" content="@yield('description', 'Yayasan Pendidikan Tilawatil Qur\'an Asy-Syams Pekanbaru')">

    {{-- Favicon / Logo Tab Browser --}}
    {{-- ⚠️ Gunakan lowercase (.png) supaya aman di Linux server --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    {{-- Open Graph — preview saat link dibagikan --}}
    <meta property="og:title" content="@yield('og_title', 'YPTQ Asy-Syams')">
    <meta property="og:description" content="@yield('og_description', 'Yayasan Pendidikan Tilawatil Qur\'an Asy-Syams Pekanbaru')">
    <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">

    {{-- ⭐ CSS via Vite (KONSISTEN dengan halaman lain) --}}
    {{--    Pastikan `resources/css/app.css` sudah include:
          @tailwind base; @tailwind components; @tailwind utilities;
          + import font Inter atau custom CSS lain --}}
    @vite(['resources/css/app.css'])

    {{-- ⭐ Slot untuk CSS tambahan per halaman --}}
    @stack('styles')

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    {{-- Livewire styles (opsional — hapus kalau tidak pakai Livewire) --}}
    {{-- @livewireStyles --}}
</head>

<body class="bg-gray-50 text-gray-800 antialiased">

    {{-- Preloader --}}
    <x-loader />

    <div class="flex flex-col min-h-screen">

        {{-- HEADER --}}
        {{-- ⚠️ Jangan bungkus lagi dengan <header> kalau
             `layouts.header` sudah punya <header> sendiri.
             Cek dulu isi `resources/views/layouts/header.blade.php`. --}}
        @include('layouts.header')

        {{-- KONTEN UTAMA --}}
        <main class="flex-grow">
            @yield('content')
        </main>

        {{-- FOOTER --}}
        @includeIf('layouts.footer')

    </div>

    {{-- ⭐ JS via Vite (KONSISTEN dengan halaman lain) --}}
    @vite(['resources/js/app.js'])

    {{-- Livewire scripts (opsional — hapus kalau tidak pakai Livewire) --}}
    {{-- @livewireScripts --}}

    {{-- ⭐ Slot untuk JS tambahan per halaman --}}
    @stack('scripts')

</body>
</html>