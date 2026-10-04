<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

$kode = $_SESSION['last_kode'] ?? '';
if ($kode === '') {
    redirect(BASE_URL . '/booking.php');
}

$stmt = pdo()->prepare('SELECT * FROM bookings WHERE kode = ?');
$stmt->execute([$kode]);
$booking = $stmt->fetch();
if (!$booking) {
    redirect(BASE_URL . '/booking.php');
}

render_header('Booking berhasil', 'booking');
?>
<section class="section">
    <div class="container">
        <div class="panel">
            <div class="alert ok">Pendaftaran servis berhasil dikirim.</div>
            <h2>Kode booking: <?= e($booking['kode']) ?></h2>
            <p><?= e($booking['nama_pelanggan']) ?> · <?= e($booking['no_polisi']) ?> · <?= e($booking['tipe_motor']) ?></p>
            <p><strong><?= date('d/m/Y', strtotime($booking['tanggal'])) ?> pukul <?= e($booking['jam']) ?></strong></p>
            <p>Total estimasi: <?= e(format_rupiah((int) $booking['total'])) ?></p>
            <p class="muted">Simpan kode ini untuk cek status. Admin bengkel akan mengonfirmasi slot Anda.</p>
            <a class="btn" href="<?= e(BASE_URL) ?>/cek.php">Cek status booking</a>
        </div>
    </div>
</section>
<?php render_footer();