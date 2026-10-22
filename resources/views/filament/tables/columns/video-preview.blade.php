{{-- resources/views/filament/tables/columns/video-preview.blade.php --}}
@php
    $record = $getRecord();
    $value  = $record?->value;

    // Normalisasi → single path
    $videoPath = null;

    if (is_array($value)) {
        $videoPath = ! empty($value) ? reset($value) : null;
    } elseif (is_string($value) && $value !== '') {
        $decoded = json_decode($value, true);
        $videoPath = is_array($decoded)
            ? (! empty($decoded) ? reset($decoded) : null)
            : $value;
    }

    // Normalisasi path (buang backslash, prefix /storage/)
    if ($videoPath) {
        $videoPath = str_replace('\\', '/', (string) $videoPath);
        $videoPath = preg_replace('#^/?storage/#', '', $videoPath);
        $videoPath = ltrim($videoPath, '/');
    }

    $videoUrl = $videoPath
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($videoPath)
        : null;

    $fileExists = $videoPath
        ? \Illuminate\Support\Facades\Storage::disk('public')->exists($videoPath)
        : false;
@endphp

@if($videoUrl && $fileExists)
    <div class="flex items-center gap-3">
        {{-- ⭐ Kotak video icon + tombol play (bukan thumbnail video) --}}
        <button
            type="button"
            onclick="yptqOpenVideo('{{ $videoUrl }}')"
            class="relative group w-16 h-12 rounded-lg flex-shrink-0
                   flex items-center justify-center
                   bg-gradient-to-br from-emerald-500 to-emerald-700
                   border-2 border-emerald-600 hover:border-emerald-400
                   shadow-md hover:shadow-lg
                   transition-all duration-200 hover:scale-105"
            aria-label="Putar video: {{ basename($videoPath) }}"
        >
            {{-- Play icon (segitiga) --}}
            <svg class="w-6 h-6 text-white drop-shadow-lg ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
            </svg>
        </button>

        {{-- Info file --}}
        <div class="min-w-0 text-xs">
            <div class="font-semibold text-gray-700 truncate max-w-[160px]"
                 title="{{ basename($videoPath) }}">
                {{ basename($videoPath) }}
            </div>
            <a href="{{ $videoUrl }}"
               target="_blank"
               rel="noopener"
               class="text-emerald-600 hover:text-emerald-800 hover:underline">
                Buka di tab baru →
            </a>
        </div>
    </div>

    @once
        <script>
            window.yptqOpenVideo = function (url) {
                let modal = document.getElementById('yptqVideoModal');
                if (!modal) {
                    modal = document.createElement('div');
                    modal.id = 'yptqVideoModal';
                    modal.style.cssText = `
                        position: fixed; inset: 0; z-index: 99999;
                        display: none; align-items: center; justify-content: center;
                        background: rgba(0,0,0,0.85); padding: 1rem;
                        backdrop-filter: blur(4px);
                    `;
                    modal.innerHTML = `
                        <div style="position: relative; width: 100%; max-width: 56rem;">
                            <button type="button" onclick="yptqCloseVideo()"
                                style="position: absolute; top: -3rem; right: 0;
                                       width: 2.5rem; height: 2.5rem; border-radius: 9999px;
                                       background: rgba(255,255,255,0.15); color: white;
                                       display: flex; align-items: center; justify-content: center;
                                       border: none; cursor: pointer; font-size: 1.25rem;"
                                aria-label="Tutup video">✕</button>
                            <video id="yptqVideoPlayer" controls
                                style="width: 100%; border-radius: 0.75rem; background: #000; max-height: 80vh;">
                                Browser Anda tidak mendukung tag video.
                            </video>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    modal.addEventListener('click', function (e) {
                        if (e.target === modal) yptqCloseVideo();
                    });
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') yptqCloseVideo();
                    });
                }

                const player = document.getElementById('yptqVideoPlayer');
                player.src = url;
                player.play();
                modal.style.display = 'flex';
            };

            window.yptqCloseVideo = function () {
                const modal  = document.getElementById('yptqVideoModal');
                const player = document.getElementById('yptqVideoPlayer');
                if (player) { player.pause(); player.src = ''; }
                if (modal) modal.style.display = 'none';
            };
        </script>
    @endonce
@else
    {{-- Fallback: file belum ada atau path kosong --}}
    <div class="flex items-center gap-2 text-xs text-gray-400">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <span>Video belum ada</span>
    </div>
@endif