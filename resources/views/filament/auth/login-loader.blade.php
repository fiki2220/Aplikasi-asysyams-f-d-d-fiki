{{-- ============================================================ --}}
{{-- PRELOADER — Loader kustom YPTQ Asy-Syams                     --}}
{{-- HTML + JS — CSS ada di theme.css                              --}}
{{-- ============================================================ --}}

{{-- Block scroll saat loading --}}
<script>
    document.documentElement.classList.add('is-loading');
</script>

{{-- Elemen Loader --}}
<div id="filament-preloader" aria-hidden="true">
    <div class="filament-loader"></div>
</div>

{{-- Script hide loader --}}
<script>
    (function () {
        const preloader = document.getElementById('filament-preloader');
        if (!preloader) return;

        const MIN_DISPLAY = 400;
        const MAX_WAIT    = 3500;
        const startTime   = Date.now();

        let hasHidden = false;

        function hideLoader() {
            if (hasHidden) return;
            hasHidden = true;

            const elapsed   = Date.now() - startTime;
            const remaining = Math.max(0, MIN_DISPLAY - elapsed);

            setTimeout(function () {
                preloader.classList.add('loader-hidden');
                document.documentElement.classList.remove('is-loading');

                setTimeout(function () {
                    preloader.remove();
                }, 600);
            }, remaining);
        }

        if (document.readyState === 'complete') {
            hideLoader();
        } else {
            window.addEventListener('load', hideLoader, { once: true });
            setTimeout(hideLoader, MAX_WAIT);
        }
    })();
</script>