<x-app-layout>
    @verbatim
    <style>
        .asy-profile {
            --green: #064e3b;
            --ink: #16352d;
            --muted: #53665e;
            --line: #dce5df;
            --sage: #f3f7f4;
            --gold: #997333;
            color: var(--ink);
            background: #ffffff;
            font: 16px/1.75 system-ui, -apple-system,
                BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .asy-profile * {
            box-sizing: border-box;
        }

        .asy-profile .wrap {
            width: min(1120px, calc(100% - 48px));
            margin-inline: auto;
        }

        .asy-profile h1,
        .asy-profile h2,
        .asy-profile h3,
        .asy-profile h4,
        .asy-profile p {
            margin: 0;
        }

        .asy-profile h1,
        .asy-profile h2 {
            font-family: Georgia, "Times New Roman", serif;
            font-weight: 500;
            letter-spacing: -0.035em;
            text-wrap: balance;
        }

        .asy-profile h1 {
            font-size: clamp(34px, 4.1vw, 50px);
            line-height: 1.16;
        }

        .asy-profile h2 {
            font-size: clamp(28px, 3vw, 38px);
            line-height: 1.25;
        }

        .asy-profile h3 {
            font-size: 17px;
            font-weight: 650;
            line-height: 1.4;
        }

        .asy-profile a {
            color: inherit;
            text-decoration: none;
        }

        .asy-profile a:focus-visible {
            outline: 3px solid #b68a3c;
            outline-offset: 5px;
        }

        .asy-profile .eyebrow {
            margin-bottom: 16px;
            color: var(--green);
            font-size: 11px;
            font-weight: 750;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        /* Pengantar halaman */
        .asy-profile .hero {
            padding: 28px 0 52px;
            background: var(--sage);
            border-bottom: 1px solid var(--line);
        }

        .asy-profile .crumb {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
            color: var(--muted);
            font-size: 12px;
        }

        .asy-profile .crumb a:hover {
            text-decoration: underline;
        }

        .asy-profile .intro {
            max-width: 680px;
            margin-top: 20px;
            color: var(--muted);
            line-height: 1.8;
        }

        /* Navigasi bagian */
        .asy-profile .section-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--line);
        }

        .asy-profile .section-nav .wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
        }

        .asy-profile .section-nav a {
            display: block;
            padding: 18px 0;
            border-bottom: 2px solid transparent;
            font-size: 13px;
            font-weight: 600;
        }

        .asy-profile .section-nav a:hover {
            color: var(--green);
            border-bottom-color: var(--green);
        }

        .asy-profile section[id] {
            scroll-margin-top: 32px;
        }

        /* Profil yayasan */
        .asy-profile .about {
            padding: 72px 0;
        }

        .asy-profile .about-grid {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            align-items: start;
            gap: 56px;
        }

        .asy-profile .identity {
            padding: 40px 28px;
            background: var(--sage);
            border: 1px solid var(--line);
            border-radius: 12px;
            text-align: center;
        }

        .asy-profile .identity img {
            display: block;
            width: 164px;
            height: 164px;
            margin: 0 auto 24px;
            object-fit: contain;
        }

        .asy-profile .identity h3 {
            color: var(--green);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 23px;
        }

        .asy-profile .identity p {
            margin-top: 8px;
            color: var(--muted);
            font-size: 12px;
        }

        .asy-profile .place {
            display: inline-block;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid var(--line);
            font-size: 11px;
            letter-spacing: 0.06em;
        }

        .asy-profile .story {
            margin-top: 20px;
            color: var(--muted);
        }

        .asy-profile .story p + p {
            margin-top: 14px;
        }

        .asy-profile .features {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px solid var(--line);
        }

        .asy-profile .feature-mark {
            display: block;
            margin-bottom: 8px;
            color: var(--gold);
            font-size: 18px;
        }

        .asy-profile .features h3 {
            margin-bottom: 6px;
            font-size: 14px;
        }

        .asy-profile .features p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }

        /* Visi dan misi */
        .asy-profile .direction {
            padding: 64px 0;
            background: var(--sage);
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }

        .asy-profile .direction-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
            align-items: stretch;
            gap: 48px;
            margin-top: 32px;
        }

        .asy-profile .vision {
            padding: 32px;
            color: #ffffff;
            background: var(--green);
            border-radius: 12px;
        }

        .asy-profile .vision .eyebrow {
            margin-bottom: 20px;
            color: #dfd1af;
        }

        .asy-profile .vision p {
            font-family: Georgia, "Times New Roman", serif;
            font-size: 22px;
            line-height: 1.6;
        }

        .asy-profile .mission > h3 {
            margin-bottom: 12px;
        }

        .asy-profile .mission ol {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .asy-profile .mission li {
            display: grid;
            grid-template-columns: 30px minmax(0, 1fr);
            gap: 16px;
            padding: 17px 0;
            border-bottom: 1px solid var(--line);
        }

        .asy-profile .mission li:last-child {
            border-bottom: 0;
        }

        .asy-profile .number {
            padding-top: 2px;
            color: var(--gold);
            font-size: 12px;
            font-weight: 700;
        }

        .asy-profile .mission h4 {
            margin-bottom: 4px;
            font-size: 14px;
            font-weight: 650;
        }

        .asy-profile .mission p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        /* Informasi pendaftaran */
        .asy-profile .enroll {
            padding: 56px 0 64px;
        }

        .asy-profile .cta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
            padding: 36px 40px;
            color: #ffffff;
            background: var(--green);
            border-radius: 12px;
        }

        .asy-profile .cta h2 {
            font-size: 30px;
        }

        .asy-profile .cta p {
            max-width: 480px;
            margin-top: 10px;
            color: #dae8e0;
            font-size: 14px;
        }

        .asy-profile .actions {
            display: flex;
            flex-shrink: 0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .asy-profile .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 46px;
            padding: 12px 18px;
            color: var(--green);
            background: #f6f1e5;
            border: 1px solid #f6f1e5;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            transition: background 0.15s;
        }

        .asy-profile .button:hover {
            background: #e8dec5;
        }

        .asy-profile .button.secondary {
            color: #ffffff;
            background: transparent;
            border-color: #91afa0;
        }

        .asy-profile .button.secondary:hover {
            background: #17634e;
        }

        /* Tablet */
        @media (max-width: 900px) {
            .asy-profile .about-grid {
                grid-template-columns: 260px minmax(0, 1fr);
                gap: 32px;
            }

            .asy-profile .direction-grid {
                gap: 28px;
            }

            .asy-profile .cta {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Ponsel */
        @media (max-width: 640px) {
            .asy-profile .wrap {
                width: calc(100% - 36px);
            }

            .asy-profile .hero {
                padding: 20px 0 36px;
            }

            .asy-profile .crumb {
                margin-bottom: 24px;
            }

            .asy-profile .section-nav .wrap {
                gap: 20px;
            }

            .asy-profile .section-nav a {
                padding: 15px 0;
                font-size: 12px;
            }

            .asy-profile .about {
                padding: 40px 0;
            }

            .asy-profile .about-grid,
            .asy-profile .direction-grid {
                grid-template-columns: minmax(0, 1fr);
                gap: 28px;
            }

            .asy-profile .identity {
                padding: 24px;
            }

            .asy-profile .identity img {
                width: 112px;
                height: 112px;
                margin-bottom: 16px;
            }

            .asy-profile .place {
                margin-top: 16px;
            }

            .asy-profile .features {
                gap: 20px;
            }

            .asy-profile .direction {
                padding: 40px 0;
            }

            .asy-profile .vision {
                padding: 26px;
            }

            .asy-profile .vision p {
                font-size: 21px;
            }

            .asy-profile .enroll {
                padding: 36px 0;
            }

            .asy-profile .cta {
                padding: 28px 24px;
            }

            .asy-profile .cta h2 {
                font-size: 28px;
            }

            .asy-profile .actions {
                width: 100%;
            }

            .asy-profile .button {
                flex: 1;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .asy-profile * {
                transition: none !important;
            }
        }
    </style>
    @endverbatim

    <div class="asy-profile">

        {{-- 1. Pengantar halaman --}}
        <header class="hero">
            <div class="wrap">
                <nav class="crumb" aria-label="Breadcrumb">
                    <a href="{{ url('/') }}">Beranda</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">Profil Yayasan</span>
                </nav>

                <p class="eyebrow">Tentang YPTQ Asy-Syams</p>

                <h1>Profil Yayasan</h1>

                <p class="intro">
                    Mengenal lebih dekat visi, misi, dan perjalanan
                    Yayasan Pendidikan Tilawatil Qur’an Asy-Syams
                    dalam mencetak generasi Qur’ani yang berwawasan global.
                </p>
            </div>
        </header>

        {{-- 2. Navigasi bagian --}}
        <nav class="section-nav" aria-label="Bagian halaman profil">
            <div class="wrap">
                <a href="#tentang-yayasan">Tentang Yayasan</a>
                <a href="#visi-misi">Visi &amp; Misi</a>
                <a href="#pendaftaran">Pendaftaran</a>
            </div>
        </nav>

        {{-- 3. Profil yayasan --}}
        <section
            class="about"
            id="tentang-yayasan"
            aria-labelledby="about-title"
        >
            <div class="wrap about-grid">
                <div class="identity">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="Logo YPTQ Asy-Syams"
                        width="164"
                        height="164"
                    >

                    <h3>YPTQ Asy-Syams</h3>

                    <p>Yayasan Pendidikan Tilawatil Qur’an</p>

                    <span class="place">PEKANBARU, RIAU</span>
                </div>

                <div>
                    <p class="eyebrow">Sejarah &amp; Dedikasi</p>

                    <h2 id="about-title">
                        Mendidik generasi Qur’ani, berakhlak mulia,
                        dan berwawasan global.
                    </h2>

                    <div class="story">
                        <p>
                            Yayasan Pendidikan Tilawatil Qur’an (YPTQ)
                            Asy-Syams didirikan sebagai wujud dedikasi
                            dalam melahirkan generasi yang cinta Al-Qur’an.
                            Kami percaya bahwa pendidikan terbaik adalah
                            perpaduan harmonis antara ilmu pengetahuan
                            modern, akhlak mulia, dan kedekatan spiritual
                            dengan kitab suci.
                        </p>

                        <p>
                            Berlokasi di jantung kota Pekanbaru, kami
                            menyediakan lingkungan belajar yang Islami
                            dan kondusif, dibimbing oleh tenaga pengajar
                            profesional dan bersanad, serta didukung
                            kurikulum komprehensif yang mengintegrasikan
                            nilai-nilai keislaman dengan tuntutan
                            kompetensi abad ke-21.
                        </p>
                    </div>

                    <div class="features">
                        <div>
                            <span class="feature-mark" aria-hidden="true">
                                01
                            </span>

                            <h3>Pengajar Ahli &amp; Bersanad</h3>

                            <p>
                                Dibimbing langsung oleh para asatidz
                                yang berkompeten di bidang tahsin
                                dan tahfizh.
                            </p>
                        </div>

                        <div>
                            <span class="feature-mark" aria-hidden="true">
                                02
                            </span>

                            <h3>Fasilitas Nyaman</h3>

                            <p>
                                Lingkungan belajar yang bersih,
                                kondusif, dan mendukung fokus
                                hafalan santri.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 4. Visi dan misi --}}
        <section
            class="direction"
            id="visi-misi"
            aria-labelledby="direction-title"
        >
            <div class="wrap">
                <p class="eyebrow">Landasan Gerak</p>

                <h2 id="direction-title">Visi &amp; Misi Yayasan</h2>

                <div class="direction-grid">
                    <div class="vision">
                        <h3 class="eyebrow">Visi Kami</h3>

                        <p>
                            Menjadi lembaga pendidikan Islam unggulan
                            yang melahirkan generasi penghafal dan
                            pengamal Al-Qur’an, berakhlak mulia,
                            serta memiliki kompetensi akademik dan
                            non-akademik yang berdaya saing global.
                        </p>
                    </div>

                    <div class="mission">
                        <h3>Misi Kami</h3>

                        <ol>
                            <li>
                                <span class="number" aria-hidden="true">
                                    01
                                </span>

                                <div>
                                    <h4>
                                        Pembelajaran Al-Qur’an yang terarah
                                    </h4>

                                    <p>
                                        Menyelenggarakan pembelajaran
                                        Al-Qur’an yang intensif,
                                        sistematis, dan menyenangkan.
                                    </p>
                                </div>
                            </li>

                            <li>
                                <span class="number" aria-hidden="true">
                                    02
                                </span>

                                <div>
                                    <h4>
                                        Pembentukan akhlak dan kemandirian
                                    </h4>

                                    <p>
                                        Membentuk karakter santri yang
                                        disiplin, tangguh, mandiri,
                                        dan berakhlaqul karimah.
                                    </p>
                                </div>
                            </li>

                            <li>
                                <span class="number" aria-hidden="true">
                                    03
                                </span>

                                <div>
                                    <h4>
                                        Pengembangan bakat dan kepemimpinan
                                    </h4>

                                    <p>
                                        Mengembangkan potensi bakat,
                                        minat, dan jiwa kepemimpinan
                                        santri secara optimal.
                                    </p>
                                </div>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        {{-- 5. Informasi pendaftaran --}}
        <section
            class="enroll"
            id="pendaftaran"
            aria-labelledby="enroll-title"
        >
            <div class="wrap">
                <div class="cta">
                    <div>
                        <h2 id="enroll-title">
                            Tumbuh bersama Asy-Syams.
                        </h2>

                        <p>
                            Mulailah perjalanan pendidikan Al-Qur’an
                            putra-putri Anda bersama asatidz berpengalaman.
                            Pelajari informasi pendaftaran melalui SPMB.
                        </p>
                    </div>

                    <div class="actions">
                        <a
                            class="button"
                            href="{{ route('spmb.index') }}"
                        >
                            Daftar SPMB
                            <span aria-hidden="true">↗</span>
                        </a>

                        <a
                            class="button secondary"
                            href="{{ url('/#contact') }}"
                        >
                            Hubungi Admin
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-app-layout>