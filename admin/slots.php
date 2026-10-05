<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';
require_admin();

$tanggal = (string) ($_GET['tanggal'] ?? date('Y-m-d'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    $tanggal = date('Y-m-d');
}
$usage = slot_usage($tanggal);
$stmt = pdo()->prepare(
    "SELECT b.*, p.nama AS paket FROM bookings b JOIN packages p ON p.id = b.package_id
     WHERE b.tanggal = ? AND b.status <> 'cancelled' ORDER BY b.jam, b.id"
);
$stmt->execute([$tanggal]);
$bookings = $stmt->fetchAll();
$byJam = [];
foreach ($bookings as $row) {
    $byJam[$row['jam']][] = $row;
}

render_admin_header('Slot jam', 'slots');
?>
<h1>Ketersediaan slot</h1>
<form class="toolbar" method="get">
    <input type="date" name="tanggal" value="<?= e($tanggal) ?>">
    <button class="btn" type="submit">Lihat</button>
</form>
<div class="cards">
    <?php foreach (time_slots() as $jam):
        $used = $usage[$jam] ?? 0;
        $sisa = max(0, SLOT_CAPACITY - $used);
        ?>
        <article class="card">
            <h3><?= e($jam) ?></h3>
            <p><?= $used ?> / <?= SLOT_CAPACITY ?> terisi · <?= $sisa ?> sisa</p>
            <?php foreach ($byJam[$jam] ?? [] as $row): ?>
                <p>
                    <a href="<?= e(BASE_URL) ?>/admin/booking-detail.php?id=<?= (int) $row['id'] ?>"><?= e($row['kode']) ?></a>
                    · <?= e($row['nama_pelanggan']) ?>
                    · <span class="badge <?= e($row['status']) ?>"><?= e(status_label($row['status'])) ?></span>
                </p>
            <?php endforeach; ?>
            <?php if (empty($byJam[$jam])): ?><p class="muted">Belum ada booking.</p><?php endif; ?>
        </article>
    <?php endforeach; ?>
</div>
<?php render_admin_footer();