{{-- ============================================================ --}}
{{-- HEADER — Top Bar + Navbar + Mobile Drawer + Login Modal       --}}
{{-- ============================================================ --}}
{{-- Struktur:
    1. Setup variabel kontak & sosmed (PHP)
    2. Wrapper Alpine.js (state global)
    3. Top Bar (info kontak — hide saat scroll)
    4. Navbar utama (logo + menu — shrink saat scroll)
    5. Mobile drawer (slide from right)
    6. Login modal (2 pilihan: Student / Teacher)
--}}
{{-- ============================================================ --}}

{{-- ============================================================ --}}
{{-- 1. VARIABEL KONTAK & SOSMED                                   --}}
{{-- ============================================================ --}}
@php
    // WhatsApp — wa.me link dengan pesan otomatis
    $waNumber = '6285376374040';
    $waText   = urlencode("Assalamu'alaikum, saya ingin bertanya tentang YPTQ Asy-Syams.");
    $waLink   = "https://wa.me/{$waNumber}?text={$waText}";

    // Sosial media
    $instagramLink = 'https://www.instagram.com/rq_asy.syams';
    $facebookLink  = 'https://www.facebook.com/rq.asy.syams.rumbai.pekanbaru';
    $youtubeLink   = 'https://youtube.com/@rq_asysyams';

    // Email
    $emailAddress = 'rqasysyams23@gmail.com';
    $emailLink    = 'mailto:' . $emailAddress;
@endphp

{{-- Sembunyikan elemen dengan x-cloak sampai Alpine.js siap --}}
<style>
    [x-cloak] { display: none !important; }
</style>


{{-- ============================================================ --}}
{{-- 2. WRAPPER ALPINE.JS — State Global Header                    --}}
{{-- ============================================================ --}}
{{-- State:
    - open      : Mobile drawer terbuka/tutup
    - loginOpen : Login modal terbuka/tutup
    - scrolled  : Sudah scroll > 100px (untuk hide top bar + shrink navbar)
    - ticking   : Guard requestAnimationFrame (hindari spam scroll event)

    Behavior:
    - x-effect        : Lock body scroll saat drawer/modal terbuka
    - @scroll.window  : Handler scroll dengan rAF guard
    - @resize.window  : Auto-close drawer saat resize ke desktop
    - @keydown.escape : Tutup drawer & modal via tombol ESC --}}
<div
    x-data="{
        open: false,
        loginOpen: false,
        scrolled: false,
        ticking: false,

        // Handler scroll — pakai requestAnimationFrame untuk performa
        // Hysteresis: shrink di >100px, expand di <20px (hindari jitter)
        handleScroll() {
            if (!this.ticking) {
                window.requestAnimationFrame(() => {
                    const y = window.pageYOffset;
                    if (!this.scrolled && y > 100) this.scrolled = true;
                    else if (this.scrolled && y < 20) this.scrolled = false;
                    this.ticking = false;
                });
                this.ticking = true;
            }
        }
    }"
    x-init="handleScroll()"
    x-effect="document.body.style.overflow = (open || loginOpen) ? 'hidden' : ''"
    @scroll.window.passive="handleScroll()"
    @resize.window="if (window.innerWidth >= 768) open = false"
    @keydown.escape.window="open = false; loginOpen = false"
    class="sticky top-0 z-50"
