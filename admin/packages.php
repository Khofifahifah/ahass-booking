<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string) ($_POST['action'] ?? 'save');
    if ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        pdo()->prepare('UPDATE packages SET aktif = 1 - aktif WHERE id = ?')->execute([$id]);
        flash('ok', 'Status paket diperbarui.');
        redirect(BASE_URL . '/admin/packages.php');
    }

    $id = (int) ($_POST['id'] ?? 0);
    $jenis = (string) ($_POST['jenis'] ?? 'servis');
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $deskripsi = trim((string) ($_POST['deskripsi'] ?? ''));
    $harga = (int) ($_POST['harga'] ?? 0);
    $durasi = (int) ($_POST['durasi_menit'] ?? 60);
    if (!in_array($jenis, ['servis', 'part'], true) || $nama === '' || $harga < 0) {
        flash('error', 'Data paket tidak lengkap.');
        redirect(BASE_URL . '/admin/packages.php');
    }
    if ($id > 0) {
        pdo()->prepare('UPDATE packages SET jenis=?, nama=?, deskripsi=?, harga=?, durasi_menit=? WHERE id=?')
            ->execute([$jenis, $nama, $deskripsi, $harga, $durasi, $id]);
        flash('ok', 'Paket diperbarui.');
    } else {
        pdo()->prepare('INSERT INTO packages (jenis, nama, deskripsi, harga, durasi_menit) VALUES (?,?,?,?,?)')
            ->execute([$jenis, $nama, $deskripsi, $harga, $durasi]);
        flash('ok', 'Paket baru ditambahkan.');
    }
    redirect(BASE_URL . '/admin/packages.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = pdo()->prepare('SELECT * FROM packages WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch();
}
$rows = pdo()->query('SELECT * FROM packages ORDER BY jenis, nama')->fetchAll();

render_admin_header('Paket & Part', 'packages');
?>
<h1>Paket servis & part</h1>
<?php if ($msg = flash('ok')): ?><div class="alert ok"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
<div class="grid-2">
    <form class="panel" method="post">
        <h2><?= $edit ? 'Ubah paket' : 'Tambah paket' ?></h2>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <div class="field">
            <label>Jenis</label>
            <select name="jenis">
                <option value="servis" <?= (($edit['jenis'] ?? '') === 'servis') ? 'selected' : '' ?>>Servis</option>
                <option value="part" <?= (($edit['jenis'] ?? '') === 'part') ? 'selected' : '' ?>>Part</option>
            </select>
        </div>
        <div class="field">
            <label>Nama</label>
            <input name="nama" required value="<?= e($edit['nama'] ?? '') ?>">
        </div>
        <div class="field">
            <label>Deskripsi</label>
            <textarea name="deskripsi"><?= e($edit['deskripsi'] ?? '') ?></textarea>
        </div>
        <div class="grid-2">
            <div class="field">
                <label>Harga</label>
                <input type="number" name="harga" min="0" required value="<?= e((string) ($edit['harga'] ?? '0')) ?>">
            </div>
            <div class="field">
                <label>Durasi (menit)</label>
                <input type="number" name="durasi_menit" min="0" value="<?= e((string) ($edit['durasi_menit'] ?? '60')) ?>">
            </div>
        </div>
        <button class="btn" type="submit">Simpan</button>
    </form>
    <div class="panel">
        <h2>Daftar</h2>
        <table>
            <thead><tr><th>Nama</th><th>Harga</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <strong><?= e($row['nama']) ?></strong><br>
                        <small class="muted"><?= e($row['jenis']) ?> · <?= $row['aktif'] ? 'aktif' : 'nonaktif' ?></small>
                    </td>
                    <td><?= e(format_rupiah((int) $row['harga'])) ?></td>
                    <td>
                        <a href="?edit=<?= (int) $row['id'] ?>">Ubah</a>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                            <button class="btn ghost" type="submit"><?= $row['aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_admin_footer();