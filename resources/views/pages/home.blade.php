@extends('root.app')

@section('title', 'YPTQ Asy-Syams')

@section('content')

{{-- ============================================================ --}}
{{-- SECTION 1: STYLE & ASSETS GLOBAL                             --}}
{{-- ============================================================ --}}
<style>
    :root {
        --ease-apple: cubic-bezier(0.16, 1, 0.3, 1);
        --ease-smooth: cubic-bezier(0.4, 0, 0.2, 1);
    }

    html {
        scroll-behavior: smooth;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* REVEAL ANIMATION */
    .js-reveal,
    .js-reveal-scale,
    .js-reveal-left,
    .js-reveal-right {
        opacity: 0;
        will-change: opacity, transform;
    }

    .js-reveal { transform: translate3d(0, 24px, 0); }
    .js-reveal-scale { transform: scale(0.985); }
    .js-reveal-left { transform: translate3d(-24px, 0, 0); }
    .js-reveal-right { transform: translate3d(24px, 0, 0); }

    .js-reveal.reveal-active {
        animation: revealUp 700ms var(--ease-apple) both;
        animation-delay: var(--reveal-delay, 0ms);
    }

    .js-reveal-scale.reveal-active {
        animation: revealScale 700ms var(--ease-apple) both;
        animation-delay: var(--reveal-delay, 0ms);
    }

    .js-reveal-left.reveal-active {
        animation: revealLeft 700ms var(--ease-apple) both;
        animation-delay: var(--reveal-delay, 0ms);
    }

    .js-reveal-right.reveal-active {
        animation: revealRight 700ms var(--ease-apple) both;
        animation-delay: var(--reveal-delay, 0ms);
    }

    @keyframes revealUp {
        from { opacity: 0; transform: translate3d(0, 24px, 0); }
        to { opacity: 1; transform: translate3d(0, 0, 0); }
    }

    @keyframes revealScale {
        from { opacity: 0; transform: scale(0.985); }
        to { opacity: 1; transform: scale(1); }
    }

    @keyframes revealLeft {
        from { opacity: 0; transform: translate3d(-24px, 0, 0); }
        to { opacity: 1; transform: translate3d(0, 0, 0); }
    }

    @keyframes revealRight {
        from { opacity: 0; transform: translate3d(24px, 0, 0); }
        to { opacity: 1; transform: translate3d(0, 0, 0); }
    }

    @media (prefers-reduced-motion: reduce) {
        .js-reveal, .js-reveal-scale, .js-reveal-left, .js-reveal-right {
            opacity: 1 !important;
            transform: none !important;
            animation: none !important;
        }
    }

    #floatingCta {
        transition: opacity 500ms var(--ease-smooth), transform 500ms var(--ease-smooth);
    }

    .swiper-button-next, .swiper-button-prev {
        transition: opacity 400ms var(--ease-smooth), transform 400ms var(--ease-smooth);
    }

    /* NEWS CARD */
    .news-card-media {
        position: relative;
        display: block;
        width: 100%;
        aspect-ratio: 4 / 3;
        overflow: hidden;
        background-color: #064e3b;
    }

    .news-card-media img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        transition: transform 700ms var(--ease-apple);
    }

    .news-card-media img.is-portrait {
        object-position: center 25%;
    }

    .news-card:hover .news-card-media img {
        transform: scale(1.05);
    }

    .news-card {
        transition: box-shadow 400ms var(--ease-apple), transform 400ms var(--ease-apple);
    }

    .news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }
</style>

