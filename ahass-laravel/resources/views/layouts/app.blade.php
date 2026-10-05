<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<header class="topbar">
    <div class="container nav">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark">H</span>
            <span>
                <strong>AHASS</strong>
                <small>Booking Servis Honda</small>
            </span>
        </a>
        <nav>
            <a class="{{ ($active ?? '') === 'home' ? 'is-active' : '' }}" href="{{ route('home') }}">Beranda</a>
            <a class="{{ ($active ?? '') === 'booking' ? 'is-active' : '' }}" href="{{ route('booking.create') }}">Daftar Servis</a>
            <a class="{{ ($active ?? '') === 'cek' ? 'is-active' : '' }}" href="{{ route('status.form') }}">Cek Booking</a>
            <a class="nav-admin" href="{{ route('admin.login') }}">Admin</a>
        </nav>
    </div>
</header>
<main>
    @yield('content')
</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <strong>{{ config('ahass.workshop_name') }}</strong>
            <p>{{ config('ahass.workshop_address') }}</p>
        </div>
        <div>
            <strong>Jam operasional</strong>
            <p>Senin–Sabtu · 08.00–16.00<br>Istirahat 12.00–13.00</p>
        </div>
        <div>
            <strong>Kontak</strong>
            <p>{{ config('ahass.workshop_phone') }}</p>
        </div>
    </div>
</footer>
@yield('scripts')
</body>
</html>
