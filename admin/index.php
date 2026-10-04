<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';
require_admin();

$today = date('Y-m-d');
$stmt = pdo()->prepare("SELECT COUNT(*) FROM bookings WHERE tanggal = ? AND status <> 'cancelled'");
$stmt->execute([$today]);
$hariIni = (int) $stmt->fetchColumn();
$pending = (int) pdo()->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$total = (int) pdo()->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
$omzet = (int) pdo()->query("SELECT COALESCE(SUM(total),0) FROM bookings WHERE status IN ('confirmed','progress','done')")->fetchColumn();

$recent = pdo()->query(
    'SELECT b.*, p.nama AS paket FROM bookings b JOIN packages p ON p.id = b.package_id ORDER BY b.id DESC LIMIT 8'
)->fetchAll();

render_admin_header('Dashboard', 'dash');
?>
<h1>Dashboard</h1>
<p class="muted">Halo, <?= e($_SESSION['admin_nama'] ?? 'Admin') ?>.</p>
<div class="stats">
    <div class="stat"><span class="muted">Booking hari ini</span><b><?= $hariIni ?></b></div>
    <div class="stat"><span class="muted">Menunggu konfirmasi</span><b><?= $pending ?></b></div>
    <div class="stat"><span class="muted">Total pendaftaran</span><b><?= $total ?></b></div>
    <div class="stat"><span class="muted">Estimasi omzet</span><b><?= e(format_rupiah($omzet)) ?></b></div>
</div>
<div class="panel">
    <h2>Pendaftaran terbaru</h2>
    <table>
        <thead>
            <tr><th>Kode</th><th>Pelanggan</th><th>Jadwal</th><th>Paket</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php foreach ($recent as $row): ?>
            <tr>
                <td><a href="<?= e(BASE_URL) ?>/admin/booking-detail.php?id=<?= (int) $row['id'] ?>"><?= e($row['kode']) ?></a></td>
                <td><?= e($row['nama_pelanggan']) ?><br><small class="muted"><?= e($row['no_polisi']) ?></small></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?> <?= e($row['jam']) ?></td>
                <td><?= e($row['paket']) ?></td>
                <td><span class="badge <?= e($row['status']) ?>"><?= e(status_label($row['status'])) ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$recent): ?>
            <tr><td colspan="5" class="muted">Belum ada pendaftaran.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php render_admin_footer();