{{-- Swiper CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    /* SWIPER HERO */
    .heroSwiper .swiper-slide {
        opacity: 0 !important;
        transition-property: opacity !important;
    }

    .heroSwiper .swiper-slide-active {
        opacity: 1 !important;
    }

    .heroSwiper .swiper-slide video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        object-position: center !important;
        display: block !important;
    }

    .custom-swiper-btn {
        background-color: rgba(0, 0, 0, 0.4) !important;
        backdrop-filter: blur(4px);
        border-radius: 50%;
        width: 48px !important;
        height: 48px !important;
        color: white !important;
    }

    .custom-swiper-btn:hover {
        background-color: rgba(0, 0, 0, 0.8) !important;
        transform: scale(1.15) !important;
    }

    .custom-swiper-btn::after {
        font-size: 18px !important;
        font-weight: 900;
    }

    .swiper-pagination {
        bottom: 35px !important;
    }

    .swiper-pagination-bullet {
        width: 8px !important;
        height: 8px !important;
        background: rgba(255, 255, 255, 0.6) !important;
        opacity: 1 !important;
        margin: 0 4px !important;
        transition: all 0.3s ease;
    }

    .swiper-pagination-bullet-active {
        background: #ea580c !important;
        width: 24px !important;
        border-radius: 9999px !important;
    }

    /* JUDUL SECTION */
    .siap-animasi-teks {
        opacity: 0;
        transform: translateY(20px);
    }

    .siap-animasi-garis {
        width: 96px;
        opacity: 0;
    }

    @keyframes textFadeIn {
        from { opacity: 0; transform: translateY(30px); letter-spacing: -0.1em; }
        to { opacity: 1; transform: translateY(0); letter-spacing: normal; }
    }

    @keyframes lineFadeInOnly {
        from { width: 0; opacity: 0; }
        to { width: 96px; opacity: 1; }
    }

    .teks-bergerak {
        animation: textFadeIn 1s cubic-bezier(0.215, 0.610, 0.355, 1) forwards;
    }

    .garis-bergerak {
        display: block !important;
        animation: lineFadeInOnly 1s ease-in-out forwards;
    }

    /* KURSOR BERKEDIP */
    .kursor-kedip {
        font-weight: 300;
        color: #065f46;
        animation: blinkCursor 0.8s step-end infinite;
    }

    @keyframes blinkCursor {
        from, to { opacity: 1; }
        50% { opacity: 0; }
    }
</style>


{{-- ============================================================ --}}
{{-- SECTION 2: DATA PREPARATION (PHP)                            --}}
{{-- ============================================================ --}}
@php
use App\Features\SiteSettings\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

$heroSetting = SiteSetting::where('key', 'hero_bg')->first();
$images = [];
if ($heroSetting) {
    $val = $heroSetting->value;
    if (is_array($val)) {
        $images = array_values(array_filter($val));
    } elseif (is_string($val) && $val !== '') {
        $decoded = json_decode($val, true);
        $images = is_array($decoded) ? array_values(array_filter($decoded)) : [$val];
    }
}

$heroVideoSetting = SiteSetting::where('key', 'hero_video')->first();
$heroVideoPaths = [];
if ($heroVideoSetting) {
    $videoVal = $heroVideoSetting->value;
    if (is_array($videoVal)) {
        $heroVideoPaths = array_values(array_filter($videoVal));
    } elseif (is_string($videoVal) && $videoVal !== '') {
        $decoded = json_decode($videoVal, true);
        $heroVideoPaths = is_array($decoded) ? array_values(array_filter($decoded)) : [$videoVal];
    }
}

$heroSlides = [];

foreach ($images as $img) {
    $heroSlides[] = [
        'type' => 'image',
        'src' => Storage::disk('public')->url($img),
    ];
}

foreach ($heroVideoPaths as $videoPath) {
    $ext = strtolower(pathinfo($videoPath, PATHINFO_EXTENSION));
    $mime = match ($ext) {
        'webm' => 'video/webm',
        'ogv', 'ogg' => 'video/ogg',
        'mov' => 'video/quicktime',
        default => 'video/mp4',
    };

    $heroSlides[] = [
        'type' => 'video',
        'src' => Storage::disk('public')->url($videoPath),
        'mime' => $mime,
    ];
}
@endphp


{{-- ============================================================ --}}
{{-- SECTION 3: HERO SWIPER + FLOATING CTA                        --}}
{{-- ============================================================ --}}
<div class="w-full bg-gray-50 flex flex-col items-center pb-12">

    <section class="relative w-full group">
        <div class="relative w-full aspect-[1920/700] md:h-[550px] overflow-hidden">
            <div class="swiper heroSwiper w-full h-full">

                <div class="swiper-wrapper">
                    @if(count($heroSlides) > 0)
                        @foreach($heroSlides as $slide)
                        <div class="swiper-slide">
                            @if($slide['type'] === 'video')
                            <video
                                class="w-full h-full object-cover object-center"
                                muted
                                loop
                                playsinline
                                preload="metadata"
                                data-slide-video>
                                <source src="{{ $slide['src'] }}" type="{{ $slide['mime'] ?? 'video/webm' }}">
                            </video>
                            @else
                            <img src="{{ $slide['src'] }}"
                                alt="Hero Slide"
                                fetchpriority="high"
                                decoding="async"
                                class="w-full h-full object-cover object-center">
                            @endif
                        </div>
                        @endforeach
                    @else
                    <div class="swiper-slide flex items-center justify-center bg-gray-800">
                        <span class="text-gray-400">Gambar Belum Diatur</span>
                    </div>
                    @endif
                </div>

                <div class="swiper-button-next custom-swiper-btn !hidden md:!flex opacity-0 group-hover:opacity-100"></div>
                <div class="swiper-button-prev custom-swiper-btn !hidden md:!flex opacity-0 group-hover:opacity-100"></div>

                <div class="swiper-pagination"></div>
            </div>
        </div>

        <div id="floatingCta"
            class="absolute left-0 right-0 bottom-0 flex justify-center z-40 px-4 pointer-events-none opacity-100"
            style="transform: translateY(50%);">
            <a href="{{ route('register') }}"
                class="pointer-events-auto inline-flex items-center justify-center px-8 md:px-10 py-3 md:py-4
                    bg-orange-600 hover:bg-orange-700 text-white text-[11px] md:text-sm font-bold rounded-full
                    shadow-[0_8px_20px_rgba(234,88,12,0.4)] uppercase tracking-widest
                    transition-all duration-300 hover:scale-105">
                DAFTAR SEKARANG
            </a>
        </div>
    </section>


    {{-- ======================================================== --}}
    {{-- SECTION 4: CARD PENDAFTARAN + COUNTDOWN                  --}}
    {{-- ======================================================== --}}
    <section class="w-full max-w-5xl mx-auto px-4 mt-12 md:mt-16 relative z-30">
        <div class="bg-white border-b-4 border-emerald-500 shadow-xl rounded-2xl p-6 md:p-10
                    flex flex-col md:flex-row items-center md:items-start justify-between gap-8 md:gap-12">

            <div class="md:w-1/2 w-full flex flex-col items-center md:items-start text-center md:text-left">
                <div class="inline-block px-4 py-1.5 bg-[#eefbf6] text-emerald-700 font-bold
                            text-[10px] md:text-xs rounded-full mb-4 uppercase tracking-widest">
                    Pendaftaran Dibuka
                </div>

                <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-3 md:mb-4 leading-snug">
                    Bergabunglah bersama <br class="block md:hidden">
                    <span class="text-emerald-600">YPTQ Asy-Syams</span>
                </h2>

                <p class="text-gray-500 text-sm md:text-base leading-relaxed max-w-md">
                    Sistem Penerimaan Peserta Didik Baru (SPMB) kini telah dibuka. Raih kesempatan
                    mendapatkan pendidikan berkualitas bersama kami.
                </p>
            </div>

            <div class="md:w-1/2 w-full flex flex-col items-center md:items-end mt-2 md:mt-0">
                <h3 class="text-base md:text-lg font-bold text-gray-800 mb-1 md:mb-2 text-center md:text-right">
                    Batas Akhir Pendaftaran:
                </h3>

                <p class="text-xs md:text-sm text-gray-500 mb-6 md:mb-8 text-center md:text-right">
                    {{ \Carbon\Carbon::parse($deadline)->translatedFormat('d F Y, H:i') }} WIB
                </p>

                <div class="flex justify-center md:justify-end items-center gap-2 md:gap-4 w-full">

                    <div class="flex flex-col items-center justify-center bg-[#eefbf6] border border-emerald-200
                                rounded-xl w-16 h-20 md:w-20 md:h-24 shadow-sm">
                        <span id="hitung-hari" class="text-2xl md:text-4xl font-extrabold text-emerald-700 leading-none">00</span>
                        <span class="text-[9px] md:text-xs text-emerald-600 uppercase tracking-widest font-bold mt-2">Hari</span>
                    </div>

                    <div class="text-xl md:text-3xl font-bold text-gray-300 mb-5 md:mb-6">:</div>

                    <div class="flex flex-col items-center justify-center bg-[#eefbf6] border border-emerald-200
                                rounded-xl w-16 h-20 md:w-20 md:h-24 shadow-sm">
                        <span id="hitung-jam" class="text-2xl md:text-4xl font-extrabold text-emerald-700 leading-none">00</span>
                        <span class="text-[9px] md:text-xs text-emerald-600 uppercase tracking-widest font-bold mt-2">Jam</span>
                    </div>

                    <div class="text-xl md:text-3xl font-bold text-gray-300 mb-5 md:mb-6">:</div>

                    <div class="flex flex-col items-center justify-center bg-[#eefbf6] border border-emerald-200
                                rounded-xl w-16 h-20 md:w-20 md:h-24 shadow-sm">
                        <span id="hitung-menit" class="text-2xl md:text-4xl font-extrabold text-emerald-700 leading-none">00</span>
                        <span class="text-[9px] md:text-xs text-emerald-600 uppercase tracking-widest font-bold mt-2">Menit</span>
                    </div>

                    <div class="text-xl md:text-3xl font-bold text-gray-300 mb-5 md:mb-6">:</div>

                    <div class="flex flex-col items-center justify-center bg-[#eefbf6] border border-emerald-200
                                rounded-xl w-16 h-20 md:w-20 md:h-24 shadow-sm">
                        <span id="hitung-detik" class="text-2xl md:text-4xl font-extrabold text-emerald-700 leading-none">00</span>
                        <span class="text-[9px] md:text-xs text-emerald-600 uppercase tracking-widest font-bold mt-2">Detik</span>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>


{{-- ============================================================ --}}
{{-- SECTION 5: JAVASCRIPT — Swiper, Countdown, Smart Hide        --}}
{{-- ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* 1. SWIPER HERO */
        (function() {
            const heroSwiperEl = document.querySelector('.heroSwiper');
            if (!heroSwiperEl) return;

            const IMAGE_DELAY = 5000;

            function handleVideoPlayback(swiper) {
                const activeSlide = swiper.slides[swiper.activeIndex];
                const activeVideo = activeSlide?.querySelector('video[data-slide-video]');

                swiper.slides.forEach(function(slide) {
                    const video = slide.querySelector('video[data-slide-video]');
                    if (!video) return;
                    if (video !== activeVideo) {
                        video.pause();
                        video.currentTime = 0;
                    }
                });

                if (activeVideo) {
                    swiper.autoplay.stop();
                    activeVideo.loop = false;
                    activeVideo.currentTime = 0;
                    const playPromise = activeVideo.play();
                    if (playPromise && typeof playPromise.catch === 'function') {
                        playPromise.catch(function() {
                            const once = function() {
                                activeVideo.play().catch(function() {});
                                document.removeEventListener('click', once);
                            };
                            document.addEventListener('click', once, { once: true });
                        });
                    }

                    activeVideo.onended = function() {
                        swiper.slideNext();
                    };
                } else {
                    if (!swiper.autoplay.running) {
                        swiper.autoplay.start();
                    }
                }
            }

            new Swiper('.heroSwiper', {
                loop: true,
                speed: 1000,
                effect: 'fade',
                fadeEffect: { crossFade: true },
                autoplay: {
                    delay: IMAGE_DELAY,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },
                observer: true,
                observeParents: true,
                on: {
                    init: function() { handleVideoPlayback(this); },
                    slideChangeTransitionStart: function() { handleVideoPlayback(this); },
                    transitionEnd: function() {
                        const slide = this.slides[this.activeIndex];
                        const video = slide?.querySelector('video[data-slide-video]');
                        if (video && video.paused) {
                            video.play().catch(function() {});
                        }
                    },
                },
            });
        })();

        /* 2. COUNTDOWN */
        const deadlineStr = "{{ $deadlineISO }}";
        let expiryTime = new Date(deadlineStr).getTime();

        if (isNaN(expiryTime)) {
            expiryTime = new Date(deadlineStr.replace(/-/g, '/').replace('T', ' ')).getTime();
        }

        const elHari = document.getElementById('hitung-hari');
        const elJam = document.getElementById('hitung-jam');
        const elMenit = document.getElementById('hitung-menit');
        const elDetik = document.getElementById('hitung-detik');

        if (elHari) {
            function updateCountdown() {
                const now = new Date().getTime();
                const diff = expiryTime - now;

                if (diff > 0) {
                    elHari.innerText = String(Math.floor(diff / (1000 * 60 * 60 * 24))).padStart(2, '0');
                    elJam.innerText = String(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                    elMenit.innerText = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                    elDetik.innerText = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');
                } else {
                    elHari.innerText = elJam.innerText = elMenit.innerText = elDetik.innerText = '00';
                }
            }
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        /* 3. SMART HIDE */
        (function() {
            const floatingCta = document.getElementById('floatingCta');
            const swiperArrows = document.querySelectorAll('.swiper-button-next, .swiper-button-prev');

            if (!floatingCta && swiperArrows.length === 0) return;

            const HIDE_THRESHOLD = 80;
            const SHOW_THRESHOLD = 20;

            let lastScrollY = window.pageYOffset;
            let ticking = false;
            let isHidden = false;

            function hideAll() {
                if (isHidden) return;
                isHidden = true;

                if (floatingCta) {
                    floatingCta.style.opacity = '0';
                    floatingCta.style.transform = 'translateY(200%) scale(0.9)';
                    floatingCta.style.pointerEvents = 'none';
                }

                swiperArrows.forEach(function(arrow) {
                    arrow.style.opacity = '0';
                    arrow.style.transform = 'scale(0.5)';
                    arrow.style.pointerEvents = 'none';
                });
            }

            function showAll() {
                if (!isHidden) return;
                isHidden = false;

                if (floatingCta) {
                    floatingCta.style.opacity = '1';
                    floatingCta.style.transform = 'translateY(50%) scale(1)';
                    floatingCta.style.pointerEvents = '';
                }

                swiperArrows.forEach(function(arrow) {
                    arrow.style.opacity = '';
                    arrow.style.transform = '';
                    arrow.style.pointerEvents = '';
                });
            }

            function updateVisibility() {
                const y = window.pageYOffset;

                if (y <= SHOW_THRESHOLD) {
                    showAll();
                } else if (y > lastScrollY && y > HIDE_THRESHOLD) {
                    hideAll();
                } else if (y < lastScrollY) {
                    showAll();
                }

                lastScrollY = y;
                ticking = false;
            }

            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(updateVisibility);
                    ticking = true;
                }
            }, { passive: true });

            showAll();
        })();
    });
