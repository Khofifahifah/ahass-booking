<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';
require_admin();

$status = (string) ($_GET['status'] ?? '');
$q = trim((string) ($_GET['q'] ?? ''));
$sql = 'SELECT b.*, p.nama AS paket FROM bookings b JOIN packages p ON p.id = b.package_id WHERE 1=1';
$params = [];
if ($status !== '' && in_array($status, ['pending', 'confirmed', 'progress', 'done', 'cancelled'], true)) {
    $sql .= ' AND b.status = ?';
    $params[] = $status;
}
if ($q !== '') {
    $sql .= ' AND (b.kode LIKE ? OR b.nama_pelanggan LIKE ? OR b.no_polisi LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like);
}
$sql .= ' ORDER BY b.tanggal DESC, b.jam ASC, b.id DESC';
$stmt = pdo()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

render_admin_header('Pendaftaran', 'bookings');
?>
<h1>Pendaftaran servis</h1>
<form class="toolbar" method="get">
    <input name="q" placeholder="Cari kode, nama, plat" value="<?= e($q) ?>">
    <select name="status">
        <option value="">Semua status</option>
        <?php foreach (['pending','confirmed','progress','done','cancelled'] as $st): ?>
            <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= e(status_label($st)) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn" type="submit">Filter</button>
</form>
<div class="panel">
    <table>
        <thead>
            <tr><th>Kode</th><th>Pelanggan</th><th>Jadwal</th><th>Paket</th><th>Total</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><a href="<?= e(BASE_URL) ?>/admin/booking-detail.php?id=<?= (int) $row['id'] ?>"><?= e($row['kode']) ?></a></td>
                <td><?= e($row['nama_pelanggan']) ?><br><small class="muted"><?= e($row['telepon']) ?> · <?= e($row['no_polisi']) ?></small></td>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?> <?= e($row['jam']) ?></td>
                <td><?= e($row['paket']) ?></td>
                <td><?= e(format_rupiah((int) $row['total'])) ?></td>
                <td><span class="badge <?= e($row['status']) ?>"><?= e(status_label($row['status'])) ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?>
            <tr><td colspan="6" class="muted">Tidak ada data.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php render_admin_footer();