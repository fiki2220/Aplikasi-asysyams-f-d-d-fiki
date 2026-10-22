@php
    use App\Features\SiteSettings\Models\SiteSetting;

    try {
        $footerSetting = SiteSetting::where('key', 'footer_bg')->first();
        $footerBgValue = $footerSetting ? $footerSetting->value : null;

        $firstFooterImage = null;

        if (is_string($footerBgValue)) {
            $decoded = json_decode($footerBgValue, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                $firstFooterImage = $decoded[0];
            } elseif (!empty($footerBgValue)) {
                $firstFooterImage = $footerBgValue;
            }
        } elseif (is_array($footerBgValue) && count($footerBgValue) > 0) {
            $firstFooterImage = $footerBgValue[0];
        }

        $firstFooterImage = $firstFooterImage
            ? ltrim(str_replace('\\', '/', $firstFooterImage), '/')
            : null;

        if ($firstFooterImage && str_starts_with($firstFooterImage, 'storage/')) {
            $firstFooterImage = substr($firstFooterImage, strlen('storage/'));
        }

        if ($firstFooterImage && str_starts_with($firstFooterImage, 'public/')) {
            $firstFooterImage = substr($firstFooterImage, strlen('public/'));
        }

    } catch (\Throwable $e) {
        $firstFooterImage = null;
    }

    $footerImage = $firstFooterImage
        ? asset('storage/' . $firstFooterImage)
        : asset('images/footer-default.png');
@endphp

<style>
    @keyframes footerFadeUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    .footer-fade {
        animation: footerFadeUp .7s ease-out both;
    }

    .footer-delay-1 {
        animation-delay: .1s;
    }

    .footer-delay-2 {
        animation-delay: .2s;
    }

    .footer-delay-3 {
        animation-delay: .3s;
    }

    .footer-logo-animate {
        animation: footerLogoGlow 6s ease-in-out infinite;
    }

    .footer-social-icon {
        transition: all .25s ease;
    }

    .footer-social-icon svg {
        width: 17px;
        height: 17px;
    }

    .footer-social-icon:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(250, 204, 21, .18);
    }

    .footer-link {
        display: inline-block;
        transition: color .25s ease, transform .25s ease;
    }

    .footer-link:hover {
        color: #facc15;
        transform: translateX(3px);
    }

    @media (max-width: 640px) {
        .footer-social-icon:hover,
        .footer-link:hover {
            transform: none;
        }
    }
</style>

