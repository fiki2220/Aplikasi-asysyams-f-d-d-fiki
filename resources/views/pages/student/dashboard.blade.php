@extends('root.app')

@section('title', 'Dashboard - YPTQ Asy-Syams')

@section('content')

{{-- ========================================== --}}
{{-- TAMPILAN USTAD & SUPERADMIN                  --}}
{{-- ========================================== --}}
@if(Auth::user()->role === 'guru' || Auth::user()->role === 'superadmin')

<div class="bg-gray-50 min-h-screen pb-12">
    <div class="bg-green-800 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-3xl font-bold text-white">
                Ahlan Wa Sahlan, Ustadz {{ Auth::user()->name }}
            </h1>
            <p class="text-green-200 mt-2">
                Selamat beraktivitas! Kelola kegiatan belajar mengajar Anda di sini.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16">

        {{-- NOTIFIKASI STATUS --}}
        @if(session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-medium">{{ session('status') }}</span>
        </div>
        @endif

        {{-- CARD STATISTIK --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <a href="{{ url('/admin') }}" target="_blank" class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition border-l-4 border-green-600 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-100 text-green-700 rounded-full group-hover:bg-green-600 group-hover:text-white transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </div>
                    <div><h3 class="text-lg font-bold text-gray-900">Panel Admin</h3><p class="text-sm text-gray-500">Masuk ke menu lengkap</p></div>
                </div>
            </a>
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-full"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    <div><h3 class="text-lg font-bold text-gray-900">Total Mengajar</h3><p class="text-sm text-gray-500">{{ $totalMeetings ?? 0 }} Pertemuan</p></div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-amber-500">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-amber-100 text-amber-700 rounded-full"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6m2 4H7a2 2 0 01-2-2V7a2 2 0 012-2h3l2-2 2 2h3a2 2 0 012 2v12a2 2 0 01-2 2z"></path></svg></div>
                    <div><h3 class="text-lg font-bold text-gray-900">Absensi Harian</h3><p class="text-sm text-gray-500">{{ $attendanceSummary['total'] ?? 0 }} Data</p></div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-500 cursor-pointer" onclick="document.getElementById('logout-form').submit()">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-red-100 text-red-600 rounded-full"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg></div>
                    <div><h3 class="text-lg font-bold text-gray-900">Keluar</h3><p class="text-sm text-gray-500">Logout Sistem</p><form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form></div>
                </div>
            </div>
        </div>

        {{-- ABSENSI HARIAN USTAD --}}
        @if(Auth::user()->role === 'guru')
        @php
        $teacherStatusLabels = ['present' => 'Hadir', 'late' => 'Terlambat', 'permission' => 'Izin', 'sick' => 'Sakit', 'alpha' => 'Alpha'];
        $teacherStatusColors = ['present' => 'bg-green-100 text-green-800', 'late' => 'bg-yellow-100 text-yellow-800', 'permission' => 'bg-blue-100 text-blue-800', 'sick' => 'bg-gray-100 text-gray-800', 'alpha' => 'bg-red-100 text-red-800'];
        $teacherAttendanceStatus = $teacherAttendanceToday?->status;
        @endphp
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800">Absensi Kehadiran Harian</h2>
                <p class="text-sm text-gray-500">Check-in dan check-out harian Anda (bukan per kelas).</p>
            </div>
            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1 border border-gray-200 rounded-lg p-5">
                    <div class="text-sm font-semibold text-gray-500 uppercase">Status Hari Ini</div>
                    <div class="mt-3">
                        @if($teacherAttendanceToday)
                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-bold {{ $teacherStatusColors[$teacherAttendanceStatus] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $teacherStatusLabels[$teacherAttendanceStatus] ?? 'Belum Absen' }}
                        </span>
                        @else
                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-bold bg-gray-100 text-gray-800">Belum Absen</span>
                        @endif
                    </div>
                    <div class="mt-4 text-sm text-gray-600 space-y-1">
                        <div>Check In: <span class="font-semibold">{{ $teacherAttendanceToday?->check_in_at?->format('H:i') ?? '-' }}</span></div>
                        <div>Check Out: <span class="font-semibold">{{ $teacherAttendanceToday?->check_out_at?->format('H:i') ?? '-' }}</span></div>
                    </div>
                    <div class="mt-5 flex flex-col sm:flex-row gap-3">
                        @if(! $teacherAttendanceToday)
                        <form method="POST" action="{{ route('teacher-attendances.check-in') }}">@csrf
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-lg bg-green-700 text-white font-semibold hover:bg-green-800">Check In Harian</button>
                        </form>
                        @elseif($teacherAttendanceToday->check_in_at && ! $teacherAttendanceToday->check_out_at && ! in_array($teacherAttendanceToday->status, ['permission', 'sick', 'alpha'], true))
                        <form method="POST" action="{{ route('teacher-attendances.check-out') }}">@csrf
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-lg bg-blue-700 text-white font-semibold hover:bg-blue-800">Check Out Harian</button>
                        </form>
                        @else
                        <div class="text-sm font-semibold text-gray-600">Absensi harian selesai.</div>
                        @endif
                    </div>
                </div>
                <div class="lg:col-span-2 overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Check In</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Check Out</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($teacherAttendanceRecent as $attendance)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $attendance->date?->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold">{{ $teacherStatusLabels[$attendance->status] ?? $attendance->status }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada riwayat absensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- JADWAL & ABSENSI KELAS --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Jadwal & Absensi Kelas</h2>
                    <p class="text-sm text-gray-500">Isi absensi santri dan lakukan Check In kelas.</p>
                </div>
                @if(Auth::user()->role === 'superadmin')
                <a href="{{ url('/admin/meetings/create') }}" class="text-sm text-white bg-green-600 px-4 py-2 rounded-lg hover:bg-green-700 transition">+ Buat Pertemuan Baru</a>
                @endif
            </div>

            @if(isset($scheduleMeetings) && $scheduleMeetings->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tanggal & Jam</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Materi</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Status Kelas</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($scheduleMeetings as $meeting)
                        @php
                            $startTime = $meeting->classGroup?->start_time ? $meeting->classGroup->start_time->format('H:i') : '--:--';
                            $endTime = $meeting->classGroup?->end_time ? $meeting->classGroup->end_time->format('H:i') : '--:--';
                            $isToday = $meeting->date->isToday();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ $meeting->date?->format('d M Y') ?? '-' }} <br>
                                <span class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $startTime }} - {{ $endTime }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <div class="font-semibold text-green-700">{{ $meeting->classGroup?->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $meeting->classGroup?->semester?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $meeting->title }}</td>
                            
                            {{-- STATUS KELAS --}}
                            <td class="px-6 py-4 text-center text-sm font-bold">
                                @if($meeting->teacher_check_in_at && $meeting->teacher_check_out_at)
                                    <span class="text-blue-600 bg-blue-50 px-2 py-1 rounded">Selesai</span>
                                @elseif($meeting->teacher_check_in_at)
                                    <span class="text-green-600 bg-green-50 px-2 py-1 rounded">Berlangsung</span>
                                @else
                                    <span class="text-gray-400 bg-gray-50 px-2 py-1 rounded">Belum Mulai</span>
                                @endif
                            </td>

                            {{-- AKSI (ISI ABSENSI, CHECK IN/OUT, EDIT) --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-col sm:flex-row gap-2 justify-end items-center">
                                    
                                    {{-- 1. TOMBOL ISI ABSENSI (Modal Alpine.js) --}}
                                    @if($isToday || Auth::user()->role === 'superadmin')
                                    <div x-data="{ open: false }" class="inline-block">
                                        <button @click="open = true" type="button" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700 transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            Isi Absensi
                                        </button>
                                        
                                        {{-- MODAL POP-UP ALPINE.JS --}}
                                        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm text-left">
                                            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden" @click.away="open = false">
                                                <form action="{{ route('meetings.attendance.store', $meeting->id) }}" method="POST">
                                                    @csrf
                                                    <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                                                        <div>
                                                            <h3 class="text-lg font-bold text-gray-900">Absensi: {{ $meeting->classGroup->name }}</h3>
                                                            <p class="text-xs text-gray-500">{{ $meeting->date->format('d M Y') }}</p>
                                                        </div>
                                                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="p-6 max-h-96 overflow-y-auto space-y-3">
                                                        @foreach($meeting->classGroup->students as $student)
                                                            @php
                                                                $att = $meeting->attendances->firstWhere('user_id', $student->id);
                                                                $status = $att ? $att->status : 'present';
                                                            @endphp
                                                            <div class="flex items-center justify-between p-2 rounded hover:bg-gray-50">
                                                                <span class="text-sm font-medium text-gray-700">{{ $student->name }}</span>
                                                                <select name="attendances[{{ $student->id }}]" class="text-xs rounded border-gray-300 py-1 pl-2 pr-8">
                                                                    <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Hadir</option>
                                                                    <option value="sick" {{ $status === 'sick' ? 'selected' : '' }}>Sakit</option>
                                                                    <option value="permission" {{ $status === 'permission' ? 'selected' : '' }}>Izin</option>
                                                                    <option value="alpha" {{ $status === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                                                </select>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="px-6 py-4 bg-gray-50 border-t flex justify-end">
                                                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700">Simpan Absensi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- 2. TOMBOL CHECK IN / OUT (Khusus Ustad) --}}
                                    @if(Auth::user()->role === 'guru')
                                        @if($isToday && !$meeting->teacher_check_in_at)
                                        <form method="POST" action="{{ route('meetings.check-in', $meeting->id) }}" class="inline-block">@csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700 transition">
                                                Check In
                                            </button>
                                        </form>
                                        @elseif($meeting->teacher_check_in_at && !$meeting->teacher_check_out_at)
                                        <form method="POST" action="{{ route('meetings.check-out', $meeting->id) }}" class="inline-block">@csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white text-xs font-semibold rounded hover:bg-amber-600 transition">
                                                Check Out
                                            </button>
                                        </form>
                                        @endif
                                    @endif

                                    {{-- 3. TOMBOL EDIT MANUAL (Khusus Superadmin) --}}
                                    @if(Auth::user()->role === 'superadmin')
                                        <a href="{{ url('/admin/meetings/'.$meeting->id.'/edit') }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-gray-800 text-white text-xs font-semibold rounded hover:bg-gray-900 transition">
                                            Edit
                                        </a>
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-10 text-center">
                <div class="inline-block p-4 rounded-full bg-gray-100 text-gray-400 mb-3">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Belum ada jadwal</h3>
                <p class="text-gray-500">Jadwal kelas Anda akan muncul di sini.</p>
            </div>
            @endif
        </div>
    </div>
</div>


{{-- ========================================== --}}
{{-- TAMPILAN SISWA                               --}}
{{-- ========================================== --}}
@else
@php
// ⭐ GRAFIK: 4 BULAN TERAKHIR
$studentId = Auth::id();

$monthNames = [
1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
7 => 'Jul', 8 => 'Ags', 9 => 'Sep',
10 => 'Okt', 11 => 'Nov', 12 => 'Des',
];

$chartLabels = [];
$chartTilawah = [];
$chartTahsin = [];
$chartTahfizh = [];

$totalSetoran = 0;
$allNilai = [];

for ($i = 3; $i >= 0; $i--) {
$date = now()->subMonths($i);
$bulan = (int) $date->month;
$tahun = (int) $date->year;

$chartLabels[] = $monthNames[$bulan] . ' ' . substr((string) $tahun, -2);

$assessments = \App\Features\Grades\Models\Assessment::query()
->where('user_id', $studentId)
->where('month', $bulan)
->where('year', $tahun)
->get();

$byJenis = [
'tilawah' => [],
'tahsin' => [],
'ziyadah' => [],
'murojaah' => [],
];

foreach ($assessments as $assessment) {
$data = $assessment->data;

if (is_string($data)) {
$decoded = json_decode($data, true);
$data = is_array($decoded) ? $decoded : [];
}

if (! is_array($data)) {
$data = [];
}

foreach ($data as $item) {
if (! is_array($item)) continue;

$jenis = $item['jenis'] ?? null;
$nilai = (float) ($item['nilai_penyetoran'] ?? 0);

if ($jenis && $nilai > 0 && isset($byJenis[$jenis])) {
$byJenis[$jenis][] = $nilai;
$allNilai[] = $nilai;
$totalSetoran++;
}
}
}

$avg = fn (array $arr) => count($arr) > 0
? round(array_sum($arr) / count($arr), 1)
: null;

$chartTilawah[] = $avg($byJenis['tilawah']);
$chartTahsin[] = $avg($byJenis['tahsin']);
$chartTahfizh[] = $avg(array_merge($byJenis['ziyadah'], $byJenis['murojaah']));
}

$monthsWithData = 0;
for ($i = 0; $i < count($chartLabels); $i++) {
    if (($chartTilawah[$i] ?? null) !==null
    || ($chartTahsin[$i] ?? null) !==null
    || ($chartTahfizh[$i] ?? null) !==null) {
    $monthsWithData++;
    }
    }

    $rataRata=count($allNilai)> 0
    ? round(array_sum($allNilai) / count($allNilai), 1)
    : null;

    $nilaiTertinggi = count($allNilai) > 0 ? max($allNilai) : null;

    $isPaid = isset($activeSemester) && in_array($paymentStatus, ['success', 'paid']);
    @endphp

    <div class="bg-gray-50 min-h-screen pb-12">
        {{-- HEADER --}}
        <div class="bg-gradient-to-br from-green-700 to-green-800 pb-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h1 class="text-3xl font-bold text-white">
                    Ahlan Wa Sahlan, {{ Auth::user()->name }}
                </h1>
                <p class="text-green-100 mt-2">
                    NISN: {{ Auth::user()->nisn ?? '-' }} | Semangat menuntut ilmu hari ini!
                </p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 space-y-6">

            {{-- BANNER: Tagihan belum dibayar --}}
            @if(isset($activeSemester) && $activeSemester && ! $isPaid)
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg overflow-hidden">
                <div class="p-5 flex items-center gap-4">
                    <div class="p-3 bg-white/20 backdrop-blur rounded-xl flex-shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white uppercase tracking-wide">⚠️ Tagihan Belum Dibayar</p>
                        <p class="text-white/90 text-sm mt-1">Tagihan semester <strong>{{ $activeSemester->name }}</strong> sebesar <strong>Rp {{ number_format($billAmount, 0, ',', '.') }}</strong> belum dibayar.</p>
                    </div>
                    <a href="{{ route('student.payment') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 bg-white text-red-600 font-bold text-sm rounded-lg hover:bg-red-50 transition flex-shrink-0">Bayar Sekarang <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></a>
                </div>
                <div class="sm:hidden px-5 pb-5">
                    <a href="{{ route('student.payment') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-red-600 font-bold text-sm rounded-lg hover:bg-red-50 transition">Bayar Sekarang <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg></a>
                </div>
            </div>
            @endif

            {{-- TAGIHAN RINGKAS --}}
            @if(isset($activeSemester) && $activeSemester)
            <a href="{{ route('student.payment') }}" class="block bg-white rounded-xl shadow-md border border-gray-100 p-5 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-xl {{ $isPaid ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }} flex-shrink-0">
                        @if($isPaid) <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        @else <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg> @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tagihan {{ $activeSemester->name }}</p>
                            @if($isPaid) <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">LUNAS</span>
                            @else <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-800">BELUM BAYAR</span> @endif
                        </div>
                        <p class="text-xl font-bold text-gray-900 mt-1">Rp {{ number_format($billAmount, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center gap-2 text-gray-400 group-hover:text-emerald-600 transition-colors flex-shrink-0">
                        <span class="hidden sm:inline text-sm font-semibold">Lihat Detail</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </div>
                </div>
            </a>
            @endif

            {{-- STATISTIK RINGKAS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-emerald-500">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-emerald-100 text-emerald-700 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg></div>
                        <div><div class="text-xs font-semibold text-gray-500 uppercase">Rata-rata Nilai</div><div class="text-2xl font-bold text-gray-900">{{ $rataRata ?? '—' }}</div><div class="text-xs text-gray-500">4 bulan terakhir</div></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-blue-100 text-blue-700 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg></div>
                        <div><div class="text-xs font-semibold text-gray-500 uppercase">Nilai Tertinggi</div><div class="text-2xl font-bold text-gray-900">{{ $nilaiTertinggi ?? '—' }}</div><div class="text-xs text-gray-500">Dari semua setoran</div></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-amber-100 text-amber-700 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg></div>
                        <div><div class="text-xs font-semibold text-gray-500 uppercase">Total Setoran</div><div class="text-2xl font-bold text-gray-900">{{ $totalSetoran }}</div><div class="text-xs text-gray-500">4 bulan terakhir</div></div>
                    </div>
                </div>
            </div>

            {{-- GRAFIK TREN NILAI --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-white border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">📈 Tren Nilai 4 Bulan Terakhir</h2>
                    <p class="text-sm text-gray-500 mt-1">Grafik rata-rata nilai setoran Anda per bulan.</p>
                </div>
                <div class="p-6">
                    @if($totalSetoran > 0 && $monthsWithData >= 2)
                    <div style="position: relative; height: 320px; width: 100%;"><canvas id="trenNilaiChart"></canvas></div>
                    @elseif($totalSetoran > 0 && $monthsWithData < 2)
                    <div class="py-12 text-center">
                        <div class="inline-block p-4 rounded-full bg-blue-50 text-blue-500 mb-3"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                        <h3 class="text-lg font-semibold text-gray-900">Data Belum Cukup untuk Grafik Tren</h3>
                        <p class="text-gray-500 mt-1 max-w-md mx-auto">Grafik akan muncul setelah Anda memiliki data setoran di <strong>2 bulan atau lebih</strong>.</p>
                    </div>
                    @else
                    <div class="py-12 text-center">
                        <div class="inline-block p-4 rounded-full bg-gray-100 text-gray-400 mb-3"><svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg></div>
                        <h3 class="text-lg font-medium text-gray-900">Belum ada data setoran</h3>
                        <p class="text-gray-500 mt-1">Grafik akan muncul setelah Anda memiliki nilai setoran.</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- MENU GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('student.transcript') }}" class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-all border border-gray-100 group">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-full group-hover:scale-110 transition-transform"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg></div>
                        <div><h3 class="text-lg font-bold text-gray-900">Transkrip Nilai</h3><p class="text-sm text-gray-500">Lihat Nilai</p></div>
                    </div>
                </a>
                <a href="{{ route('student.attendance') }}" class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition-all border border-gray-100 group">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-100 text-purple-600 rounded-full group-hover:scale-110 transition-transform"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                        <div><h3 class="text-lg font-bold text-gray-900">Riwayat Absensi</h3><p class="text-sm text-gray-500">Cek Kehadiran</p></div>
                    </div>
                </a>
                <div class="relative bg-white rounded-xl shadow hover:shadow-lg transition-all border border-gray-100 group overflow-hidden">
                    <a href="{{ route('profile') }}" class="absolute inset-0 z-0 rounded-xl" aria-label="Buka Profil Saya"><span class="sr-only">Buka Profil Saya</span></a>
                    <div class="relative flex items-center gap-4 p-6 pointer-events-none">
                        <div class="p-3 bg-gray-100 text-gray-600 rounded-full group-hover:scale-110 transition-transform"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg></div>
                        <div class="flex-1 min-w-0"><h3 class="text-lg font-bold text-gray-900 group-hover:text-emerald-700 transition-colors">Profil Saya</h3><p class="text-sm text-gray-500">Lihat &amp; ubah profil</p></div>
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </div>
                    <div class="relative border-t border-gray-100"></div>
                    <div class="relative p-4 bg-gray-50/50">
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 text-sm text-red-600 hover:text-red-800 font-semibold transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg> Keluar (Logout)</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($totalSetoran > 0 && $monthsWithData >= 2)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            const canvas = document.getElementById('trenNilaiChart');
            if (!canvas || typeof Chart === 'undefined') return;
            const ctx = canvas.getContext('2d');
            const makeGradient = (color) => { const g = ctx.createLinearGradient(0, 0, 0, 320); g.addColorStop(0, color + '33'); g.addColorStop(1, color + '00'); return g; };
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        { label: 'Tilawah', data: @json($chartTilawah), borderColor: '#2563eb', backgroundColor: makeGradient('#2563eb'), borderWidth: 3, tension: 0.35, fill: true, pointBackgroundColor: '#2563eb', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7 },
                        { label: 'Tahsin', data: @json($chartTahsin), borderColor: '#eab308', backgroundColor: makeGradient('#eab308'), borderWidth: 3, tension: 0.35, fill: true, pointBackgroundColor: '#eab308', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7 },
                        { label: 'Tahfizh', data: @json($chartTahfizh), borderColor: '#16a34a', backgroundColor: makeGradient('#16a34a'), borderWidth: 3, tension: 0.35, fill: true, pointBackgroundColor: '#16a34a', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 5, pointHoverRadius: 7 },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, pointStyle: 'circle', padding: 16, font: { size: 13, weight: '600' } } }, tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.95)', titleColor: '#fff', bodyColor: '#e5e7eb', padding: 12, cornerRadius: 8, titleFont: { size: 13, weight: 'bold' }, bodyFont: { size: 12 }, displayColors: true, callbacks: { label: function(context) { let val = context.parsed.y; return context.dataset.label + ': ' + (val !== null ? val : '—'); } } } }, scales: { y: { beginAtZero: false, min: 0, max: 100, grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false }, ticks: { stepSize: 20, font: { size: 12 }, color: '#6b7280' } }, x: { grid: { display: false }, ticks: { font: { size: 12, weight: '500' }, color: '#374151' } } }, elements: { line: { spanGaps: true } } },
            });
        })();
    </script>
    @endif

    @endif

    @endsection