@extends('root.app')

@section('title', 'Profil Saya - YPTQ Asy-Syams')

@section('content')

@php
    $photoPath = $user->file_foto ?? null;
    $photoUrl  = ($photoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath))
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($photoPath)
        : null;

    $initial = strtoupper(substr($user->name ?? 'S', 0, 1));

    $docList = [
        ['field' => 'file_kk',     'label' => 'Kartu Keluarga', 'accept' => 'image/*,application/pdf',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['field' => 'file_akta',   'label' => 'Akta Kelahiran', 'accept' => 'image/*,application/pdf',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    ];
@endphp

<div class="bg-gray-50 min-h-screen">

    {{-- HEADER --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs font-bold mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        AKUN SANTRI
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">
                        Profil Saya
                    </h1>
                    <p class="text-gray-500 text-sm mt-1.5">
                        Kelola data pribadi, dokumen, dan keamanan akun Anda.
                    </p>
                </div>

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-sm font-semibold shadow-sm hover:bg-gray-50 hover:border-gray-300 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 p-4 rounded-xl border border-emerald-200 bg-emerald-50">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-bold text-emerald-800 text-sm">Berhasil</p>
                    <p class="text-emerald-700 text-sm mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 flex items-start gap-3 p-4 rounded-xl border border-red-200 bg-red-50">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-bold text-red-800 text-sm">Gagal</p>
                    <p class="text-red-700 text-sm mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl border border-red-200 bg-red-50">
                <div class="font-bold text-red-800 text-sm mb-2">Ada data yang perlu diperbaiki:</div>
                <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
              class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            @csrf
            @method('PUT')

            {{-- SIDEBAR --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden sticky top-24">

                    <div class="p-6 text-center border-b border-gray-100">
                        <label for="file_foto" class="relative inline-block cursor-pointer group">
                            <div class="w-32 h-32 rounded-full overflow-hidden bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center ring-4 ring-white shadow-md mx-auto">
                                @if($photoUrl)
                                    <img id="photo-preview" src="{{ $photoUrl }}" alt="Foto Profil"
                                         decoding="async"
                                         class="w-full h-full object-cover">
                                    <span id="photo-initial" class="hidden text-4xl font-bold text-emerald-700">{{ $initial }}</span>
                                @else
                                    <img id="photo-preview" src="" alt="Foto Profil" class="hidden w-full h-full object-cover">
                                    <span id="photo-initial" class="text-4xl font-bold text-emerald-700">{{ $initial }}</span>
                                @endif
                            </div>

                            <div class="absolute inset-0 rounded-full bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold">
                                Ganti Foto
                            </div>

                            <div class="absolute bottom-0 right-0 w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center shadow-lg ring-4 ring-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h4l2-3h6l2 3h4v13H3V7z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17a4 4 0 100-8 4 4 0 000 8z"/>
                                </svg>
                            </div>
                        </label>

                        <input id="file_foto" type="file" name="file_foto"
                               accept="image/png,image/jpeg,image/jpg,image/webp" class="hidden">

                        {{-- ✅ Teks diubah menjadi 2 MB --}}
                        <p class="mt-4 text-[11px] text-gray-400">
                            JPG, PNG, WEBP · Maks 2 MB
                        </p>
                    </div>

                    <div class="p-6">
                        <h2 class="text-lg font-bold text-gray-900 text-center">{{ $user->name ?? '-' }}</h2>

                        <p class="mt-1.5 text-center">
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold uppercase tracking-wider">
                                {{ ucfirst($user->role ?? 'santri') }}
                            </span>
                        </p>

                        <dl class="mt-6 space-y-4 text-sm">
                            <div>
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Email</dt>
                                <dd class="font-semibold text-gray-800 break-words">{{ $user->email ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">NISN</dt>
                                <dd class="font-semibold text-gray-800">{{ $user->nisn ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Nomor HP</dt>
                                <dd class="font-semibold text-gray-800">{{ $user->phone ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- FORM CONTENT --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- 1. DATA PRIBADI --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Data Pribadi</h3>
                            <p class="text-xs text-gray-500">Informasi dasar tentang Anda</p>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full rounded-lg border @error('email') border-red-400 @else border-gray-300 @enderror bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                NISN
                            </label>
                            <input type="text" value="{{ $user->nisn ?? '—' }}" readonly disabled
                                   class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Nomor HP / WhatsApp
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                   placeholder="0812xxxx"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Jenis Kelamin
                            </label>
                            {{-- ✅ Nilai value diperbaiki menjadi L & P agar sesuai validasi Controller --}}
                            <select name="gender"
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                                <option value="">— Pilih —</option>
                                <option value="L" @selected(old('gender', $user->gender ?? '') === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('gender', $user->gender ?? '') === 'P')>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Tempat Lahir
                            </label>
                            <input type="text" name="birth_place" value="{{ old('birth_place', $user->birth_place ?? '') }}"
                                   placeholder="Contoh: Pekanbaru"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Tanggal Lahir
                            </label>
                            <input type="date" name="birth_date"
                                   value="{{ old('birth_date', !empty($user->birth_date) ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '') }}"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Alamat Lengkap
                            </label>
                            <textarea name="address" rows="3" placeholder="Alamat lengkap tempat tinggal"
                                      class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">{{ old('address', $user->address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- 2. DATA ORANG TUA --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-4a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Data Orang Tua / Wali</h3>
                            <p class="text-xs text-gray-500">Informasi kontak orang tua atau wali</p>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Nama Ayah
                            </label>
                            <input type="text" name="father_name"
                                   value="{{ old('father_name', $user->father_name ?? $user->parent_name ?? '') }}"
                                   placeholder="Nama lengkap ayah"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Nama Ibu
                            </label>
                            <input type="text" name="mother_name"
                                   value="{{ old('mother_name', $user->mother_name ?? '') }}"
                                   placeholder="Nama lengkap ibu"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Nomor HP Orang Tua / Wali
                            </label>
                            <input type="text" name="parent_phone"
                                   value="{{ old('parent_phone', $user->parent_phone ?? '') }}"
                                   placeholder="0812xxxx"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>
                    </div>
                </div>

                {{-- 3. DOKUMEN --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Dokumen Pendukung</h3>
                            <p class="text-xs text-gray-500">Maks 2 MB per file · Format: JPG, PNG, PDF</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        @foreach($docList as $doc)
                            @php
                                $currentFile = $user->{$doc['field']} ?? null;
                                $currentUrl  = ($currentFile && \Illuminate\Support\Facades\Storage::disk('public')->exists($currentFile))
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($currentFile)
                                    : null;
                                $isImage = $currentFile && ! str_ends_with(strtolower($currentFile), '.pdf');
                            @endphp

                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-4 rounded-xl border border-gray-200 hover:border-emerald-300 transition-colors bg-gray-50/50">

                                <div class="flex-shrink-0">
                                    @if($currentUrl && $isImage)
                                        <img src="{{ $currentUrl }}" alt="{{ $doc['label'] }}"
                                             loading="lazy"
                                             decoding="async"
                                             class="w-16 h-16 rounded-lg object-cover ring-2 ring-white shadow-sm">
                                    @elseif($currentUrl && !$isImage)
                                        <div class="w-16 h-16 rounded-lg bg-red-100 flex items-center justify-center">
                                            <span class="text-xs font-bold text-red-600">PDF</span>
                                        </div>
                                    @else
                                        <div class="w-16 h-16 rounded-lg bg-white border border-gray-200 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $doc['icon'] }}"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-2">
                                        <span class="font-bold text-gray-800 text-sm">{{ $doc['label'] }}</span>
                                        @if($currentUrl)
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">✓ ADA</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 text-[10px] font-bold">BELUM</span>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3">
                                        @if($currentUrl)
                                            <a href="{{ $currentUrl }}" target="_blank"
                                               class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-800">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Lihat file
                                            </a>
                                        @endif

                                        <label class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-900 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                            </svg>
                                            {{ $currentUrl ? 'Ganti' : 'Upload' }}
                                            <input type="file" name="{{ $doc['field'] }}" accept="{{ $doc['accept'] }}" class="hidden">
                                        </label>
                                    </div>

                                    @error($doc['field'])
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 4. KEAMANAN AKUN --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Keamanan Akun</h3>
                            <p class="text-xs text-gray-500">Kosongkan jika tidak ingin ganti password</p>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 gap-5">

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                Password Saat Ini
                            </label>
                            <div class="relative">
                                <input type="password" id="current_password" name="current_password"
                                       autocomplete="current-password"
                                       placeholder="Masukkan password saat ini"
                                       class="w-full rounded-lg border @error('current_password') border-red-400 @else border-gray-300 @enderror bg-white px-3.5 py-2.5 pr-10 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                                <button type="button" onclick="togglePassword('current_password')"
                                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                    Password Baru
                                </label>
                                <input type="password" name="password" autocomplete="new-password"
                                       placeholder="Min. 8 karakter"
                                       class="w-full rounded-lg border @error('password') border-red-400 @else border-gray-300 @enderror bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wider">
                                    Konfirmasi Password
                                </label>
                                <input type="password" name="password_confirmation" autocomplete="new-password"
                                       placeholder="Ulangi password baru"
                                       class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACTION BAR --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 flex flex-col-reverse sm:flex-row gap-3 justify-between items-stretch sm:items-center sticky bottom-4 z-10">
                    <button type="button"
                            onclick="document.getElementById('logout-form-profile').submit()"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-red-200 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-sm hover:shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>

            </div>
        </form>

        {{-- MODAL CROP --}}
        <div id="crop-modal" class="fixed inset-0 z-[9999] hidden">
            <div class="absolute inset-0 bg-black/70" onclick="closeCropModal(true)"></div>
            <div class="relative min-h-screen flex items-center justify-center px-4 py-8">
                <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900">Rapikan Foto Profil</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Geser & zoom agar wajah pas di lingkaran</p>
                        </div>
                        <button type="button" onclick="closeCropModal(true)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-center">
                            <div id="crop-area" class="relative w-[280px] h-[280px] bg-gray-900 overflow-hidden rounded-xl select-none" style="touch-action: none;">
                                <img id="crop-image" src="" alt="Crop" draggable="false" class="absolute left-1/2 top-1/2 max-w-none select-none">
                                <div class="pointer-events-none absolute inset-0 border-2 border-white/60"></div>
                                <div class="pointer-events-none absolute inset-6 rounded-full border-2 border-white"></div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Zoom</label>
                            <input id="crop-zoom" type="range" min="1" max="3" step="0.01" value="1" class="w-full accent-emerald-600">
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" id="cancel-crop-button"
                                class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="button" id="apply-crop-button"
                                class="px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">
                            Gunakan Foto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form id="logout-form-profile" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>

    </div>
</div>

<script>
    const photoInput   = document.getElementById('file_foto');
    const photoPreview = document.getElementById('photo-preview');
    const photoInitial = document.getElementById('photo-initial');

    const cropModal    = document.getElementById('crop-modal');
    const cropArea     = document.getElementById('crop-area');
    const cropImage    = document.getElementById('crop-image');
    const cropZoom     = document.getElementById('crop-zoom');
    const cancelCropButton = document.getElementById('cancel-crop-button');
    const applyCropButton  = document.getElementById('apply-crop-button');

    let cropScale = 1, cropPosX = 0, cropPosY = 0, cropBaseWidth = 0, cropBaseHeight = 0;
    let isDragging = false, dragStartX = 0, dragStartY = 0, dragStartPosX = 0, dragStartPosY = 0;

    function validatePhotoFile(file) {
        const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!allowed.includes(file.type)) { alert('Format foto harus JPG, PNG, atau WEBP.'); return false; }
        
        // ✅ UKURAN MAKSIMAL DIUBAH MENJADI 2 MB
        if (file.size > 2 * 1024 * 1024) { alert('Ukuran foto maksimal 2 MB.'); return false; }
        return true;
    }

    function openCropModal(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            cropModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            cropImage.onload = () => setupCropImage();
            cropImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function closeCropModal(resetInput = false) {
        cropModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        if (resetInput && photoInput) photoInput.value = '';
        cropImage.src = '';
        cropScale = 1; cropPosX = 0; cropPosY = 0;
        if (cropZoom) cropZoom.value = 1;
    }

    function setupCropImage() {
        const cw = cropArea.clientWidth, ch = cropArea.clientHeight;
        const ir = cropImage.naturalWidth / cropImage.naturalHeight;
        const cr = cw / ch;
        if (ir > cr) { cropBaseHeight = ch; cropBaseWidth = ch * ir; }
        else { cropBaseWidth = cw; cropBaseHeight = cw / ir; }
        cropScale = 1; cropPosX = 0; cropPosY = 0;
        cropImage.style.width = cropBaseWidth + 'px';
        cropImage.style.height = cropBaseHeight + 'px';
        updateCropTransform();
    }

    function updateCropTransform() {
        cropImage.style.transform = `translate(-50%, -50%) translate(${cropPosX}px, ${cropPosY}px) scale(${cropScale})`;
    }

    function constrainCropPosition() {
        const cw = cropArea.clientWidth, ch = cropArea.clientHeight;
        const sw = cropBaseWidth * cropScale, sh = cropBaseHeight * cropScale;
        const maxX = Math.max(0, (sw - cw) / 2), maxY = Math.max(0, (sh - ch) / 2);
        cropPosX = Math.min(maxX, Math.max(-maxX, cropPosX));
        cropPosY = Math.min(maxY, Math.max(-maxY, cropPosY));
    }

    if (photoInput) {
        photoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            if (!validatePhotoFile(file)) { photoInput.value = ''; return; }
            openCropModal(file);
        });
    }

    if (cropZoom) cropZoom.addEventListener('input', function () {
        cropScale = parseFloat(this.value);
        constrainCropPosition();
        updateCropTransform();
    });

    if (cropArea) {
        cropArea.addEventListener('pointerdown', (e) => {
            isDragging = true;
            dragStartX = e.clientX; dragStartY = e.clientY;
            dragStartPosX = cropPosX; dragStartPosY = cropPosY;
            cropArea.setPointerCapture(e.pointerId);
        });
        cropArea.addEventListener('pointermove', (e) => {
            if (!isDragging) return;
            cropPosX = dragStartPosX + (e.clientX - dragStartX);
            cropPosY = dragStartPosY + (e.clientY - dragStartY);
            constrainCropPosition();
            updateCropTransform();
        });
        cropArea.addEventListener('pointerup', () => isDragging = false);
        cropArea.addEventListener('pointercancel', () => isDragging = false);
    }

    if (cancelCropButton) cancelCropButton.addEventListener('click', () => closeCropModal(true));

    if (applyCropButton) applyCropButton.addEventListener('click', () => {
        const cw = cropArea.clientWidth, ch = cropArea.clientHeight;
        const sw = cropBaseWidth * cropScale, sh = cropBaseHeight * cropScale;
        const imageLeft = cw / 2 + cropPosX - sw / 2;
        const imageTop  = ch / 2 + cropPosY - sh / 2;
        const sourceX = Math.max(0, (0 - imageLeft) / sw * cropImage.naturalWidth);
        const sourceY = Math.max(0, (0 - imageTop) / sh * cropImage.naturalHeight);
        const sourceWidth  = Math.min(cropImage.naturalWidth - sourceX, cw / sw * cropImage.naturalWidth);
        const sourceHeight = Math.min(cropImage.naturalHeight - sourceY, ch / sh * cropImage.naturalHeight);

        const canvas = document.createElement('canvas');
        const size = 600;
        canvas.width = size; canvas.height = size;
        canvas.getContext('2d').drawImage(cropImage, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, size, size);

        canvas.toBlob((blob) => {
            if (!blob) { alert('Gagal memproses foto.'); return; }
            const file = new File([blob], 'profile-photo.jpg', { type: 'image/jpeg', lastModified: Date.now() });
            const dt = new DataTransfer();
            dt.items.add(file);
            photoInput.files = dt.files;

            photoPreview.src = URL.createObjectURL(blob);
            photoPreview.classList.remove('hidden');
            if (photoInitial) photoInitial.classList.add('hidden');

            closeCropModal(false);
        }, 'image/jpeg', 0.9);
    });

    function togglePassword(id) {
        const input = document.getElementById(id);
        if (input) input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>

@endsection