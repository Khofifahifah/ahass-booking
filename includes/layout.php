<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';

function render_header(string $title, string $active = ''): void
{
    $pageTitle = $title . ' · ' . APP_NAME;
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
<header class="topbar">
    <div class="container nav">
        <a class="brand" href="<?= e(BASE_URL) ?>/index.php">
            <span class="brand-mark">H</span>
            <span>
                <strong>AHASS</strong>
                <small>Booking Servis Honda</small>
            </span>
        </a>
        <nav>
            <a class="<?= $active === 'home' ? 'is-active' : '' ?>" href="<?= e(BASE_URL) ?>/index.php">Beranda</a>
            <a class="<?= $active === 'booking' ? 'is-active' : '' ?>" href="<?= e(BASE_URL) ?>/booking.php">Daftar Servis</a>
            <a class="<?= $active === 'cek' ? 'is-active' : '' ?>" href="<?= e(BASE_URL) ?>/cek.php">Cek Booking</a>
            <a class="nav-admin" href="<?= e(BASE_URL) ?>/admin/login.php">Admin</a>
        </nav>
    </div>
</header>
<main>
    <?php
}

function render_footer(): void
{
    ?>
</main>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <strong><?= e(WORKSHOP_NAME) ?></strong>
            <p><?= e(WORKSHOP_ADDRESS) ?></p>
        </div>
        <div>
            <strong>Jam operasional</strong>
            <p>Senin–Sabtu · 08.00–16.00<br>Istirahat 12.00–13.00</p>
        </div>
        <div>
            <strong>Kontak</strong>
            <p><?= e(WORKSHOP_PHONE) ?></p>
        </div>
    </div>
</footer>
</body>
</html>
    <?php
}

function render_admin_header(string $title, string $active = ''): void
{
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · Admin AHASS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body class="admin-body">
<aside class="sidebar">
    <a class="brand compact" href="<?= e(BASE_URL) ?>/admin/index.php">
        <span class="brand-mark">H</span>
        <span><strong>AHASS</strong><small>Panel Admin</small></span>
    </a>
    <nav>
        <a class="<?= $active === 'dash' ? 'is-active' : '' ?>" href="<?= e(BASE_URL) ?>/admin/index.php">Dashboard</a>
        <a class="<?= $active === 'bookings' ? 'is-active' : '' ?>" href="<?= e(BASE_URL) ?>/admin/bookings.php">Pendaftaran</a>
        <a class="<?= $active === 'slots' ? 'is-active' : '' ?>" href="<?= e(BASE_URL) ?>/admin/slots.php">Slot Jam</a>
        <a class="<?= $active === 'packages' ? 'is-active' : '' ?>" href="<?= e(BASE_URL) ?>/admin/packages.php">Paket & Part</a>
        <a href="<?= e(BASE_URL) ?>/index.php">Lihat Situs</a>
        <a href="<?= e(BASE_URL) ?>/admin/logout.php">Keluar</a>
    </nav>
</aside>
<main class="admin-main">
    <?php
}

function render_admin_footer(): void
{
    echo '</main></body></html>';
}
