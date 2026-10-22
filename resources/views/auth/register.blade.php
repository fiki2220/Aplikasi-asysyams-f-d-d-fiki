<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran SPMB - YPTQ Asy-Syams</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 bg-gradient-to-br from-gray-100 via-green-50 to-gray-100 min-h-screen py-6 sm:py-10 px-4 sm:px-6">

    <x-loader />

    <div class="w-full max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">

        {{-- HEADER --}}
        <div class="bg-green-800 px-6 py-10 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-40 h-40 bg-green-700 rounded-full opacity-50 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-green-900 rounded-full opacity-50 blur-2xl"></div>

            <div class="relative z-10">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl bg-white mb-5 shadow-lg border border-green-700/50 p-1.5 transform hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo YPTQ Asy-Syams" class="w-full h-full object-cover rounded-xl">
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Formulir SPMB Online
                </h1>

                <p class="mt-3 text-green-100 text-sm sm:text-base font-medium max-w-xl mx-auto">
                    Selamat datang di portal pendaftaran Yayasan Pendidikan Tilawatil Qur'an Asy-Syams.
                    Silakan lengkapi data di bawah ini dengan benar.
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-10">

            {{-- ERROR SUMMARY --}}
            @if ($errors->any())
            <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-5">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold text-red-900">
                            Ada {{ $errors->count() }} kesalahan pada formulir
                        </h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}"
                enctype="multipart/form-data"
                x-data="{
                      submitting: false,
                      today: new Date().toISOString().split('T')[0],
                      files: { file_kk: '', file_akta: '' }
                  }"
                @submit="submitting = true">
                @csrf

                {{-- Honeypot anti-bot --}}
                <div style="position:absolute; left:-9999px;" aria-hidden="true">
                    <label for="website">Website (jangan diisi)</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                {{-- ==================================================== --}}
                {{-- SECTION 1 — DATA DIRI SANTRI                          --}}
                {{-- ==================================================== --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 border-b-2 border-green-100 pb-2 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Data Diri Santri
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                        {{-- Nama Lengkap --}}
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input id="name" type="text" name="name"
                                value="{{ old('name') }}"
                                required autofocus
                                autocomplete="name"
                                placeholder="Masukkan nama lengkap santri"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('name') border-red-500 @enderror">
                            @error('name')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- NISN --}}
                        <div>
                            <label for="nisn" class="block text-sm font-semibold text-gray-700 mb-1">
                                NISN <span class="text-red-500">*</span>
                            </label>
                            <input id="nisn" type="text" name="nisn"
                                value="{{ old('nisn') }}"
                                required inputmode="numeric"
                                minlength="10" maxlength="20"
                                pattern="[0-9]+"
                                placeholder="Contoh: 1000000001"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('nisn') border-red-500 @enderror">
                            @error('nisn')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Gender --}}
                        <div>
                            <label for="gender" class="block text-sm font-semibold text-gray-700 mb-1">
                                Jenis Kelamin <span class="text-red-500">*</span>
                            </label>
                            <select id="gender" name="gender" required
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('gender') border-red-500 @enderror">
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih jenis kelamin</option>
                                <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tempat Lahir --}}
                        <div>
                            <label for="birth_place" class="block text-sm font-semibold text-gray-700 mb-1">
                                Tempat Lahir <span class="text-red-500">*</span>
                            </label>
                            <input id="birth_place" type="text" name="birth_place"
                                value="{{ old('birth_place') }}"
                                required
                                placeholder="Contoh: Pekanbaru"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('birth_place') border-red-500 @enderror">
                            @error('birth_place')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir — max dibatasi hari ini --}}
                        <div>
                            <label for="birth_date" class="block text-sm font-semibold text-gray-700 mb-1">
                                Tanggal Lahir <span class="text-red-500">*</span>
                            </label>
                            <input id="birth_date" type="date" name="birth_date"
                                value="{{ old('birth_date') }}"
                                required
                                :max="today"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('birth_date') border-red-500 @enderror">
                            @error('birth_date')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- No HP Santri --}}
                        <div class="md:col-span-2">
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">
                                No. HP / WA Santri
                                <span class="text-gray-400 text-xs font-normal">(opsional)</span>
                            </label>
                            <input id="phone" type="text" name="phone"
                                value="{{ old('phone') }}"
                                inputmode="tel"
                                autocomplete="tel"
                                placeholder="Contoh: 081234567890"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('phone') border-red-500 @enderror">
                            @error('phone')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ==================================================== --}}
                {{-- SECTION 2 — DATA ORANG TUA / WALI                     --}}
                {{-- ==================================================== --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 border-b-2 border-green-100 pb-2 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m6-4a4 4 0 10-8 0 4 4 0 008 0zm6 0a3 3 0 10-6 0 3 3 0 006 0z" />
                        </svg>
                        Data Orang Tua / Wali
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                        <div>
                            <label for="father_name" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Ayah <span class="text-red-500">*</span>
                            </label>
                            <input id="father_name" type="text" name="father_name"
                                value="{{ old('father_name') }}"
                                required
                                placeholder="Masukkan nama ayah"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('father_name') border-red-500 @enderror">
                            @error('father_name')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="mother_name" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Ibu <span class="text-red-500">*</span>
                            </label>
                            <input id="mother_name" type="text" name="mother_name"
                                value="{{ old('mother_name') }}"
                                required
                                placeholder="Masukkan nama ibu"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('mother_name') border-red-500 @enderror">
                            @error('mother_name')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="parent_name" class="block text-sm font-semibold text-gray-700 mb-1">
                                Nama Wali
                                <span class="text-gray-400 text-xs font-normal">(opsional)</span>
                            </label>
                            <input id="parent_name" type="text" name="parent_name"
                                value="{{ old('parent_name') }}"
                                placeholder="Isi jika wali berbeda dari ayah / ibu"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('parent_name') border-red-500 @enderror">
                            @error('parent_name')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- No HP Orang Tua — dengan validasi client-side --}}
                        <div>
                            <label for="parent_phone" class="block text-sm font-semibold text-gray-700 mb-1">
                                No. HP Orang Tua / Wali <span class="text-red-500">*</span>
                            </label>
                            <input id="parent_phone" type="text" name="parent_phone"
                                value="{{ old('parent_phone') }}"
                                required inputmode="tel"
                                autocomplete="tel"
                                minlength="10" maxlength="15"
                                pattern="[0-9+\-\s]+"
                                title="Masukkan nomor HP yang valid (10-15 digit)"
                                placeholder="Contoh: 081234567890"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('parent_phone') border-red-500 @enderror">
                            @error('parent_phone')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ==================================================== --}}
                {{-- SECTION 3 — AKADEMIK & DOMISILI                       --}}
                {{-- ==================================================== --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 border-b-2 border-green-100 pb-2 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Akademik & Domisili
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                        {{-- Program --}}
                        <div>
                            <label for="program" class="block text-sm font-semibold text-gray-700 mb-1">
                                Daftar Untuk Program <span class="text-red-500">*</span>
                            </label>
                            <select id="program" name="program" required
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('program') border-red-500 @enderror">
                                <option value="" disabled {{ old('program') ? '' : 'selected' }}>Pilih program</option>
                                <option value="Tahsin" {{ old('program') === 'Tahsin' ? 'selected' : '' }}>Tahsin</option>
                                <option value="Tilawah" {{ old('program') === 'Tilawah' ? 'selected' : '' }}>Tilawah</option>
                                <option value="Murottal" {{ old('program') === 'Murottal' ? 'selected' : '' }}>Murottal</option>
                                <option value="Baca_Tulis" {{ old('program') === 'Baca_Tulis' ? 'selected' : '' }}>Baca / Tulis Al-Qur'an</option>
                            </select>
                            @error('program')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- ⭐ BARU: Jenjang / Kelas --}}
                        <div>
                            <label for="grade_level" class="block text-sm font-semibold text-gray-700 mb-1">
                                Jenjang / Kelas
                                <span class="text-gray-400 text-xs font-normal">(opsional)</span>
                            </label>
                            <input id="grade_level" type="text" name="grade_level"
                                value="{{ old('grade_level') }}"
                                placeholder="Contoh: Kelas 7 / Tahfizh 1"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('grade_level') border-red-500 @enderror">
                            <span class="text-gray-400 text-xs mt-1 block">
                                Kosongkan jika belum ditentukan.
                            </span>
                            @error('grade_level')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Asal Sekolah --}}
                        <div class="md:col-span-2">
                            <label for="school_origin" class="block text-sm font-semibold text-gray-700 mb-1">
                                Asal Sekolah Saat ini <span class="text-red-500">*</span>
                            </label>
                            <input id="school_origin" type="text" name="school_origin"
                                value="{{ old('school_origin') }}"
                                required
                                placeholder="Contoh: SDN 01 Riau"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('school_origin') border-red-500 @enderror">
                            @error('school_origin')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">
                                Alamat Lengkap <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address" name="address" rows="3" required
                                placeholder="Tuliskan alamat lengkap domisili saat ini"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 resize-none @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                            @error('address')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ==================================================== --}}
                {{-- SECTION 3.5 — DOKUMEN PERSYARATAN                     --}}
                {{-- ==================================================== --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 border-b-2 border-green-100 pb-2 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Dokumen Persyaratan
                    </h3>

                    <p class="text-sm text-gray-500 mb-4">
                        Upload dokumen berikut. Format: <strong>PDF / JPG / PNG</strong> • Maks: <strong>2MB</strong> per file.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                        @php
                        // Ijazah & Pas Foto telah dihapus
                        $uploads = [
                        ['name' => 'file_kk', 'label' => 'Kartu Keluarga (KK)', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                        ['name' => 'file_akta', 'label' => 'Akta Kelahiran', 'accept' => '.pdf,.jpg,.jpeg,.png'],
                        ];
                        @endphp

                        @foreach ($uploads as $upload)
                        <div>
                            <label for="{{ $upload['name'] }}" class="block text-sm font-semibold text-gray-700 mb-1">
                                {{ $upload['label'] }} <span class="text-red-500">*</span>
                            </label>
                            <input id="{{ $upload['name'] }}"
                                type="file"
                                name="{{ $upload['name'] }}"
                                accept="{{ $upload['accept'] }}"
                                required
                                x-on:change="files.{{ $upload['name'] }} = $event.target.files[0]?.name || ''"
                                class="block w-full text-sm text-gray-600 bg-gray-50 border border-gray-200 rounded-xl shadow-sm py-2 px-3 cursor-pointer
                                              file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                              file:text-xs file:font-semibold file:bg-green-100 file:text-green-700
                                              hover:file:bg-green-200 transition @error($upload['name']) border-red-500 @enderror">
                            {{-- Preview nama file setelah dipilih --}}
                            <template x-if="files.{{ $upload['name'] }}">
                                <span class="text-xs text-green-700 mt-1 flex items-center gap-1">
                                    ✅ <span x-text="files.{{ $upload['name'] }}"></span>
                                </span>
                            </template>
                            @error($upload['name'])
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- ==================================================== --}}
                {{-- SECTION 4 — INFORMASI AKUN LOGIN                      --}}
                {{-- ==================================================== --}}
                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-900 border-b-2 border-green-100 pb-2 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Informasi Akun Login
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                                Email Aktif <span class="text-red-500">*</span>
                            </label>
                            <input id="email" type="email" name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="email@contoh.com"
                                class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 px-4 @error('email') border-red-500 @enderror">
                            @error('email')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input id="password"
                                    :type="show ? 'text' : 'password'"
                                    name="password" required
                                    autocomplete="new-password"
                                    placeholder="Minimal 8 karakter"
                                    class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 pl-4 pr-12 @error('password') border-red-500 @enderror">

                                <button type="button" @click="show = !show"
                                    aria-label="Tampilkan / sembunyikan password"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-green-600 transition-colors focus:outline-none">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div x-data="{ showConfirm: false }">
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input id="password_confirmation"
                                    :type="showConfirm ? 'text' : 'password'"
                                    name="password_confirmation" required
                                    autocomplete="new-password"
                                    placeholder="Ketik ulang password"
                                    class="block w-full bg-gray-50 border border-gray-200 focus:bg-white focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-colors py-2.5 pl-4 pr-12 @error('password_confirmation') border-red-500 @enderror">

                                <button type="button" @click="showConfirm = !showConfirm"
                                    aria-label="Tampilkan / sembunyikan konfirmasi password"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-green-600 transition-colors focus:outline-none">
                                    <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" />
                                    </svg>
                                    <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Checkbox Persetujuan T&C --}}
                <div class="mb-6 rounded-xl bg-gray-50 border border-gray-200 p-5">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="agree_terms" value="1" required
                            {{ old('agree_terms') ? 'checked' : '' }}
                            class="mt-1 w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-700 leading-relaxed">
                            Saya menyatakan bahwa seluruh data dan dokumen yang saya isi adalah
                            <strong>benar dan dapat dipertanggungjawabkan</strong>.
                            Saya juga menyetujui
                            <a href="#" class="text-green-700 font-semibold underline hover:text-green-800">Syarat &amp; Ketentuan</a>
                            serta
                            <a href="#" class="text-green-700 font-semibold underline hover:text-green-800">Kebijakan Privasi</a>
                            YPTQ Asy-Syams.
                        </span>
                    </label>
                    @error('agree_terms')
                    <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Info Alur Setelah Submit --}}
                <div class="mb-6 rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm text-blue-800">
                    <strong class="block mb-1">📋 Alur Selanjutnya:</strong>
                    <ol class="list-decimal pl-5 space-y-1">
                        <li>Data akan diverifikasi oleh panitia (1–2 hari kerja)</li>
                        <li>Anda akan menerima email berisi <strong>nomor pendaftaran</strong></li>
                        <li>Pantau status pendaftaran melalui dashboard atau hubungi panitia</li>
                    </ol>
                </div>

                {{-- FOOTER FORM — Navigasi + Submit --}}
                <div class="mt-10 pt-8 border-t border-gray-200 space-y-6">

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-6 text-center">
                        <a href="{{ url('/') }}"
                            class="group inline-flex items-center gap-1.5 text-sm font-semibold text-gray-500 hover:text-green-700 transition-colors duration-200 ease-out">
                            <svg class="w-4 h-4 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:-translate-x-0.5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali ke Beranda</span>
                        </a>

                        <span class="hidden sm:block w-px h-4 bg-gray-300"></span>

                        <a href="{{ route('login') }}"
                            class="group inline-flex items-center gap-1.5 text-sm font-semibold text-green-700 hover:text-green-800 transition-colors duration-200 ease-out">
                            <span>Sudah punya akun? Login di sini</span>
                            <svg class="w-4 h-4 transition-transform duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:translate-x-0.5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    <button type="submit"
                        :disabled="submitting"
                        class="w-full inline-flex justify-center items-center py-3.5 px-8 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700 transform hover:-translate-y-0.5 transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] tracking-wide disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none">

                        <svg x-show="!submitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        <svg x-show="submitting" x-cloak class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>

                        <span x-text="submitting ? 'Mengirim formulir...' : 'Kirim Formulir Pendaftaran'">
                            Kirim Formulir Pendaftaran
                        </span>
                    </button>

                </div>
            </form>
        </div>
    </div>
</body>

</html>