</script>


{{-- ============================================================ --}}
{{-- SECTION 6: JENJANG PENDIDIKAN (RQ + TK)                       --}}
{{-- ============================================================ --}}
<section class="bg-gray-50" id="jenjang">

    <div class="text-center px-4 pt-12 md:pt-16 mb-10 md:mb-12">
        <h2 class="text-2xl md:text-3xl font-extrabold text-emerald-800 relative mx-auto max-w-sm md:max-w-none
                   leading-snug siap-animasi-teks">
            Daftar Pilihan <br class="block md:hidden"> Jenjang Pendidikan
            <span class="block mt-3 md:mt-4 h-1.5 w-32 bg-yellow-400 mx-auto rounded-full siap-animasi-garis"></span>
        </h2>
    </div>

    {{-- MOBILE --}}
    <div class="block md:hidden pb-10">
        <div class="flex flex-col justify-center px-4 py-6">
            <div class="flex flex-col gap-3">
                <div class="h-[450px] rounded-xl flex items-center justify-center shadow-lg relative overflow-hidden group bg-green-800">
                    <img
                        src="{{ asset('images/gambar RQ bg.webp') }}"
                        onerror="this.onerror=null; this.src='https://placehold.co/400x600/166534/ffffff?text=RUMAH+QUR\'AN'"
                        class="w-full h-full object-cover object-top transform group-hover:scale-105 transition duration-500"
                        alt="RUMAH QUR'AN">
                </div>

                <a href="{{ url('/register?jenjang=RQ') }}"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded shadow
                          text-center text-sm transition">
                    DAFTAR RUMAH QUR'AN &rarr;
                </a>

                <a href="#"
                    class="w-full py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded shadow
                          text-center text-sm transition">
                    INFO SPMB RQ
                </a>
            </div>
        </div>

        <div class="flex flex-col justify-center px-4 py-6">
            <div class="flex flex-col gap-3">
                <div class="h-[450px] rounded-xl flex items-center justify-center shadow-lg relative overflow-hidden group bg-purple-600">
                    <img
                        src="{{ asset('images/gambar TK, bg.webp') }}"
                        onerror="this.onerror=null; this.src='https://placehold.co/400x600/6b21a8/ffffff?text=TK'"
                        class="w-full h-full object-cover object-top transform group-hover:scale-105 transition duration-500"
                        alt="TKIT">
                </div>

                <a href="{{ url('/register?jenjang=TKIT') }}"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded shadow
                          text-center text-sm transition">
                    DAFTAR TK &rarr;
                </a>

                <a href="#"
                    class="w-full py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded shadow
                          text-center text-sm transition">
                    INFO SPMB TK
                </a>
            </div>
        </div>
    </div>

    {{-- DESKTOP --}}
    <div class="hidden md:flex min-h-[80vh] items-center">
        <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-2 gap-8">

                <div class="flex flex-col gap-3">
                    <div class="h-[24rem] lg:h-[28rem] rounded-xl flex items-center justify-center shadow-lg
                                relative overflow-hidden group bg-green-800">
                        <img
                            src="{{ asset('images/gambar RQ bg.webp') }}"
                            onerror="this.onerror=null; this.src='https://placehold.co/400x600/166534/ffffff?text=RUMAH+QURAN'"
                            class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                            alt="RUMAH QUR'AN">
                    </div>

                    <a href="{{ url('/register?jenjang=RQ') }}"
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded shadow
                              text-center text-base transition">
                        DAFTAR RUMAH QUR'AN &rarr;
                    </a>

                    <a href="#"
                        class="w-full py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded shadow
                              text-center text-sm transition">
                        INFO SPMB RQ
                    </a>
                </div>

                <div class="flex flex-col gap-3">
                    <div class="h-[24rem] lg:h-[28rem] rounded-xl flex items-center justify-center shadow-lg
                                relative overflow-hidden group bg-purple-600">
                        <img
                            src="{{ asset('images/gambar TK, bg.webp') }}"
                            onerror="this.onerror=null; this.src='https://placehold.co/400x600/6b21a8/ffffff?text=TK'"
                            class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                            alt="TKIT">
                    </div>

                    <a href="{{ url('/register?jenjang=TKIT') }}"
                        class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded shadow
                              text-center text-base transition">
                        DAFTAR TK &rarr;
                    </a>

                    <a href="#"
                        class="w-full py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded shadow
                              text-center text-sm transition">
                        INFO SPMB TK
                    </a>
                </div>

            </div>
        </div>
    </div>

