<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

$booking = null;
$items = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $kode = strtoupper(trim((string) ($_POST['kode'] ?? '')));
    $telepon = trim((string) ($_POST['telepon'] ?? ''));
    $stmt = pdo()->prepare('SELECT * FROM bookings WHERE kode = ? AND telepon = ?');
    $stmt->execute([$kode, $telepon]);
    $booking = $stmt->fetch();
    if ($booking) {
        $itemStmt = pdo()->prepare(
            'SELECT bi.*, p.nama, p.jenis FROM booking_items bi JOIN packages p ON p.id = bi.package_id WHERE bi.booking_id = ?'
        );
        $itemStmt->execute([$booking['id']]);
        $items = $itemStmt->fetchAll();
    } else {
        flash('error', 'Booking tidak ditemukan. Periksa kode dan nomor HP.');
    }
}

render_header('Cek Booking', 'cek');
?>
<section class="section">
    <div class="container" style="max-width:720px">
        <form class="panel" method="post">
            <h2>Cek status booking</h2>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
            <div class="field">
                <label for="kode">Kode booking</label>
                <input id="kode" name="kode" required placeholder="AH...">
            </div>
            <div class="field">
                <label for="telepon">Nomor HP</label>
                <input id="telepon" name="telepon" required>
            </div>
            <button class="btn" type="submit">Lihat status</button>
        </form>

        <?php if ($booking): ?>
            <div class="panel" style="margin-top:16px">
                <span class="badge <?= e($booking['status']) ?>"><?= e(status_label($booking['status'])) ?></span>
                <h2><?= e($booking['kode']) ?></h2>
                <p><?= e($booking['nama_pelanggan']) ?> · <?= e($booking['no_polisi']) ?></p>
                <p><?= date('d/m/Y', strtotime($booking['tanggal'])) ?> pukul <?= e($booking['jam']) ?></p>
                <ul>
                    <?php foreach ($items as $item): ?>
                        <li><?= e($item['nama']) ?> — <?= e(format_rupiah((int) $item['harga'])) ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Total <?= e(format_rupiah((int) $booking['total'])) ?></strong></p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php render_footer();