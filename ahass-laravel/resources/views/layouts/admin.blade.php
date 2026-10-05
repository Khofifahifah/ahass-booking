<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · Admin AHASS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="admin-body">
<aside class="sidebar">
    <a class="brand compact" href="{{ route('admin.dashboard') }}">
        <span class="brand-mark">H</span>
        <span><strong>AHASS</strong><small>Panel Admin</small></span>
    </a>
    <nav>
        <a class="{{ ($active ?? '') === 'dash' ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
        <a class="{{ ($active ?? '') === 'bookings' ? 'is-active' : '' }}" href="{{ route('admin.bookings.index') }}">Pendaftaran</a>
        <a class="{{ ($active ?? '') === 'slots' ? 'is-active' : '' }}" href="{{ route('admin.slots') }}">Slot Jam</a>
        <a class="{{ ($active ?? '') === 'packages' ? 'is-active' : '' }}" href="{{ route('admin.packages.index') }}">Paket & Part</a>
        <a href="{{ route('home') }}">Lihat Situs</a>
        <form method="post" action="{{ route('admin.logout') }}">
            @csrf
            <button class="btn ghost" type="submit" style="width:100%;margin-top:4px">Keluar</button>
        </form>
    </nav>
</aside>
<main class="admin-main">
    @yield('content')
</main>
</body>
</html>