</section>


{{-- ============================================================ --}}
{{-- SECTION 7: 3 KEUNGGULAN                                      --}}
{{-- ============================================================ --}}
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center px-4 mb-16 md:mb-20 w-full">
            <h2 id="judul-animasi"
                class="text-[20px] sm:text-2xl md:text-4xl font-extrabold text-[#183F3B] leading-snug
                       md:leading-normal mx-auto max-w-4xl siap-animasi-teks">
                <span class="block md:inline">3 KEUNGGULAN</span>
                <span class="block md:inline mt-1 md:mt-0">
                    RUMAH QUR'AN <span class="whitespace-nowrap">ASY-SYAMS !</span>
                </span>
            </h2>

            <div id="garis-animasi"
                class="w-16 md:w-32 h-1.5 bg-yellow-500 mx-auto mt-3 md:mt-5 rounded-full shadow-sm siap-animasi-garis"></div>
        </div>

        <div class="grid gap-8 grid-cols-1 md:grid-cols-3">

            {{-- Kartu 1: Pembinaan --}}
            <div class="group relative bg-[#F4F9F7] rounded-2xl p-8 border-l-[12px] border-[#183F3B] shadow-sm
                        hover:shadow-2xl hover:bg-white transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <span class="absolute top-4 right-6 text-7xl font-black text-[#183F3B] opacity-10
                           group-hover:opacity-20 transition-opacity duration-500 select-none pointer-events-none">
                    01
                </span>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-[#183F3B] text-white rounded-2xl flex items-center justify-center mb-10
                                shadow-lg group-hover:rotate-6 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-[#183F3B] mb-4 tracking-tight">Pembinaan</h3>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        Membina bacaan dan hafalan Al-Qur'an dari
                        <span class="font-bold text-green-700">Nol sampai Berprestasi</span>.
                    </p>
                </div>
            </div>

            {{-- Kartu 2: Pengajar --}}
            <div class="group relative bg-[#F4F9F7] rounded-2xl p-8 border-l-[12px] border-[#183F3B] shadow-sm
                        hover:shadow-2xl hover:bg-white transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <span class="absolute top-4 right-6 text-7xl font-black text-[#183F3B] opacity-10
                           group-hover:opacity-20 transition-opacity duration-500 select-none pointer-events-none">
                    02
                </span>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-[#183F3B] text-white rounded-2xl flex items-center justify-center mb-10
                                shadow-lg group-hover:rotate-6 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-[#183F3B] mb-4 tracking-tight">Pengajar</h3>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        Dibimbing langsung oleh para
                        <span class="font-bold text-green-700">Juara MTQ & MHQ</span>
                        tingkat Nasional & Internasional.
                    </p>
                </div>
            </div>

            {{-- Kartu 3: Bersanad --}}
            <div class="group relative bg-[#F4F9F7] rounded-2xl p-8 border-l-[12px] border-[#183F3B] shadow-sm
                        hover:shadow-2xl hover:bg-white transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <span class="absolute top-4 right-6 text-7xl font-black text-[#183F3B] opacity-10
                           group-hover:opacity-20 transition-opacity duration-500 select-none pointer-events-none">
                    03
                </span>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-[#183F3B] text-white rounded-2xl flex items-center justify-center mb-10
                                shadow-lg group-hover:rotate-6 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-[#183F3B] mb-4 tracking-tight">Bersanad</h3>
                    <p class="text-gray-600 leading-relaxed text-lg">
                        Ilmu Al-Qur'an yang diajarkan telah terverifikasi dan
                        <span class="font-bold text-green-700">Memiliki Sanad</span>
                        yang jelas.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- SECTION 8: SEJARAH YAYASAN + VIDEO PROFIL                    --}}
{{-- ============================================================ --}}
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            <div class="space-y-4 md:space-y-6">
                <h2 class="text-[22px] sm:text-2xl md:text-4xl font-extrabold text-emerald-800 leading-snug
                           md:leading-tight min-h-[66px] md:min-h-[48px]">
                    <span id="teks-ketik"></span><span class="kursor-kedip">|</span>
                </h2>

                <p class="text-gray-600 text-justify text-sm md:text-base leading-relaxed">
                    Rumah Qur'an Asy-Syams adalah sebuah fasilitator Pembelajaran yang berfokus pada pembinaan
                    Tahsin, Tahfizh dan Seni Baca Al-Qur'an dengan 7 Irama Al-Qur'an, baik dengan Murottal dan Mujawwad.
                    Serta mencetak kader-kader Juara MTQ & MHQ.
                </p>
            </div>

            <div class="relative w-full aspect-video rounded-2xl overflow-hidden shadow-2xl border-4 border-white ring-1 ring-gray-200">
                <iframe
                    class="absolute top-0 left-0 w-full h-full"
                    src="https://www.youtube.com/embed/ExTHDaHW9uw"
                    title="Profile YPTQ Asy-Syams"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen></iframe>
            </div>

        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- SECTION 9: KELAS YANG TERSEDIA                               --}}
{{-- ============================================================ --}}
<section class="py-16 bg-gray-50" id="kelas">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl inline-block relative siap-animasi-teks">
                Kelas di <span class="text-green-800">Rumah Qur'an Asy-Syams</span>
                <span class="block mt-4 h-1 bg-yellow-400 mx-auto rounded-full siap-animasi-garis"></span>
            </h2>
            <p class="mt-4 text-gray-500">
                Pilihan program pembelajaran Al-Qur'an sesuai kebutuhan Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Kelas Tahsin --}}
            <div class="group bg-white rounded-2xl p-7 border border-gray-100 shadow-sm
                        hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="mb-6">
                    <div class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center
                                group-hover:bg-green-600 group-hover:text-white group-hover:rotate-[360deg]
                                transition-all duration-700 ease-in-out shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-[#183F3B] mb-3 transition-colors group-hover:text-green-700">
                    Kelas Tahsin
                </h3>
                <p class="text-gray-500 leading-relaxed text-sm">
                    Program pembelajaran yang fokus pada memperbaiki cara membaca Al-Qur'an sesuai kaidah
                    tajwid dan makharijul huruf agar bacaan menjadi fasih dan benar.
                </p>
            </div>

            {{-- Kelas Murottal --}}
            <div class="group bg-white rounded-2xl p-7 border border-gray-100 shadow-sm
                        hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="mb-6">
                    <div class="w-14 h-14 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center
                                group-hover:bg-yellow-500 group-hover:text-white group-hover:rotate-[360deg]
                                transition-all duration-700 ease-in-out shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-[#183F3B] mb-3 transition-colors group-hover:text-yellow-600">
                    Kelas Murottal
                </h3>
                <p class="text-gray-500 leading-relaxed text-sm">
                    Kelas yang fokus pada melatih cara membaca Al-Qur'an dengan irama dan tartil yang indah
                    sesuai kaidah tajwid.
                </p>
            </div>

            {{-- Kelas Tilawah --}}
            <div class="group bg-white rounded-2xl p-7 border border-gray-100 shadow-sm
                        hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="mb-6">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center
                                group-hover:bg-blue-600 group-hover:text-white group-hover:rotate-[360deg]
                                transition-all duration-700 ease-in-out shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-[#183F3B] mb-3 transition-colors group-hover:text-blue-700">
                    Kelas Tilawah
                </h3>
                <p class="text-gray-500 leading-relaxed text-sm">
                    Pelajari seni lagu (Nagham). Kelas khusus ini mendalami berbagai jenis irama qira'ah
                    dan seni baca Al-Qur'an secara profesional.
                </p>
            </div>

        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- SECTION 10: BERITA & ARTIKEL                                 --}}
{{-- ============================================================ --}}
<section class="py-16 bg-gray-50" id="berita">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl inline-block relative siap-animasi-teks">
                Berita & <span class="text-emerald-600">Artikel</span> Yayasan
            </h2>
            <p class="mt-4 text-gray-500 max-w-2xl mx-auto">
                Informasi terbaru seputar kegiatan dan prestasi YPTQ Asy-Syams.
            </p>
        </div>

        {{-- GRID BERITA --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">

            @forelse($posts as $post)
            @php
            $hasImage = !empty($post->image);
            $imageSrc = $hasImage
                ? asset('storage/' . str_replace('\\', '/', $post->image))
                : null;

            $publishedAt = $post->published_at
                ? \Illuminate\Support\Carbon::parse($post->published_at)->translatedFormat('d M Y')
                : '-';

            $views = number_format((int) ($post->views ?? 0), 0, ',', '.');
            $excerpt = \Illuminate\Support\Str::limit(
                $post->excerpt ?? strip_tags($post->content ?? ''),
                130
            );

            $placeholder = <<<'HTML'
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 to-emerald-700
                            flex items-center justify-center text-white">
                    <div class="text-center">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-serif">Gambar Berita</p>
                    </div>
                </div>
            HTML;
            @endphp

            <article class="news-card group bg-white rounded-2xl overflow-hidden
                            border border-gray-100 shadow-sm
                            flex flex-col
                            hover:shadow-xl">

                <a href="{{ route('post.show', $post->slug) }}"
                    class="news-card-media"
                    aria-label="Baca: {{ $post->title }}">

                    @if($hasImage)
                    <img src="{{ $imageSrc }}"
                        alt="Gambar untuk artikel: {{ $post->title }}"
                        loading="lazy"
                        decoding="async"
                        onload="
                            if (!this.dataset.checked) {
                                this.dataset.checked = '1';
                                if (this.naturalHeight > this.naturalWidth) {
                                    this.classList.add('is-portrait');
                                }
                            }
                        "
                        onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">

                    <div class="hidden absolute inset-0">
                        {!! $placeholder !!}
                    </div>
                    @else
                    {!! $placeholder !!}
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"
                        aria-hidden="true"></div>

                    <div class="absolute top-4 left-4 z-10">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full
                                     bg-white/95 backdrop-blur-sm
                                     text-emerald-900 text-[10px] font-bold
                                     uppercase tracking-widest shadow-md whitespace-nowrap">
                            {{ $post->category ?? 'Berita' }}
                        </span>
                    </div>

                    <div class="absolute bottom-4 right-4 z-10
                                opacity-0 group-hover:opacity-100
                                translate-y-2 group-hover:translate-y-0
                                transition-all duration-300">
                        <div class="w-10 h-10 rounded-full bg-white/95 backdrop-blur-sm
                                    flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </div>
                    </div>
                </a>

                <div class="p-6 flex flex-col flex-1">

                    <h3 class="text-lg font-serif font-bold text-gray-900 leading-snug
                                line-clamp-2
                                group-hover:text-emerald-700
                                transition-colors duration-200">
                        <a href="{{ route('post.show', $post->slug) }}">
                            {{ \Illuminate\Support\Str::limit($post->title, 80) }}
                        </a>
                    </h3>

                    <p class="mt-3 text-gray-500 text-sm leading-relaxed line-clamp-3">
                        {{ $excerpt }}
                    </p>

                    <div class="mt-auto pt-5 flex items-center justify-between
                                text-xs text-gray-400 border-t border-gray-100">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <time datetime="{{ optional($post->published_at)->toIso8601String() }}">
                                {{ $publishedAt }}
                            </time>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>{{ $views }}</span>
                        </div>
                    </div>

                </div>
            </article>

            @empty
            <div class="col-span-full">
                <div class="bg-white border border-gray-100 rounded-3xl p-14 md:p-16 text-center shadow-sm">
                    <div class="mx-auto w-20 h-20 rounded-2xl bg-emerald-50 text-emerald-800
                                flex items-center justify-center mb-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-serif font-bold text-gray-900">Belum ada berita</h3>
                    <p class="mt-2 text-gray-500">Berita dan artikel terbaru akan tampil di halaman ini.</p>
                </div>
            </div>
            @endforelse

        </div>

        @if(isset($posts) && $posts->count() > 0 && method_exists($posts, 'hasPages') && $posts->hasPages())
        <div class="mt-12 text-center">
            <a href="{{ route('news.index') }}"
                class="group inline-flex items-center gap-2
                       bg-emerald-600 hover:bg-emerald-700 text-white
                       font-bold py-3 px-8 rounded-xl
                       shadow-md hover:shadow-lg
                       transition-all duration-300
                       transform hover:-translate-y-0.5">
                <span>Lihat Semua Berita</span>
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
        @endif

    </div>
</section>


{{-- ============================================================ --}}
{{-- SECTION 11: CONTACT — Alamat, Peta, Form Kirim Pesan        --}}
{{-- ============================================================ --}}
<section id="contact" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl inline-block relative siap-animasi-teks">
                Hubungi <span class="text-emerald-600">Kami</span>
            </h2>
            <p class="mt-4 text-gray-500 text-lg">
                Punya pertanyaan seputar pendaftaran atau program Rumah Qur'an Asy-Syams?
                <br>
                Silakan datang langsung atau kirim pesan kepada kami.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            <div class="space-y-8">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-600 hover:shadow-md transition">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-green-100 rounded-lg text-green-600 mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800">Alamat Kantor</h4>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Alamat : Jl. Tegal Sari <br>
                            Gg. Kemuning No. 9 kel. Umban sari Kec. Rumbai kota Pekanbaru
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-600 hover:shadow-md transition">
                        <div class="flex items-center mb-3">
                            <div class="p-2 bg-green-100 rounded-lg text-green-600 mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-800">Layanan Informasi</h4>
                        </div>
                        <p class="text-gray-600 text-sm">
                            <span class="block mb-1">WA: +62 853-7637-4040</span>
                        </p>
                    </div>

                </div>

                <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-200 h-80 relative overflow-hidden group">
                    <iframe
                        src="https://maps.google.com/maps?q=Rumah%20Qur'an%20Asy-Syams,%20Umban%20Sari,%20Pekanbaru&t=&z=16&ie=UTF8&iwloc=&output=embed"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="rounded-lg grayscale group-hover:grayscale-0 transition duration-500"></iframe>
                </div>

            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-orange-700 relative overflow-hidden">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Kirim Pesan</h3>

                @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded">
                    {{ session('success') }}
                </div>
                @endif

                <form action="{{ route('contact.send') }}" method="POST">
                    @csrf

                    <div class="space-y-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input
                                type="text"
                                name="name"
                                required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500
                                       transition outline-none"
                                placeholder="Contoh: Abdullah">
                            @error('name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300
                                           focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500
                                           transition outline-none"
                                    placeholder="email@contoh.com">
                                @error('email')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                                <input
                                    type="text"
                                    name="phone"
                                    required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300
                                           focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500
                                           transition outline-none"
                                    placeholder="0812xxx">
                                @error('phone')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Isi Pesan</label>
                            <textarea
                                name="message"
                                rows="4"
                                required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500
                                       transition outline-none"
                                placeholder="Tulis pertanyaan Anda di sini..."></textarea>
                            @error('message')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-orange-700 text-white font-bold py-3 px-6 rounded-lg
                                   hover:bg-orange-800 transition transform hover:-translate-y-1 shadow-lg
                                   flex justify-center items-center gap-2">
                            <span>Kirim Pesan Sekarang</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>

                    </div>
                </form>

                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-20 h-20 bg-yellow-100 rounded-full blur-2xl opacity-50 pointer-events-none"></div>
            </div>

        </div>
    </div>
</section>


{{-- ============================================================ --}}
{{-- SECTION 12: JAVASCRIPT — Typing & Reveal Animation           --}}
{{-- ============================================================ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* 1. TYPING EFFECT */
        (function() {
            const kalimat = "Rumah Qur'an Asy-Syams";
            const elemenTeks = document.getElementById("teks-ketik");
            if (!elemenTeks) return;

            let indexHuruf = 0;

            function ketikOtomatis() {
                if (indexHuruf < kalimat.length) {
                    elemenTeks.innerHTML += kalimat.charAt(indexHuruf);
                    indexHuruf++;
                    setTimeout(ketikOtomatis, 100);
                } else {
                    setTimeout(function() {
                        elemenTeks.innerHTML = "";
                        indexHuruf = 0;
                        ketikOtomatis();
                    }, 2000);
                }
            }

            const pantauKetik = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        setTimeout(ketikOtomatis, 300);
                        pantauKetik.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            pantauKeturun = pantauKetik.observe(elemenTeks);
        })();

        /* 2. REVEAL ANIMATION & HELPER SELECTORS */
        const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

        function cariGaris(judul) {
            return judul.querySelector(".siap-animasi-garis") ||
                (judul.nextElementSibling?.classList.contains("siap-animasi-garis") ? judul.nextElementSibling : null) ||
                judul.parentElement?.querySelector(".siap-animas-garis");
        }

        function jalankanAnimasiJudul(judul) {
            if (!judul || judul.dataset.done === "1") return;
            judul.dataset.done = "1";

            const garis = cariGaris(judul);
            judul.classList.remove("siap-animasi-teks");
            judul.classList.add("teks-bergerak");

            if (garis) {
                garis.classList.remove("siap-animasi-garis");
                garis.classList.add("garis-bergerak");
            }
        }

        const titleObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    jalankanAnimasiJudul(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.25,
            rootMargin: "0px 0px -55px 0px"
        });

        document.querySelectorAll(".siap-animasi-teks").forEach(function(judul) {
            if (reduceMotion) jalankanAnimasiJudul(judul);
            else titleObserver.observe(judul);
        });

        function siapkan(el, tipe = "js-reveal", delay = 0) {
            if (!el || el.dataset.revealReady) return;
            el.dataset.revealReady = "1";

            if (reduceMotion) return;

            tipe.split(" ").forEach(c => c && el.classList.add(c));
            el.style.setProperty("--reveal-delay", delay + "ms");
        }

        function munculkan(el) {
            if (!el || reduceMotion) return;
            if (el.classList.contains("reveal-active")) return;

            el.classList.add("reveal-active");

            el.addEventListener("animationend", function handler(e) {
                if (e.target !== el) return;
                el.classList.remove(
                    "js-reveal",
                    "js-reveal-scale",
                    "js-reveal-left",
                    "js-reveal-right",
                    "reveal-active"
                );
                el.style.removeProperty("--reveal-delay");
                el.removeEventListener("animationend", handler);
            });
        }

        const revealObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    munculkan(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.14,
            rootMargin: "0px 0px -45px 0px"
        });

        function observe(el) {
            if (!el) return;
            if (reduceMotion) return;
            revealObserver.observe(el);
        }

        const revealConfig = [
            { sel: ".heroSwiper", tipe: "js-reveal js-reveal-scale", delay: 0, immediate: true },
            { sel: "section .grid > div.group.relative", tipe: "js-reveal", delay: 120, multi: true },
            { sel: "#kelas .grid > div.group", tipe: "js-reveal", delay: 110, multi: true },
            { sel: "#berita article", tipe: "js-reveal", delay: 110, multi: true },
        ];

        revealConfig.forEach(function(item) {
            document.querySelectorAll(item.sel).forEach(function(el, i) {
                const target = item.wrap ? el.closest(item.wrap) : el;
                if (!target) return;

                siapkan(target, item.tipe, item.delay * i);

                if (item.immediate) {
                    setTimeout(() => munculkan(target), 120);
                } else {
                    observe(target);
                }
            });
        });

        ["hitung-hari", "hitung-jam", "hitung-menit", "hitung-detik"].forEach(function(id, index) {
            const angka = document.getElementById(id);
            const box = angka?.closest(".flex.flex-col.items-center.justify-center");
            if (box) {
                siapkan(box, "js-reveal", 220 + index * 80);
                observe(box);
            }
        });

        document.querySelectorAll("#contact .grid > div").forEach(function(item, i) {
            const arah = i === 0 ? "js-reveal-left" : "js-reveal-right";
            siapkan(item, "js-reveal " + arah, i * 130);
            observe(item);
        });

        document.querySelectorAll("#contact .bg-white.p-6, #contact iframe, #contact form .space-y-5 > div").forEach(function(item, i) {
            const target = item.tagName === "IFRAME" ? item.closest(".bg-white") : item;
            siapkan(target, "js-reveal", Math.min(i * 70, 300));
            observe(target);
        });

        document.querySelectorAll(".swiper-pagination, .custom-swiper-btn").forEach(function(item, i) {
            siapkan(item, "js-reveal", 250 + i * 80);
            setTimeout(() => munculkan(item), 420 + i * 80);
        });
    });
</script>

@endsection