<footer class="relative w-full overflow-hidden bg-[#032c24] text-white">

    <!-- CTA FOOTER -->
    <section class="relative w-full overflow-hidden bg-[#032c24]">
        <div class="relative w-full aspect-[1920/700] overflow-hidden bg-[#032c24]">

            <img
                src="{{ $footerImage }}"
                alt="Footer Background"
                class="absolute inset-0 w-full h-full object-cover object-center"
                onerror="this.onerror=null; this.style.display='none';"
            >

            <div class="absolute inset-0 bg-gradient-to-r from-[#032c24]/96 via-[#032c24]/78 to-[#032c24]/20"></div>
            <div class="absolute inset-0 bg-black/10"></div>

            <div class="absolute inset-0 z-10 flex items-center">
                <div class="max-w-7xl mx-auto w-full px-5 sm:px-6 lg:px-8">

                    <div class="max-w-[65%] md:max-w-3xl">

                        <div class="footer-fade flex items-center gap-2 md:gap-3 mb-1.5 md:mb-4">
                            <span class="w-5 sm:w-7 md:w-10 h-[2px] bg-yellow-400"></span>

                            <span class="text-yellow-400 text-[6px] sm:text-[9px] md:text-xs font-extrabold tracking-[0.22em] md:tracking-[0.38em] uppercase">
                                YPTQ ASY-SYAMS
                            </span>

                            <span class="w-5 sm:w-7 md:w-10 h-[2px] bg-yellow-400 hidden sm:block"></span>
                        </div>

                        <h2 class="footer-fade footer-delay-1 text-white text-[13px] min-[390px]:text-[15px] sm:text-2xl md:text-5xl lg:text-6xl font-black leading-[1.08] tracking-tight">
                            Mari Bergabung Bersama Kami
                            <span class="block text-yellow-400 mt-0.5 md:mt-2">
                                Menjadi Generasi Qur’ani
                            </span>
                        </h2>

                        <p class="footer-fade footer-delay-2 mt-1.5 sm:mt-3 md:mt-5 max-w-[210px] sm:max-w-md md:max-w-2xl text-white/85 text-[7px] min-[390px]:text-[8px] sm:text-xs md:text-lg leading-relaxed">
                            Bersama YPTQ Asy-Syams, wujudkan pendidikan Al-Qur’an yang berkualitas,
                            berakhlak mulia, dan berprestasi.
                        </p>

                        <div class="footer-fade footer-delay-3 mt-2.5 sm:mt-4 md:mt-9 flex flex-row gap-1.5 sm:gap-2 md:gap-3">
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex items-center justify-center gap-1 md:gap-2 bg-yellow-400 hover:bg-yellow-500 text-[#032c24] font-extrabold py-1.5 px-3 sm:py-2 sm:px-4 md:py-3.5 md:px-8 rounded-full shadow-[0_16px_40px_rgba(250,204,21,0.22)] transition-all duration-300 hover:scale-[1.03] text-[7px] min-[390px]:text-[8px] sm:text-xs md:text-base whitespace-nowrap"
                            >
                                Daftar Sekarang

                                <svg class="w-3 h-3 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3"
                                    />
                                </svg>
                            </a>

                            <a
                                href="#contact"
                                class="inline-flex items-center justify-center border border-white/35 hover:bg-white/10 text-white font-bold py-1.5 px-3 sm:py-2 sm:px-4 md:py-3.5 md:px-8 rounded-full transition-all duration-300 text-[7px] min-[390px]:text-[8px] sm:text-xs md:text-base whitespace-nowrap"
                            >
                                Hubungi Kami
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- FOOTER INFO -->
    <section class="relative bg-[#032c24] border-t-[3px] border-yellow-400">
        <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8 py-8 md:py-12">

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-8 md:gap-8">

                <!-- BRAND -->
                <div class="md:col-span-4">
                    <div class="flex items-center gap-3 md:gap-4 mb-4">
                        <div class="footer-logo-animate shrink-0 rounded-full">
                            <img
                                src="{{ asset('images/logo.PNG') }}"
                                alt="Logo YPTQ Asy-Syams"
                                class="w-12 h-12 md:w-16 md:h-16 object-contain rounded-full bg-white shadow-xl p-1"
                                onerror="this.onerror=null; this.style.display='none';"
                            >
                        </div>

                        <div>
                            <h3 class="text-xl md:text-2xl font-black tracking-widest font-serif leading-tight">
                                YPTQ ASY-SYAMS
                            </h3>

                            <p class="text-yellow-400 text-[8px] md:text-[10px] tracking-[0.26em] uppercase mt-1 font-bold">
                                Tilawah Center Pekanbaru
                            </p>
                        </div>
                    </div>

                    <p class="text-white/70 text-sm leading-relaxed max-w-sm">
                        Yayasan Pendidikan Tilawatil Qur’an Asy-Syams berfokus pada pembinaan
                        Tahsin, Tahfizh, Tilawah, serta pembentukan karakter Islami.
                    </p>

                    <div class="flex items-center gap-3 mt-5">

                        <a
                            href="https://www.facebook.com/rq.asy.syams.rumbai.pekanbaru"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Facebook"
                            class="footer-social-icon w-10 h-10 rounded-full border border-yellow-400/50 text-yellow-400 flex items-center justify-center hover:bg-yellow-400 hover:text-[#032c24]"
                        >
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12.07C22 6.48 17.52 2 11.93 2S2 6.48 2 12.07c0 5.02 3.66 9.18 8.44 9.96v-7.05H7.9v-2.91h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.23.2 2.23.2v2.45h-1.26c-1.24 0-1.63.77-1.63 1.56v1.91h2.78l-.44 2.91h-2.34v7.05C18.34 21.25 22 17.09 22 12.07z"/>
                            </svg>
                        </a>

                        <a
                            href="https://www.instagram.com/rq_asy.syams?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Instagram"
                            class="footer-social-icon w-10 h-10 rounded-full border border-yellow-400/50 text-yellow-400 flex items-center justify-center hover:bg-yellow-400 hover:text-[#032c24]"
                        >
                            <svg fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                                <circle cx="12" cy="12" r="4"></circle>
                                <circle cx="17.5" cy="6.5" r="1"></circle>
                            </svg>
                        </a>

                        <a
                            href="https://youtube.com/@rq_asysyams?si=C3Bk6xwr0f1DKlWU"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="YouTube"
                            class="footer-social-icon w-10 h-10 rounded-full border border-yellow-400/50 text-yellow-400 flex items-center justify-center hover:bg-yellow-400 hover:text-[#032c24]"
                        >
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.5 6.2s-.2-1.7-.9-2.4c-.9-.9-1.9-.9-2.4-1C16.8 2.5 12 2.5 12 2.5s-4.8 0-8.2.3c-.5.1-1.5.1-2.4 1C.7 4.5.5 6.2.5 6.2S.2 8.2.2 10.3v1.9c0 2.1.3 4.1.3 4.1s.2 1.7.9 2.4c.9.9 2.1.9 2.6 1 1.9.2 8 .3 8 .3s4.8 0 8.2-.3c.5-.1 1.5-.1 2.4-1 .7-.7.9-2.4.9-2.4s.3-2.1.3-4.1v-1.9c0-2.1-.3-4.1-.3-4.1zM9.7 14.8V7.9l6.3 3.5-6.3 3.4z"/>
                            </svg>
                        </a>

                        <a
                            href="https://wa.me/6285376374040"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="WhatsApp"
                            class="footer-social-icon w-10 h-10 rounded-full border border-yellow-400/50 text-yellow-400 flex items-center justify-center hover:bg-yellow-400 hover:text-[#032c24]"
                        >
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.04 2C6.57 2 2.13 6.44 2.13 11.91c0 1.75.46 3.46 1.34 4.97L2 22l5.25-1.38a9.86 9.86 0 0 0 4.79 1.22h.01c5.47 0 9.91-4.44 9.91-9.91C21.96 6.44 17.52 2 12.04 2zm5.78 14.18c-.24.67-1.39 1.28-1.94 1.36-.5.07-1.13.1-1.82-.11-.42-.13-.95-.31-1.64-.61-2.88-1.24-4.76-4.13-4.9-4.32-.14-.19-1.17-1.56-1.17-2.98s.74-2.12 1-2.41c.26-.29.57-.36.76-.36h.55c.17 0 .42-.06.65.5.24.58.82 2 .89 2.14.07.14.12.31.02.5-.1.19-.14.31-.29.48-.14.17-.3.38-.43.51-.14.14-.29.29-.12.57.17.29.76 1.25 1.63 2.02 1.12 1 2.06 1.31 2.35 1.45.29.14.45.12.62-.07.19-.22.72-.84.91-1.13.19-.29.38-.24.65-.14.26.1 1.68.79 1.97.94.29.14.48.22.55.34.07.12.07.7-.17 1.37z"/>
                            </svg>
                        </a>

                    </div>
                </div>

                <!-- PROGRAM -->
                <div class="md:col-span-2">
                    <h4 class="text-yellow-400 font-extrabold uppercase tracking-[0.22em] mb-3 text-sm">
                        Program
                    </h4>

                    <ul class="space-y-2 text-white/75 text-sm">
                        <li><a href="#kelas" class="footer-link">Tahsin</a></li>
                        <li><a href="#kelas" class="footer-link">Tahfizh</a></li>
                        <li><a href="#kelas" class="footer-link">Tilawah</a></li>
                        <li><a href="#kelas" class="footer-link">Murottal</a></li>
                    </ul>
                </div>

                <!-- INFORMASI -->
                <div class="md:col-span-2">
                    <h4 class="text-yellow-400 font-extrabold uppercase tracking-[0.22em] mb-3 text-sm">
                        Informasi
                    </h4>

                    <ul class="space-y-2 text-white/75 text-sm">
                        <li><a href="{{ route('register') }}" class="footer-link">Pendaftaran</a></li>
                        <li><a href="#berita" class="footer-link">Berita</a></li>
                        <li><a href="#jenjang" class="footer-link">Jenjang</a></li>
                        <li><a href="#contact" class="footer-link">Kontak</a></li>
                    </ul>
                </div>

                <!-- KONTAK -->
                <div class="md:col-span-4">
                    <h4 class="text-yellow-400 font-extrabold uppercase tracking-[0.22em] mb-3 text-sm">
                        Kontak
                    </h4>

                    <ul class="space-y-2.5 text-white/75 text-sm leading-relaxed">
                        <li>📍 Jl. Tegal Sari, Gg. Kemuning No. 9, Umban Sari, Rumbai, Pekanbaru</li>
                        <li>☎ +62 853-7637-4040</li>
                        <li>✉ rqasysyams23@gmail.com</li>
                    </ul>
                </div>

            </div>

            <div class="w-full h-px bg-gradient-to-r from-transparent via-yellow-400/40 to-transparent my-7 md:my-8"></div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-3 text-center">
                <p class="text-white/55 text-xs md:text-sm">
                    © {{ date('Y') }} Yayasan Pendidikan Tilawatil Qur'an Asy-Syams Pekanbaru. All rights reserved.
                </p>

                <p class="text-yellow-400/80 text-xs md:text-sm font-semibold tracking-widest uppercase">
                    YPTQ Asy-Syams
                </p>
            </div>

        </div>
    </section>

</footer>