<div class="yptq-login-wrap">

    <div class="yptq-login-card">

        {{-- ============================================================ --}}
        {{-- LEFT PANEL — Hijau dengan pattern                               --}}
        {{-- ============================================================ --}}
        <div class="yptq-left">

            {{-- Pattern SVG (Arabesque halus) --}}
            <svg class="yptq-pattern" viewBox="0 0 400 600" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="arabesque" x="0" y="0" width="100" height="100" patternUnits="userSpaceOnUse">
                        <path d="M50 0 L100 50 L50 100 L0 50 Z" fill="none" stroke="rgba(212,175,55,0.15)" stroke-width="0.5"/>
                        <circle cx="50" cy="50" r="15" fill="none" stroke="rgba(212,175,55,0.10)" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#arabesque)"/>
            </svg>

            {{-- Ornamen buku terbuka --}}
            <svg class="yptq-book" viewBox="0 0 400 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M200 180 C200 180, 180 140, 100 120 L100 20 C180 40, 200 80, 200 80 C200 80, 220 40, 300 20 L300 120 C220 140, 200 180, 200 180 Z"
                      stroke="rgba(212,175,55,0.4)" stroke-width="1.2" fill="none"/>
                <path d="M200 80 L200 180" stroke="rgba(212,175,55,0.4)" stroke-width="1"/>
                <path d="M120 50 L180 65 M120 70 L180 85 M120 90 L180 105"
                      stroke="rgba(212,175,55,0.25)" stroke-width="0.8"/>
                <path d="M280 50 L220 65 M280 70 L220 85 M280 90 L220 105"
                      stroke="rgba(212,175,55,0.25)" stroke-width="0.8"/>
            </svg>

            <div class="yptq-left-content">
                <div class="yptq-brand">
                    <img src="{{ asset('images/logo.PNG') }}" alt="Logo YPTQ Asy-Syams" class="yptq-brand-logo">
                    <div>
                        <div class="yptq-brand-name">YPTQ ASY-SYAMS</div>
                        <div class="yptq-brand-tag">Tilawah Center Pekanbaru</div>
                    </div>
                </div>

                <div class="yptq-tagline">Bersama Menumbuhkan Kebaikan</div>

                <h2 class="yptq-hero-title">
                    Mengajar dengan <em>hati.</em><br>
                    Melayani dengan <em>amanah.</em>
                </h2>

                <p class="yptq-hero-desc">
                    Setiap ilmu yang dibagikan dan setiap tugas yang dituntaskan berarti bagi masa depan santri.
                </p>

                <div class="yptq-footer-text">
                    Terima kasih telah menjadi bagian dari perjalanan mereka.
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT PANEL — Form login                                        --}}
        {{-- ============================================================ --}}
        <div class="yptq-right">

            <a href="{{ url('/') }}" class="yptq-back-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="14" height="14">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Kembali ke Beranda</span>
            </a>

            <div class="yptq-portal-label">Portal Guru & Admin</div>

            <h1 class="yptq-welcome">Selamat datang kembali</h1>
            <p class="yptq-subtitle">Semangat baru untuk pengabdian terbaik.</p>

            <form wire:submit="authenticate" class="yptq-form">
                {{ $this->form }}

                <button type="submit" class="yptq-submit">
                    <span>Masuk ke Portal</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </form>

            <div class="yptq-footer">
                <span class="yptq-footer-line"></span>
                <span class="yptq-footer-brand">YPTQ Asy-Syams</span>
                <span class="yptq-footer-line"></span>
            </div>

        </div>

    </div>

</div>

{{-- ============================================================ --}}
{{-- CUSTOM PASSWORD TOGGLE — 1 icon mata saja                     --}}
{{-- ============================================================ --}}
<script>
    (function () {
        function initPasswordToggle() {
            const passwordInput = document.querySelector('.yptq-input-password');

            if (!passwordInput || passwordInput.dataset.toggleInit === '1') return;
            passwordInput.dataset.toggleInit = '1';

            const wrap = passwordInput.closest('.fi-input-wrp');
            if (!wrap) return;

            // Buat tombol custom
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'yptq-eye-toggle';
            btn.setAttribute('aria-label', 'Tampilkan kata sandi');
            btn.setAttribute('tabindex', '-1');
            btn.innerHTML = `
                <svg class="yptq-eye yptq-eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/>
                    <circle cx="12" cy="12" r="3"/>
                </svg>
                <svg class="yptq-eye yptq-eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                    <path d="M9.88 9.88a3 3 0 104.24 4.24"/>
                    <path d="M10.73 5.08A10.43 10.43 0 0112 5c7 0 10 7 10 7a13.16 13.16 0 01-1.67 2.68"/>
                    <path d="M6.61 6.61A13.526 13.526 0 002 12s3 7 10 7a9.74 9.74 0 005.39-1.61"/>
                    <line x1="2" y1="2" x2="22" y2="22"/>
                </svg>
            `;

            wrap.appendChild(btn);

            const eyeOpen = btn.querySelector('.yptq-eye-open');
            const eyeClosed = btn.querySelector('.yptq-eye-closed');

            function updateVisibility() {
                const hasValue = passwordInput.value.length > 0;
                btn.classList.toggle('is-visible', hasValue);

                // Kalau kosong & sebelumnya visible → reset ke hidden
                if (!hasValue && passwordInput.type === 'text') {
                    passwordInput.type = 'password';
                    eyeOpen.style.display = 'block';
                    eyeClosed.style.display = 'none';
                }
            }

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const isCurrentlyText = passwordInput.type === 'text';
                passwordInput.type = isCurrentlyText ? 'password' : 'text';
                eyeOpen.style.display = isCurrentlyText ? 'block' : 'none';
                eyeClosed.style.display = isCurrentlyText ? 'none' : 'block';
            });

            passwordInput.addEventListener('input', updateVisibility);
            passwordInput.addEventListener('change', updateVisibility);
            updateVisibility();
        }

        document.addEventListener('DOMContentLoaded', initPasswordToggle);
        document.addEventListener('livewire:navigated', initPasswordToggle);

        if (window.Livewire && typeof Livewire.hook === 'function') {
            Livewire.hook('morph.updated', function () {
                setTimeout(initPasswordToggle, 50);
            });
        }

        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(initPasswordToggle, 100);
        }
    })();
</script>