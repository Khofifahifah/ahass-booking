<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

$today = date('Y-m-d');
$usage = slot_usage($today);
$open = 0;
foreach (time_slots() as $jam) {
    $used = $usage[$jam] ?? 0;
    $open += max(0, SLOT_CAPACITY - $used);
}

render_header('Beranda', 'home');
?>
<section class="hero">
    <div class="container">
        <p class="muted" style="color:#ffd2ce;margin:0 0 8px">Bengkel resmi Honda</p>
        <h1>Booking servis motor Honda tanpa antrian di bengkel.</h1>
        <p>Pilih tanggal, cek slot jam yang masih kosong, lalu tentukan paket servis dan part original AHM.</p>
        <div class="hero-actions">
            <a class="btn" href="<?= e(BASE_URL) ?>/booking.php">Daftar servis sekarang</a>
            <a class="btn ghost" href="<?= e(BASE_URL) ?>/cek.php">Cek status booking</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-3">
            <article class="card">
                <h3>1. Isi data kendaraan</h3>
                <p class="muted">Nama, nomor HP, plat, dan tipe motor Honda Anda.</p>
            </article>
            <article class="card">
                <h3>2. Pilih slot jam</h3>
                <p class="muted">Kapasitas <?= SLOT_CAPACITY ?> unit per jam. Slot penuh tidak bisa dipilih.</p>
            </article>
            <article class="card">
                <h3>3. Paket & part</h3>
                <p class="muted">Pilih servis berkala, tune up, atau tambah oli dan suku cadang.</p>
            </article>
        </div>

        <div class="panel" style="margin-top:24px">
            <h2>Slot hari ini</h2>
            <p class="muted"><?= (int) $open ?> tempat masih tersedia untuk <?= date('d/m/Y') ?>.</p>
            <div class="slots" style="margin-top:12px">
                <?php foreach (time_slots() as $jam):
                    $used = $usage[$jam] ?? 0;
                    $sisa = max(0, SLOT_CAPACITY - $used);
                    ?>
                    <div class="slot <?= $sisa === 0 ? 'is-full' : '' ?>">
                        <strong><?= e($jam) ?></strong>
                        <small><?= $sisa === 0 ? 'Penuh' : $sisa . ' sisa' ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php render_footer();