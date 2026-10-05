<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin · AHASS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body class="login-wrap">
    <form class="panel login-card" method="post" action="{{ route('admin.login.store') }}">
        @csrf
        <h2>Masuk panel AHASS</h2>
        <p class="muted">Kelola pendaftaran, slot jam, dan paket servis.</p>
        @if (session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif
        <div class="field">
            <label for="username">Username</label>
            <input id="username" name="username" value="{{ old('username', 'admin') }}" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>
        <button class="btn" type="submit">Masuk</button>
        <p class="muted" style="margin-top:12px">Default: admin / admin123</p>
        <p><a href="{{ route('home') }}">Kembali ke situs</a></p>
    </form>
</body>
</html>
