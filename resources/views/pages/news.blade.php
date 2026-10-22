<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Artikel - YPTQ Asy-Syams</title>

    {{-- Guard anti-FOUC: aktifkan reveal SEBELUM paint pertama --}}
    <script>
        document.documentElement.classList.add('js-ready');
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ============================================================ --}}
    {{-- STYLE — Reveal, Card Hover, dan Media Container               --}}
    {{-- ============================================================ --}}
    <style>
        :root {
            --ease-apple: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-smooth: cubic-bezier(0.4, 0, 0.2, 1);
        }

        html {
            scroll-behavior: smooth;
        }

        /* -------------------------------------------------------- */
        /* REVEAL ANIMATION                                           */
        /* -------------------------------------------------------- */
        .js-ready .js-reveal {
            opacity: 0;
            transform: translate3d(0, 24px, 0);
            transition:
                opacity 700ms var(--ease-apple),
                transform 700ms var(--ease-apple);
            transition-delay: var(--reveal-delay, 0ms);
            will-change: opacity, transform;
        }

        .js-ready .js-reveal.reveal-active {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }

        .js-ready .js-reveal-scale {
            transform: translate3d(0, 24px, 0) scale(0.985);
        }

        .js-ready .js-reveal-scale.reveal-active {
            transform: translate3d(0, 0, 0) scale(1);
        }

        .js-ready .js-reveal-left {
            transform: translate3d(-24px, 0, 0);
        }

        .js-ready .js-reveal-right {
            transform: translate3d(24px, 0, 0);
        }

        .js-ready .js-reveal-left.reveal-active,
        .js-ready .js-reveal-right.reveal-active {
            transform: translate3d(0, 0, 0);
        }

        /* -------------------------------------------------------- */
        /* CARD HOVER — pakai `translate` (bukan transform)           */
        /* supaya tidak konflik dengan reveal animation               */
        /* -------------------------------------------------------- */
        .news-card {
            transition:
                box-shadow 400ms var(--ease-apple),
                translate 400ms var(--ease-apple);
        }

        .news-card:hover {
            translate: 0 -6px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        /* ============================================================ */
        /* MEDIA CONTAINER — KUNCI AGAR GRID RAPI                       */
        /* ============================================================ */
        .news-media {
            position: relative;
            display: block;
            width: 100%;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background-color: #064e3b;
        }

        @supports not (aspect-ratio: 4 / 3) {
            .news-media {
                height: 0;
                padding-bottom: 75%;
            }
        }

        .news-media img.news-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            transition: scale 700ms var(--ease-apple);
        }

        .news-card:hover .news-media img.news-image {
            scale: 1.05;
        }

        .news-media img.news-image.is-portrait {
            object-position: center 25%;
        }

        .news-media img.news-image.is-landscape {
            object-position: center;
        }

        .news-media-featured {
            aspect-ratio: 16 / 10;
        }

        @media (min-width: 1024px) {
            .news-media-featured {
                aspect-ratio: auto;
                height: 100%;
                min-height: 400px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .js-ready .js-reveal,
            .js-ready .js-reveal-scale,
            .js-ready .js-reveal-left,
            .js-ready .js-reveal-right {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }

            .news-card {
                transition: none;
            }

            .news-card:hover {
                translate: 0;
            }

            .news-media img.news-image {
                transition: none;
            }

            .news-card:hover .news-media img.news-image {
                scale: 1;
            }
        }
    </style>
</head>

<body class="bg-[#F9F8F6] text-gray-900 antialiased font-sans">

    <x-loader />
    @include('layouts.header')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

        {{-- BREADCRUMB --}}
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-sm text-gray-500 mb-8 md:mb-10">
            <a href="{{ url('/') }}" class="hover:text-emerald-800 transition-colors duration-200 ease-out">
                Beranda
            </a>
            <svg aria-hidden="true" class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-medium" aria-current="page">News & Artikel</span>
        </nav>


        {{-- HERO --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-14 items-center mb-14 md:mb-20">

            <div class="js-reveal js-reveal-left">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-10 h-[2px] bg-amber-600"></div>
                    <span class="text-xs font-bold tracking-widest text-emerald-900 uppercase">
                        YPTQ Asy-Syams
                    </span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif text-gray-900 leading-[1.1] tracking-tight">
                    News & <span class="text-emerald-800">Artikel</span>
                </h1>

                <p class="mt-5 text-base md:text-lg text-gray-600 leading-relaxed max-w-lg">
                    Cerita kegiatan, kabar prestasi, dan inspirasi dari keluarga besar YPTQ Asy-Syams. Temukan informasi terbaru kami di sini.
                </p>
            </div>

            <div class="relative js-reveal js-reveal-right" style="--reveal-delay: 120ms;">
                <div aria-hidden="true" class="absolute -top-10 -right-10 w-64 h-64 bg-emerald-100 rounded-full blur-3xl opacity-50 -z-10"></div>
                <div aria-hidden="true" class="absolute -bottom-10 -left-10 w-40 h-40 bg-amber-100 rounded-full blur-3xl opacity-50 -z-10"></div>

                <div class="bg-white p-7 md:p-10 rounded-3xl border border-gray-100
                            shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-800
                                flex items-center justify-center mb-6">
                        <svg aria-hidden="true" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>

                    <h2 class="text-xl md:text-2xl lg:text-3xl font-serif text-gray-800 leading-snug">
                        Berbagi cerita,<br>menumbuhkan inspirasi.
                    </h2>

                    <p class="mt-6 text-[10px] font-bold tracking-widest text-gray-400 uppercase">
                        Tilawah Center Pekanbaru
                    </p>
                </div>
            </div>

        </section>


        {{-- PREP DATA --}}
        @php
        $totalPosts = method_exists($posts, 'total') ? $posts->total() : $posts->count();

        $featuredPost = $featuredPost ?? null;
        if (!$featuredPost && $posts->count() > 0) {
        $featuredPost = $posts->first();
        }

        $gridPosts = $posts;
        if ($featuredPost) {
        if ($posts instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
        if ($posts->currentPage() === 1) {
        $gridPosts = $posts->getCollection()->skip(1);
        }
        } elseif ($posts->count() > 1) {
        $gridPosts = $posts->slice(1);
        }
        }

        $activeCategory = request('kategori', 'semua');

        $categoryOptions = $categories ?? [
        'semua' => 'Semua',
        'prestasi' => 'Prestasi',
        'kegiatan' => 'Kegiatan',
        'pengumuman' => 'Pengumuman',
        'pendidikan' => 'Pendidikan',
        ];

        $popularPosts = $popularPosts ?? collect();
        $archives = $archives ?? collect();

        $imgUrl = function ($img) {
        return $img
        ? \Illuminate\Support\Facades\Storage::url(str_replace('\\', '/', $img))
        : null;
        };

        $placeholderHtml = <<<'HTML'
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900 to-emerald-700
                            flex items-center justify-center text-white">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <p class="text-lg font-serif">Gambar Berita</p>
            </div>
            </div>
            HTML;
            @endphp


            {{-- SECTION 1 — BERITA UTAMA (FEATURED) --}}
            @if($featuredPost)
            @php
            $fpImg = $imgUrl($featuredPost->image ?? null);
            $fpDate = $featuredPost->published_at
            ? \Illuminate\Support\Carbon::parse($featuredPost->published_at)->translatedFormat('d F Y')
            : '-';
            $fpViews = number_format((int) ($featuredPost->views ?? 0), 0, ',', '.');
            $fpExcerpt = \Illuminate\Support\Str::limit(
            $featuredPost->excerpt ?? strip_tags($featuredPost->content ?? ''),
            180
            );
            @endphp

            <section class="mb-16 md:mb-20">
                <div class="flex items-center gap-4 mb-6 js-reveal">
                    <div class="w-10 h-[2px] bg-amber-600"></div>
                    <span class="text-xs font-bold tracking-widest text-emerald-900 uppercase">
                        Berita Utama
                    </span>
                </div>

                <article class="news-card group bg-white rounded-3xl overflow-hidden border border-gray-100
                                shadow-sm grid grid-cols-1 lg:grid-cols-2 js-reveal js-reveal-scale">

                    <a href="{{ route('post.show', $featuredPost->slug) }}"
                        class="news-media news-media-featured"
                        aria-label="Baca: {{ $featuredPost->title }}">

                        @if($fpImg)
                        <img src="{{ $fpImg }}"
                            alt="Gambar untuk artikel: {{ $featuredPost->title }}"
                            loading="eager"
                            fetchpriority="high"
                            decoding="async"
                            class="news-image"
                            onload="
                                    if (!this.dataset.checked) {
                                        this.dataset.checked = '1';
                                        if (this.naturalHeight > this.naturalWidth) this.classList.add('is-portrait');
                                        else if (this.naturalWidth > this.naturalHeight * 1.4) this.classList.add('is-landscape');
                                    }
                                 "
                            onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        <div class="hidden absolute inset-0">{!! $placeholderHtml !!}</div>
                        @else
                        {!! $placeholderHtml !!}
                        @endif

                        <div aria-hidden="true"
                            class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full
                                         bg-white/95 backdrop-blur text-emerald-900
                                         text-[10px] font-bold uppercase tracking-widest shadow-sm whitespace-nowrap">
                                {{ $featuredPost->category ?? 'Berita' }}
                            </span>
                        </div>
                    </a>

                    <div class="p-7 md:p-10 lg:p-12 flex flex-col justify-center">
                        <div class="flex items-center gap-3 text-xs text-gray-400 mb-4">
                            <time datetime="{{ optional($featuredPost->published_at)->toIso8601String() }}">
                                {{ $fpDate }}
                            </time>
                            <span aria-hidden="true">•</span>
                            <span>{{ $fpViews }} views</span>
                        </div>

                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-serif font-bold text-gray-900
                                   leading-snug group-hover:text-emerald-800
                                   transition-colors duration-200 ease-out">
                            <a href="{{ route('post.show', $featuredPost->slug) }}">
                                {{ \Illuminate\Support\Str::limit($featuredPost->title, 90) }}
                            </a>
                        </h2>

                        <p class="mt-4 text-gray-500 text-base leading-relaxed">
                            {{ $fpExcerpt }}
                        </p>

                        <div class="mt-7">
                            <a href="{{ route('post.show', $featuredPost->slug) }}"
                                class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-800
                                      hover:text-emerald-900 transition-colors duration-200 ease-out
                                      group/link">
                                Baca selengkapnya
                                <svg aria-hidden="true"
                                    class="w-4 h-4 transition-transform duration-200 group-hover/link:translate-x-1"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            </section>
            @endif


            {{-- SECTION 2 — HEADER + FILTER + GRID --}}
            <section>

                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5
                        mb-8 md:mb-10 js-reveal">
                    <div>
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-10 h-[2px] bg-amber-600"></div>
                            <span class="text-xs font-bold tracking-widest text-emerald-900 uppercase">
                                Informasi Terbaru
                            </span>
                        </div>

                        <h2 class="text-3xl md:text-4xl font-serif text-gray-900">
                            Kabar dari Asy-Syams
                        </h2>

                        <p class="mt-2 text-sm md:text-base text-gray-500">
                            Ikuti kegiatan dan perkembangan terbaru yayasan kami.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-3 bg-white px-5 py-2.5 rounded-full
                            border border-gray-200 shadow-sm self-start md:self-auto whitespace-nowrap">
                        <span class="text-lg md:text-xl font-bold text-emerald-800">{{ $totalPosts }}</span>
                        <span class="text-xs md:text-sm text-gray-500 font-medium">berita & artikel</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 mb-8 js-reveal" style="--reveal-delay: 80ms;">
                    @foreach($categoryOptions as $slug => $label)
                    @php
                    $isActive = $activeCategory === $slug;
                    $href = $slug === 'semua'
                    ? request()->url()
                    : request()->url() . '?kategori=' . urlencode($slug);
                    @endphp
                    <a href="{{ $href }}"
                        class="inline-flex items-center px-4 py-2 rounded-full text-xs md:text-sm font-semibold
                              transition-colors duration-200 ease-out border
                              {{ $isActive
                                  ? 'bg-emerald-800 text-white border-emerald-800 shadow-sm'
                                  : 'bg-white text-gray-600 border-gray-200 hover:border-emerald-300 hover:text-emerald-800' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7 md:gap-8">

                    @forelse($gridPosts as $post)
                    @php
                    $hasImage = !empty($post->image);
                    $imageSrc = $imgUrl($post->image);
                    $publishedAt = $post->published_at
                    ? \Illuminate\Support\Carbon::parse($post->published_at)->translatedFormat('d M Y')
                    : '-';
                    $views = number_format((int) ($post->views ?? 0), 0, ',', '.');
                    $excerpt = \Illuminate\Support\Str::limit(
                    $post->excerpt ?? strip_tags($post->content ?? ''),
                    135
                    );
                    @endphp

                    <article class="news-card group bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm
                                    flex flex-col h-full js-reveal"
                        style="--reveal-delay: {{ $loop->index * 80 }}ms;">

                        <a href="{{ route('post.show', $post->slug) }}"
                            class="news-media"
                            aria-label="Baca: {{ $post->title }}">

                            @if($hasImage)
                            <img src="{{ $imageSrc }}"
                                alt="Gambar untuk artikel: {{ $post->title }}"
                                loading="lazy"
                                decoding="async"
                                class="news-image"
                                onload="
                                        if (!this.dataset.checked) {
                                            this.dataset.checked = '1';
                                            if (this.naturalHeight > this.naturalWidth) this.classList.add('is-portrait');
                                            else if (this.naturalWidth > this.naturalHeight * 1.4) this.classList.add('is-landscape');
                                        }
                                     "
                                onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                            <div class="hidden absolute inset-0">{!! $placeholderHtml !!}</div>
                            @else
                            {!! $placeholderHtml !!}
                            @endif

                            <div aria-hidden="true"
                                class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>

                            <div class="absolute top-4 left-4">
                                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full
                                             bg-white/95 backdrop-blur text-emerald-900
                                             text-[10px] font-bold uppercase tracking-widest shadow-sm whitespace-nowrap">
                                    {{ $post->category ?? 'Berita' }}
                                </span>
                            </div>
                        </a>

                        <div class="p-6 md:p-7 flex flex-col flex-1">
                            <h3 class="text-lg md:text-xl font-serif font-bold text-gray-900 leading-snug
                                       group-hover:text-emerald-800 transition-colors duration-200 ease-out">
                                <a href="{{ route('post.show', $post->slug) }}">
                                    {{ \Illuminate\Support\Str::limit($post->title, 70) }}
                                </a>
                            </h3>

                            <p class="mt-3 text-gray-500 text-sm leading-relaxed flex-grow">
                                {{ $excerpt }}
                            </p>

                            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between
                                        text-xs text-gray-400">
                                <div class="flex items-center gap-1.5">
                                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <time datetime="{{ optional($post->published_at)->toIso8601String() }}">
                                        {{ $publishedAt }}
                                    </time>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg aria-hidden="true" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>{{ $views }} views</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    @empty
                    <div class="lg:col-span-3 md:col-span-2">
                        <div class="bg-white border border-gray-100 rounded-3xl p-14 md:p-16
                                    text-center shadow-sm">
                            <div class="mx-auto w-20 h-20 rounded-2xl bg-emerald-50 text-emerald-800
                                        flex items-center justify-center mb-6">
                                <svg aria-hidden="true" class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-serif font-bold text-gray-900">Belum ada berita lain</h3>
                            <p class="mt-2 text-gray-500">Berita dan artikel terbaru akan tampil di halaman ini.</p>
                        </div>
                    </div>
                    @endforelse

                </div>


                @if(method_exists($posts, 'links') && $posts->hasPages())
                <div class="mt-12 md:mt-16 js-reveal">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4 overflow-x-auto">
                        {{ $posts->links() }}
                    </div>
                </div>
                @endif

            </section>


            {{-- SECTION 3 — ARSIP & POPULER --}}
            @if(($archives->count() ?? 0) > 0 || ($popularPosts->count() ?? 0) > 0)

            <section class="mt-16 md:mt-20 pt-14 md:pt-16 border-t border-gray-200/70">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-12">

                    @if($archives->count() > 0)
                    <div class="lg:col-span-2 js-reveal">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-[2px] bg-amber-600"></div>
                            <h2 class="text-xs font-bold tracking-widest text-emerald-900 uppercase">
                                Arsip Berita
                            </h2>
                        </div>

                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($archives as $arc)
                            @php
                            $arcLabel = is_array($arc) ? ($arc['label'] ?? '-')
                            : (is_object($arc) ? ($arc->label ?? '-') : $arc);
                            $arcCount = is_array($arc) ? ($arc['count'] ?? null)
                            : (is_object($arc) ? ($arc->count ?? null) : null);
                            $arcUrl = is_array($arc) ? ($arc['url'] ?? '#')
                            : (is_object($arc) ? ($arc->url ?? '#') : '#');
                            @endphp
                            <li>
                                <a href="{{ $arcUrl }}"
                                    class="flex items-center justify-between gap-3 px-5 py-3.5 bg-white
                                                  rounded-2xl border border-gray-100 shadow-sm
                                                  hover:border-emerald-300 hover:text-emerald-800
                                                  transition-colors duration-200 ease-out group/arc">
                                    <span class="flex items-center gap-3">
                                        <svg aria-hidden="true" class="w-4 h-4 text-emerald-700"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm font-medium">{{ $arcLabel }}</span>
                                    </span>
                                    @if(!is_null($arcCount))
                                    <span class="text-xs text-gray-400 group-hover/arc:text-emerald-700">
                                        {{ $arcCount }} berita
                                    </span>
                                    @endif
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($popularPosts->count() > 0)
                    <div class="js-reveal" style="--reveal-delay: 120ms;">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-[2px] bg-amber-600"></div>
                            <h2 class="text-xs font-bold tracking-widest text-emerald-900 uppercase">
                                Paling Dibaca
                            </h2>
                        </div>

                        <ol class="space-y-4">
                            @foreach($popularPosts->take(5) as $pop)
                            @php
                            $popViews = number_format((int) ($pop->views ?? 0), 0, ',', '.');
                            @endphp
                            <li>
                                <a href="{{ route('post.show', $pop->slug) }}"
                                    class="flex items-start gap-4 group/pop">
                                    <span class="shrink-0 w-8 text-2xl font-serif font-bold text-emerald-800/30
                                                         group-hover/pop:text-emerald-800 transition-colors duration-200">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold text-gray-800 leading-snug
                                                             group-hover/pop:text-emerald-800 transition-colors duration-200">
                                            {{ \Illuminate\Support\Str::limit($pop->title, 70) }}
                                        </span>
                                        <span class="mt-1 flex items-center gap-1.5 text-xs text-gray-400">
                                            <svg aria-hidden="true" class="w-3.5 h-3.5"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ $popViews }} views
                                        </span>
                                    </span>
                                </a>
                            </li>
                            @endforeach
                        </ol>
                    </div>
                    @endif

                </div>
            </section>
            @endif

    </main>

    @includeIf('layouts.footer')


    <script>
        (function() {
            const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

            function init() {
                document.documentElement.classList.add('js-ready');

                if (reduceMotion) {
                    document.querySelectorAll('main .js-reveal').forEach(function(el) {
                        el.classList.add('reveal-active');
                    });
                    return;
                }

                const revealObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('reveal-active');
                            observer.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.14,
                    rootMargin: "0px 0px -45px 0px"
                });

                document.querySelectorAll('main .js-reveal').forEach(function(el) {
                    revealObserver.observe(el);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>

</body>

</html>