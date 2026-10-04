<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/layout.php';

$packages = pdo()->query("SELECT * FROM packages WHERE aktif = 1 AND jenis = 'servis' ORDER BY harga")->fetchAll();
$parts = pdo()->query("SELECT * FROM packages WHERE aktif = 1 AND jenis = 'part' ORDER BY nama")->fetchAll();
$error = flash('error');
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

render_header('Daftar Servis', 'booking');
?>
<section class="section">
    <div class="container grid-2">
        <form class="panel" method="post" action="<?= e(BASE_URL) ?>/booking-save.php">
            <h2>Form pendaftaran servis</h2>
            <p class="muted">Isi data pelanggan, pilih jam, lalu tentukan paket.</p>
            <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <div class="grid-2">
                <div class="field">
                    <label for="nama">Nama pelanggan</label>
                    <input id="nama" name="nama" required value="<?= e($old['nama'] ?? '') ?>">
                </div>
                <div class="field">
                    <label for="telepon">Nomor HP</label>
                    <input id="telepon" name="telepon" required value="<?= e($old['telepon'] ?? '') ?>">
                </div>
            </div>
            <div class="grid-2">
                <div class="field">
                    <label for="no_polisi">Nomor polisi</label>
                    <input id="no_polisi" name="no_polisi" required value="<?= e($old['no_polisi'] ?? '') ?>">
                </div>
                <div class="field">
                    <label for="tipe_motor">Tipe motor</label>
                    <select id="tipe_motor" name="tipe_motor" required>
                        <?php foreach (motor_types() as $type): ?>
                            <option <?= (($old['tipe_motor'] ?? '') === $type) ? 'selected' : '' ?>><?= e($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field">
                <label for="keluhan">Keluhan / permintaan</label>
                <textarea id="keluhan" name="keluhan"><?= e($old['keluhan'] ?? '') ?></textarea>
            </div>
            <div class="field">
                <label for="tanggal">Tanggal servis</label>
                <input id="tanggal" type="date" name="tanggal" required min="<?= date('Y-m-d') ?>" value="<?= e($old['tanggal'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="field">
                <label>Slot jam</label>
                <div id="slot-list" class="slots"></div>
            </div>

            <h3>Paket servis</h3>
            <?php foreach ($packages as $pkg): ?>
                <label class="pkg">
                    <input type="radio" name="package_id" value="<?= (int) $pkg['id'] ?>" required <?= ((string) ($old['package_id'] ?? '') === (string) $pkg['id']) ? 'checked' : '' ?>>
                    <span>
                        <strong><?= e($pkg['nama']) ?></strong>
                        <div class="muted"><?= e($pkg['deskripsi'] ?? '') ?> · <?= (int) $pkg['durasi_menit'] ?> menit</div>
                    </span>
                    <span class="price"><?= e(format_rupiah((int) $pkg['harga'])) ?></span>
                </label>
            <?php endforeach; ?>

            <h3>Part tambahan (opsional)</h3>
            <?php foreach ($parts as $part): ?>
                <label class="pkg">
                    <input type="checkbox" name="parts[]" value="<?= (int) $part['id'] ?>" <?= in_array((string) $part['id'], $old['parts'] ?? [], true) ? 'checked' : '' ?>>
                    <span>
                        <strong><?= e($part['nama']) ?></strong>
                        <div class="muted"><?= e($part['deskripsi'] ?? '') ?></div>
                    </span>
                    <span class="price"><?= e(format_rupiah((int) $part['harga'])) ?></span>
                </label>
            <?php endforeach; ?>

            <button class="btn" type="submit">Kirim pendaftaran</button>
        </form>
        <aside class="panel">
            <h2>Ketersediaan slot</h2>
            <p class="muted">Setiap jam menampung maksimal <?= SLOT_CAPACITY ?> motor. Istirahat bengkel pukul 12.00.</p>
            <p class="muted">Setelah terkirim, catat kode booking untuk cek status.</p>
        </aside>
    </div>
</section>
<script>
const endpoint = <?= json_encode(BASE_URL . '/api/slots.php') ?>;
const selected = <?= json_encode($old['jam'] ?? '') ?>;
const tanggal = document.getElementById('tanggal');
const list = document.getElementById('slot-list');

async function loadSlots() {
    const res = await fetch(endpoint + '?tanggal=' + encodeURIComponent(tanggal.value));
    const data = await res.json();
    list.innerHTML = '';
    data.slots.forEach((slot) => {
        const label = document.createElement('label');
        label.className = 'slot' + (slot.sisa === 0 ? ' is-full' : '');
        label.innerHTML = `
            <input type="radio" name="jam" value="${slot.jam}" ${slot.sisa === 0 ? 'disabled' : ''} ${selected === slot.jam ? 'checked' : ''} required>
            <strong>${slot.jam}</strong>
            <small>${slot.sisa === 0 ? 'Penuh' : slot.sisa + ' sisa'}</small>
        `;
        list.appendChild(label);
    });
}
tanggal.addEventListener('change', loadSlots);
loadSlots();
</script>
<?php render_footer();