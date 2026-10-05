<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$stmt = pdo()->prepare('SELECT b.*, p.nama AS paket FROM bookings b JOIN packages p ON p.id = b.package_id WHERE b.id = ?');
$stmt->execute([$id]);
$booking = $stmt->fetch();
if (!$booking) {
    redirect(BASE_URL . '/admin/bookings.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $status = (string) ($_POST['status'] ?? '');
    $catatan = trim((string) ($_POST['catatan_admin'] ?? ''));
    if (!in_array($status, ['pending', 'confirmed', 'progress', 'done', 'cancelled'], true)) {
        flash('error', 'Status tidak valid.');
        redirect(BASE_URL . '/admin/booking-detail.php?id=' . $id);
    }
    if ($status !== 'cancelled' && !slot_available($booking['tanggal'], $booking['jam'], $id)) {
        flash('error', 'Slot jam sudah penuh, tidak bisa mengaktifkan booking ini.');
        redirect(BASE_URL . '/admin/booking-detail.php?id=' . $id);
    }
    $upd = pdo()->prepare('UPDATE bookings SET status = ?, catatan_admin = ? WHERE id = ?');
    $upd->execute([$status, $catatan, $id]);
    flash('ok', 'Status booking diperbarui.');
    redirect(BASE_URL . '/admin/booking-detail.php?id=' . $id);
}

$itemStmt = pdo()->prepare(
    'SELECT bi.*, p.nama, p.jenis FROM booking_items bi JOIN packages p ON p.id = bi.package_id WHERE bi.booking_id = ?'
);
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

render_admin_header('Detail booking', 'bookings');
?>
<h1><?= e($booking['kode']) ?></h1>
<?php if ($msg = flash('ok')): ?><div class="alert ok"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
<div class="grid-2">
    <div class="panel">
        <p><strong><?= e($booking['nama_pelanggan']) ?></strong><br><?= e($booking['telepon']) ?></p>
        <p><?= e($booking['no_polisi']) ?> · <?= e($booking['tipe_motor']) ?></p>
        <p>Jadwal: <?= date('d/m/Y', strtotime($booking['tanggal'])) ?> pukul <?= e($booking['jam']) ?></p>
        <p>Keluhan: <?= e($booking['keluhan'] ?: '-') ?></p>
        <h3>Item</h3>
        <ul>
            <?php foreach ($items as $item): ?>
                <li><?= e($item['jenis']) ?> · <?= e($item['nama']) ?> — <?= e(format_rupiah((int) $item['harga'])) ?></li>
            <?php endforeach; ?>
        </ul>
        <p><strong>Total <?= e(format_rupiah((int) $booking['total'])) ?></strong></p>
    </div>
    <form class="panel" method="post">
        <h2>Ubah status</h2>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (['pending','confirmed','progress','done','cancelled'] as $st): ?>
                    <option value="<?= $st ?>" <?= $booking['status'] === $st ? 'selected' : '' ?>><?= e(status_label($st)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="catatan_admin">Catatan admin</label>
            <textarea id="catatan_admin" name="catatan_admin"><?= e($booking['catatan_admin'] ?? '') ?></textarea>
        </div>
        <button class="btn" type="submit">Simpan</button>
    </form>
</div>
<?php render_admin_footer();