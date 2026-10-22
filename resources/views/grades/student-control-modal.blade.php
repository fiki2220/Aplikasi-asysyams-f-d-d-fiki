@php
    // Load logo sebagai base64 agar tidak tergantung HTTP request
    $logoSrc = null;
    try {
        $logoPath = public_path('images/logo.PNG');
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoSrc = 'data:image/png;base64,' . base64_encode($logoData);
        }
    } catch (\Exception $e) {
        $logoSrc = null;
    }
@endphp

@include('grades.student-control-screen-style')
@include('grades.student-control-table', ['logoSrc' => $logoSrc])