>

    {{-- ======================================================== --}}
    {{-- 3. TOP BAR — Info Kontak & Sosmed                         --}}
    {{-- ======================================================== --}}
    {{-- - Hanya tampil di layar ≥ md (desktop)
        - Auto hide saat scroll (via max-h transition)
        - Konten: WA | Email (kiri), Sosmed (kanan) --}}
    <div
        class="hidden md:block bg-gray-50 text-gray-600 text-xs
               border-b border-gray-200 overflow-hidden
               transition-all duration-500"
        style="transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);"
        :class="{
            'max-h-0 opacity-0 border-b-0': scrolled,
            'max-h-20 opacity-100': !scrolled
        }"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-2.5">

            {{-- Kiri: WhatsApp + Email --}}
            <div class="flex items-center gap-6">
                {{-- WhatsApp --}}
                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-2 hover:text-emerald-700 transition-colors duration-200">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span class="font-medium">+62 853-7637-4040</span>
                </a>

                {{-- Email --}}
                <a href="{{ $emailLink }}"
                   class="flex items-center gap-2 hover:text-emerald-700 transition-colors duration-200">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-medium">{{ $emailAddress }}</span>
                </a>
            </div>

            {{-- Kanan: Sosmed --}}
            <div class="flex items-center gap-4">
                {{-- Instagram --}}
                <a href="{{ $instagramLink }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
                   class="text-gray-400 hover:text-pink-600 transition-[color,transform] duration-200 hover:-translate-y-0.5">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919C8.416 2.175 8.796 2.163 12 2.163zm0-2.163C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98C23.986 15.668 24 15.259 24 12c0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838A6.162 6.162 0 1012 18.163 6.162 6.162 0 0012 5.838zm0 10.162A4 4 0 1112 8a4 4 0 010 8zm6.406-11.845a1.44 1.44 0 101.439 1.44 1.44 1.44 0 00-1.439-1.44z"/>
                    </svg>
                </a>

                {{-- Facebook --}}
                <a href="{{ $facebookLink }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"
                   class="text-gray-400 hover:text-blue-600 transition-[color,transform] duration-200 hover:-translate-y-0.5">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 12.07C22 6.48 17.52 2 11.93 2S2 6.48 2 12.07c0 5.02 3.66 9.18 8.44 9.96v-7.05H7.9v-2.91h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.23.2 2.23.2v2.45h-1.26c-1.24 0-1.63.77-1.63 1.56v1.91h2.78l-.44 2.91h-2.34v7.05C18.34 21.25 22 17.09 22 12.07z"/>
                    </svg>
                </a>

                {{-- YouTube --}}
                <a href="{{ $youtubeLink }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube"
                   class="text-gray-400 hover:text-red-600 transition-[color,transform] duration-200 hover:-translate-y-0.5">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0C.488 3.45.029 5.804 0 12c.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zM9 16V8l8 3.993L9 16z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>


    {{-- ======================================================== --}}
    {{-- 4. NAVBAR UTAMA — Logo + Menu Desktop + Hamburger Mobile  --}}
    {{-- ======================================================== --}}
    <div
        :class="{
            'shadow-[0_4px_20px_rgba(0,0,0,0.06)]': scrolled,
            'shadow-sm': !scrolled
        }"
        class="bg-white border-b border-gray-100 transition-shadow duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="flex justify-between items-center transition-all duration-500"
                style="transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);"
                :class="{
                    'h-[60px] md:h-16': scrolled,
                    'h-[72px] md:h-20': !scrolled
                }"
            >

                {{-- LOGO — Gambar + Nama Yayasan --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2 sm:gap-3 min-w-0 flex-shrink">
                    <img src="{{ asset('images/logo.png') }}"
                         alt="Logo YPTQ Asy-Syams"
                         class="object-contain rounded-full shadow-sm flex-shrink-0
                                transition-[width,height] duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
                         :class="{
                             'w-9 h-9 md:w-10 md:h-10': scrolled,
                             'w-11 h-11 md:w-12 md:h-12': !scrolled
                         }">

                    <div class="flex flex-col leading-tight min-w-0">
                        {{-- Nama Yayasan --}}
                        <span class="font-serif font-bold tracking-tight text-emerald-900 whitespace-nowrap
                                     transition-[font-size] duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
                              :class="{
                                  'text-sm sm:text-base md:text-lg': scrolled,
                                  'text-base sm:text-lg md:text-2xl': !scrolled
                              }">
                            YPTQ ASY-SYAMS
                        </span>

                        {{-- Subtitle (hidden di mobile, fade out saat scroll) --}}
                        <span class="hidden sm:block text-[8px] md:text-[9px] text-gray-500
                                     tracking-[0.12em] md:tracking-widest uppercase whitespace-nowrap
                                     transition-[opacity,margin] duration-500 ease-[cubic-bezier(0.4,0,0.2,1)]"
                              :class="{
                                  'opacity-0 -mt-1': scrolled,
                                  'opacity-100 mt-0': !scrolled
                              }">
                            Yayasan Pendidikan Tilawatil Qur'an
                        </span>
                    </div>
                </a>


                {{-- ============================== --}}
                {{-- MENU DESKTOP (≥ md)            --}}
                {{-- ============================== --}}
                <div class="hidden md:flex md:items-center md:gap-1">

                    {{-- HOME --}}
                    <a href="{{ url('/') }}"
                       class="group relative px-4 py-2 text-[13px] font-bold uppercase tracking-wide
                              transition-colors duration-200
                              {{ Request::is('/') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">
                        Home
                        <span class="absolute left-4 right-4 bottom-0 h-[2px] bg-emerald-700 origin-left
                                     transition-transform duration-300 ease-out
                                     {{ Request::is('/') ? 'scale-x-100' : 'scale-x-0' }}
                                     group-hover:scale-x-100"></span>
                    </a>

                    {{-- ⭐ PROFILE — MENU BARU --}}
                    <a href="{{ route('profile.yayasan') }}"
                       class="group relative px-4 py-2 text-[13px] font-bold uppercase tracking-wide
                              transition-colors duration-200
                              {{ request()->routeIs('profile.yayasan') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">
                        Profile
                        <span class="absolute left-4 right-4 bottom-0 h-[2px] bg-emerald-700 origin-left
                                     transition-transform duration-300 ease-out
                                     {{ request()->routeIs('profile.yayasan') ? 'scale-x-100' : 'scale-x-0' }}
                                     group-hover:scale-x-100"></span>
                    </a>

                    {{-- PROGRAMS — Dropdown (Mendukung Hover Mouse & Sentuhan Jari Tablet) --}}
                    <div x-data="{ openProgram: false, timer: null }"
                         @mouseenter="clearTimeout(timer); openProgram = true"
                         @mouseleave="timer = setTimeout(() => openProgram = false, 150)"
                         @click.outside="openProgram = false"
                         class="relative">

                        <button type="button"
                                @click="openProgram = !openProgram"
                                class="group relative flex items-center gap-1 px-4 py-2 text-[13px] font-bold uppercase
                                       tracking-wide transition-colors duration-200"
                                :class="openProgram ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700'">
                            Programs
                            <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                 :class="{ 'rotate-180': openProgram }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="openProgram"
                             x-transition:enter="transition duration-200 ease-out"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition duration-150 ease-in"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="absolute left-0 top-full pt-2 w-56 z-50"
                             x-cloak>
                            <div class="bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 overflow-hidden">
                                <a href="{{ request()->routeIs('home') ? '#kelas' : url('/#kelas') }}"
                                   class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 hover:pl-5 transition-all duration-200">
                                    Tahsin
                                </a>
                                <a href="{{ request()->routeIs('home') ? '#kelas' : url('/#kelas') }}"
                                   class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 hover:pl-5 transition-all duration-200">
                                    Tilawah
                                </a>
                                <a href="{{ request()->routeIs('home') ? '#kelas' : url('/#kelas') }}"
                                   class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 hover:pl-5 transition-all duration-200">
                                    Murottal
                                </a>
                                <a href="{{ request()->routeIs('home') ? '#kelas' : url('/#kelas') }}"
                                   class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 hover:pl-5 transition-all duration-200">
                                    Baca / Tulis
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- ⭐ SPMB — MENU BARU --}}
                    <a href="{{ route('spmb.index') }}"
                       class="group relative px-4 py-2 text-[13px] font-bold uppercase tracking-wide
                              transition-colors duration-200
                              {{ request()->routeIs('spmb.*') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">
                        SPMB
                        <span class="absolute left-4 right-4 bottom-0 h-[2px] bg-emerald-700 origin-left
                                     transition-transform duration-300 ease-out
                                     {{ request()->routeIs('spmb.*') ? 'scale-x-100' : 'scale-x-0' }}
                                     group-hover:scale-x-100"></span>
                    </a>

                    {{-- REGISTRATION --}}
                    <a href="{{ route('register') }}"
                       class="group relative px-4 py-2 text-[13px] font-bold uppercase tracking-wide
                              transition-colors duration-200
                              {{ request()->routeIs('register') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">
                        Registration
                        <span class="absolute left-4 right-4 bottom-0 h-[2px] bg-emerald-700 origin-left
                                     transition-transform duration-300 ease-out
                                     {{ request()->routeIs('register') ? 'scale-x-100' : 'scale-x-0' }}
                                     group-hover:scale-x-100"></span>
                    </a>

                    {{-- NEWS — route ke /news --}}
                    <a href="{{ route('news.index') }}"
                       class="group relative px-4 py-2 text-[13px] font-bold uppercase tracking-wide
                              transition-colors duration-200
                              {{ request()->routeIs('news.index') || request()->routeIs('post.show') ? 'text-emerald-700' : 'text-gray-700 hover:text-emerald-700' }}">
                        News
                        <span class="absolute left-4 right-4 bottom-0 h-[2px] bg-emerald-700 origin-left
                                     transition-transform duration-300 ease-out
                                     {{ request()->routeIs('news.index') || request()->routeIs('post.show') ? 'scale-x-100' : 'scale-x-0' }}
                                     group-hover:scale-x-100"></span>
                    </a>

                    {{-- CONTACT — anchor ke section #contact --}}
                    <a href="{{ request()->routeIs('home') ? '#contact' : url('/#contact') }}"
                       class="group relative px-4 py-2 text-[13px] font-bold uppercase tracking-wide
                              text-gray-700 hover:text-emerald-700 transition-colors duration-200">
                        Contact
                        <span class="absolute left-4 right-4 bottom-0 h-[2px] bg-emerald-700 origin-left
                                     scale-x-0 transition-transform duration-300 ease-out
                                     group-hover:scale-x-100"></span>
                    </a>

                    {{-- LOGIN PORTAL — beda tampilan jika sudah login --}}
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="ml-2 inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-800 text-white
                                  text-[13px] font-bold rounded-full shadow-sm
                                  hover:bg-emerald-700 hover:shadow-lg hover:-translate-y-0.5
                                  transition-all duration-300">
                            Dashboard
                        </a>
                    @else
                        <button type="button" @click="loginOpen = true"
                                class="ml-2 inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-800 text-white
                                       text-[13px] font-bold rounded-full shadow-sm
                                       hover:bg-emerald-700 hover:shadow-lg hover:-translate-y-0.5
                                       transition-all duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            <span>Login Portal</span>
                        </button>
                    @endauth
                </div>


                {{-- ============================== --}}
                {{-- HAMBURGER MOBILE (< md)         --}}
                {{-- ============================== --}}
                <div class="flex md:hidden flex-shrink-0">
                    <button type="button"
                            @click="
                                open = true;
                                setTimeout(() => {
                                    const el = document.getElementById('mobileMenuScroll');
                                    if (el) el.scrollTop = 0;
                                }, 100);
                            "
                            class="inline-flex items-center justify-center rounded-xl text-gray-600 bg-gray-50
                                   hover:bg-emerald-50 hover:text-emerald-800 active:scale-95
                                   transition-all duration-200 w-10 h-10"
                            aria-label="Open menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- ======================================================== --}}
    {{-- 5. MOBILE DRAWER — Slide from right                       --}}
    {{-- ======================================================== --}}
    <template x-teleport="body">
        <div x-show="open" x-transition.opacity.duration.300ms class="md:hidden fixed inset-0 z-[9999]" x-cloak>

            {{-- BACKDROP --}}
            <div x-show="open"
                 x-transition:enter="transition duration-[350ms] ease-out"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition duration-[250ms] ease-in"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50 backdrop-blur-[6px]"
                 @click="open = false"></div>

            {{-- DRAWER --}}
            <aside x-show="open"
                   x-transition:enter="transition duration-[350ms] ease-out"
                   x-transition:enter-start="translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition duration-[250ms] ease-in"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="translate-x-full"
                   class="fixed right-0 top-0 h-[100dvh] w-[88%] max-w-[400px] bg-white
                          shadow-2xl overflow-hidden flex flex-col">

                {{-- MOBILE HEADER --}}
                <div class="shrink-0 bg-white border-b border-gray-100 px-5 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ url('/') }}" class="flex items-center gap-2.5 min-w-0" @click="open = false">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                                 class="w-10 h-10 object-contain rounded-full shadow-sm flex-shrink-0">
                            <div class="leading-tight min-w-0">
                                <h3 class="text-base font-serif font-bold text-emerald-900 whitespace-nowrap">
                                    YPTQ ASY-SYAMS
                                </h3>
                                <p class="hidden xs:block text-[7px] text-gray-500 tracking-[0.12em] uppercase whitespace-nowrap">
                                    Yayasan Pendidikan Tilawatil Qur'an
                                </p>
                            </div>
                        </a>
                        <button type="button" @click="open = false"
                                class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-500
                                       hover:bg-red-50 hover:text-red-600 transition-all duration-200 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- SCROLL AREA --}}
                <div id="mobileMenuScroll" class="flex-1 overflow-y-auto px-4 pt-4 pb-6 space-y-4 bg-white">

                    {{-- NAV CARD --}}
                    <div class="bg-gray-50 rounded-2xl p-2 border border-gray-100 space-y-1">

                        {{-- HOME --}}
                        <a href="{{ url('/') }}" @click="open = false"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold
                                  transition-all duration-200
                                  {{ Request::is('/') ? 'bg-emerald-100 text-emerald-800 shadow-sm' : 'text-gray-600 hover:bg-white hover:text-emerald-800' }}">
                            <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-emerald-700 shadow-sm flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 11l9-8 9 8v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z"/>
                                </svg>
                            </span>
                            <span>Home</span>
                        </a>

                        {{-- ⭐ PROFILE YAYASAN — MENU BARU (Mobile) --}}
                        <a href="{{ route('profile.yayasan') }}" @click="open = false"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold
                                  transition-all duration-200
                                  {{ request()->routeIs('profile.yayasan') ? 'bg-emerald-100 text-emerald-800 shadow-sm' : 'text-gray-600 hover:bg-white hover:text-emerald-800' }}">
                            <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-emerald-700 shadow-sm flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <span>Profile</span>
                        </a>

                        {{-- PROGRAMS — Accordion --}}
                        <div x-data="{ openSub: false }">
                            <button type="button" @click="openSub = !openSub"
                                    class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl
                                           text-sm font-bold text-gray-600 hover:bg-white hover:text-emerald-800
                                           transition-all duration-200">
                                <span class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-emerald-700 shadow-sm flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </span>
                                    <span>Programs</span>
                                </span>
                                <svg :class="{ 'rotate-180 text-emerald-700': openSub }"
                                     class="w-5 h-5 transform transition-transform duration-200 flex-shrink-0"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="openSub" x-collapse.duration.300ms
                                 class="mt-2 ml-12 mr-2 bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                                <a @click="open = false" href="{{ request()->routeIs('home') ? '#kelas' : url('/#kelas') }}"
                                   class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 transition">Tahsin</a>
                                <a @click="open = false" href="{{ request()->routeIs('home') ? '#kelas' : url('/#kelas') }}"
                                   class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 transition">Tilawah</a>
                                <a @click="open = false" href="{{ request()->routeIs('home') ? '#kelas' : url('/#kelas') }}"
                                   class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 transition">Murottal</a>
                                <a @click="open = false" href="{{ request()->routeIs('home') ? '#kelas' : url('/#kelas') }}"
                                   class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-emerald-800 transition">Baca/Tulis</a>
                            </div>
                        </div>

                        {{-- ⭐ SPMB — MENU BARU (Mobile) --}}
                        <a href="{{ route('spmb.index') }}" @click="open = false"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold
                                  transition-all duration-200
                                  {{ request()->routeIs('spmb.*') ? 'bg-emerald-100 text-emerald-800 shadow-sm' : 'text-gray-600 hover:bg-white hover:text-emerald-800' }}">
                            <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-emerald-700 shadow-sm flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14l-4-2-3 2-3-2-4 2V6a2 2 0 012-2z"/>
                                </svg>
                            </span>
                            <span>SPMB</span>
                        </a>

                        {{-- REGISTRATION (Dilengkapi Indikator Aktif & Ikon Khusus) --}}
                        <a href="{{ route('register') }}" @click="open = false"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold
                                  transition-all duration-200
                                  {{ request()->routeIs('register') ? 'bg-emerald-100 text-emerald-800 shadow-sm' : 'text-gray-600 hover:bg-white hover:text-emerald-800' }}">
                            <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-emerald-700 shadow-sm flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </span>
                            <span>Registration</span>
                        </a>

                        {{-- NEWS --}}
                        <a href="{{ route('news.index') }}" @click="open = false"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold
                                  transition-all duration-200
                                  {{ request()->routeIs('news.index') || request()->routeIs('post.show') ? 'bg-emerald-100 text-emerald-800 shadow-sm' : 'text-gray-600 hover:bg-white hover:text-emerald-800' }}">
                            <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-emerald-700 shadow-sm flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                            <span>News</span>
                        </a>

                        {{-- CONTACT --}}
                        <a href="{{ request()->routeIs('home') ? '#contact' : url('/#contact') }}" @click="open = false"
                           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold text-gray-600
                                  hover:bg-white hover:text-emerald-800 transition-all duration-200">
                            <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-emerald-700 shadow-sm flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <span>Contact</span>
                        </a>
                    </div>

                    {{-- CONTACT CARD --}}
                    <div class="rounded-2xl bg-gradient-to-br from-emerald-900 to-emerald-700 p-5 text-white shadow-lg">
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.25em] text-emerald-100 mb-4">
                            Hubungi Kami
                        </p>
                        <div class="space-y-3">
                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-3 text-sm text-white/90 hover:text-amber-300 transition">
                                <span class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                </span>
                                <span>+62 853-7637-4040</span>
                            </a>
                            <a href="{{ $emailLink }}"
                               class="flex items-center gap-3 text-sm text-white/90 hover:text-amber-300 transition">
                                <span class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <span class="break-all">{{ $emailAddress }}</span>
                            </a>
                        </div>
                    </div>

                    {{-- LOGIN BUTTON MOBILE --}}
                    <div class="pt-2">
                        @auth
                            <a href="{{ url('/dashboard') }}" @click="open = false"
                               class="flex items-center justify-center gap-2 w-full px-4 py-3.5 bg-emerald-800 text-white
                                      font-bold rounded-2xl shadow-md hover:bg-emerald-700 active:scale-[0.98]
                                      transition-all duration-200">
                                Dashboard
                            </a>
                        @else
                            <button type="button" @click="loginOpen = true; open = false"
                                    class="w-full flex justify-center items-center gap-2 px-4 py-3.5 bg-emerald-800 text-white
                                           font-bold rounded-2xl shadow-md hover:bg-emerald-700 active:scale-[0.98]
                                           transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                Login Portal
                            </button>
                        @endauth
                    </div>
                </div>
            </aside>
        </div>
    </template>


    {{-- ======================================================== --}}
    {{-- 6. LOGIN MODAL                                            --}}
    {{-- ======================================================== --}}
    <template x-teleport="body">
        <div x-show="loginOpen" x-transition.opacity.duration.250ms class="fixed inset-0 z-[10000] overflow-y-auto" role="dialog" aria-modal="true" x-cloak>

            {{-- BACKDROP --}}
            <div x-show="loginOpen"
                 x-transition:enter="transition duration-[250ms] ease-out"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition duration-200 ease-in"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-md"
                 @click="loginOpen = false"></div>

            {{-- MODAL PANEL --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="loginOpen"
                     x-transition:enter="transition duration-300 ease-out"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-[0.97]"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition duration-200 ease-in"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 scale-[0.97]"
                     class="relative transform overflow-hidden rounded-3xl bg-white
                            shadow-[0_30px_80px_rgba(0,0,0,0.25)] w-full max-w-lg">

                    <div class="px-6 py-8 sm:p-10">
                        <div class="text-center mb-8">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-white
                                        mb-5 shadow-lg border border-gray-100 overflow-hidden">
                                <img src="{{ asset('images/logo.png') }}" alt="Logo YPTQ Asy-Syams"
                                     class="w-full h-full object-cover">
                            </div>
                            <h3 class="text-2xl font-serif font-bold text-gray-900 tracking-tight">
                                Pilih Akses Login
                            </h3>
                            <p class="text-sm text-gray-500 mt-2.5 leading-relaxed">
                                Silakan pilih jenis akun untuk masuk ke portal sistem kami.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- STUDENT LOGIN --}}
                            <a href="{{ route('login') }}"
                               class="group relative flex flex-col items-center justify-center p-6 bg-white
                                      border-2 border-gray-100 rounded-2xl
                                      hover:border-blue-500 hover:shadow-[0_10px_25px_rgba(59,130,246,0.15)]
                                      hover:-translate-y-1 transition-all duration-[250ms]">
                                <div class="p-4 bg-gray-50 rounded-full group-hover:bg-blue-50 transition-colors duration-200 mb-4">
                                    <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-600 transition-colors duration-200"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-900 group-hover:text-blue-700 text-lg transition-colors duration-200">
                                    Students Login
                                </h4>
                                <p class="text-xs text-gray-500 mt-1.5">Check Grades & Payments</p>
                            </a>

                            {{-- TEACHER LOGIN --}}
                            <a href="{{ route('filament.admin.auth.login') }}"
                               class="group relative flex flex-col items-center justify-center p-6 bg-white
                                      border-2 border-gray-100 rounded-2xl
                                      hover:border-emerald-500 hover:shadow-[0_10px_25px_rgba(16,185,129,0.15)]
                                      hover:-translate-y-1 transition-all duration-[250ms]">
                                <div class="p-4 bg-gray-50 rounded-full group-hover:bg-emerald-50 transition-colors duration-200 mb-4">
                                    <svg class="w-8 h-8 text-gray-400 group-hover:text-emerald-600 transition-colors duration-200"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.118l-6.5.496c-.146.011-.292.011-.438 0l-6.5-.496a2.122 2.122 0 01-1.872-2.118v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/>
                                    </svg>
                                </div>
                                <h4 class="font-bold text-gray-900 group-hover:text-emerald-700 text-lg transition-colors duration-200">
                                    Teachers Login
                                </h4>
                                <p class="text-xs text-gray-500 mt-1.5">Access Admin Panel</p>
                            </a>

                        </div>
                    </div>

                    <div class="bg-gray-50/80 px-6 py-5 flex justify-center border-t border-gray-100">
                        <button type="button" @click="loginOpen = false"
                                class="w-full sm:w-auto px-10 py-2.5 text-sm font-bold text-gray-600
                                       bg-white border border-gray-300 rounded-xl
                                       hover:bg-gray-100 hover:text-gray-900 hover:border-gray-400
                                       active:scale-[0.98] transition-all duration-200">
                            Cancel
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </template>

</div>