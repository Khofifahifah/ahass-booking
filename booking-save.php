<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/booking.php');
}

csrf_verify();

$nama = trim((string) ($_POST['nama'] ?? ''));
$telepon = trim((string) ($_POST['telepon'] ?? ''));
$noPolisi = strtoupper(trim((string) ($_POST['no_polisi'] ?? '')));
$tipe = (string) ($_POST['tipe_motor'] ?? '');
$keluhan = trim((string) ($_POST['keluhan'] ?? ''));
$tanggal = (string) ($_POST['tanggal'] ?? '');
$jam = (string) ($_POST['jam'] ?? '');
$packageId = (int) ($_POST['package_id'] ?? 0);
$partIds = array_map('intval', $_POST['parts'] ?? []);

$_SESSION['old'] = [
    'nama' => $nama,
    'telepon' => $telepon,
    'no_polisi' => $noPolisi,
    'tipe_motor' => $tipe,
    'keluhan' => $keluhan,
    'tanggal' => $tanggal,
    'jam' => $jam,
    'package_id' => (string) $packageId,
    'parts' => array_map('strval', $partIds),
];

$fail = function (string $message) {
    flash('error', $message);
    redirect(BASE_URL . '/booking.php');
};

if ($nama === '' || $telepon === '' || $noPolisi === '' || $tipe === '' || $tanggal === '' || $jam === '' || $packageId < 1) {
    $fail('Lengkapi data pelanggan, tanggal, jam, dan paket servis.');
}
if ($tanggal < date('Y-m-d')) {
    $fail('Tanggal servis tidak boleh di masa lalu.');
}
if (!in_array($jam, time_slots(), true)) {
    $fail('Slot jam tidak valid.');
}
if (!slot_available($tanggal, $jam)) {
    $fail('Slot jam tersebut sudah penuh. Pilih jam lain.');
}

$pkgStmt = pdo()->prepare("SELECT * FROM packages WHERE id = ? AND jenis = 'servis' AND aktif = 1");
$pkgStmt->execute([$packageId]);
$package = $pkgStmt->fetch();
if (!$package) {
    $fail('Paket servis tidak ditemukan.');
}

$items = [$package];
if ($partIds) {
    $in = implode(',', array_fill(0, count($partIds), '?'));
    $partStmt = pdo()->prepare("SELECT * FROM packages WHERE jenis = 'part' AND aktif = 1 AND id IN ($in)");
    $partStmt->execute($partIds);
    $items = array_merge($items, $partStmt->fetchAll());
}

$total = 0;
foreach ($items as $item) {
    $total += (int) $item['harga'];
}

$kode = generate_kode();
$pdo = pdo();
$pdo->beginTransaction();
try {
    $insert = $pdo->prepare(
        'INSERT INTO bookings (kode, nama_pelanggan, telepon, no_polisi, tipe_motor, keluhan, package_id, tanggal, jam, total)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $insert->execute([$kode, $nama, $telepon, $noPolisi, $tipe, $keluhan, $packageId, $tanggal, $jam, $total]);
    $bookingId = (int) $pdo->lastInsertId();

    $itemStmt = $pdo->prepare('INSERT INTO booking_items (booking_id, package_id, qty, harga) VALUES (?, ?, 1, ?)');
    foreach ($items as $item) {
        $itemStmt->execute([$bookingId, $item['id'], $item['harga']]);
    }
    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    $fail('Pendaftaran gagal disimpan. Coba lagi.');
}

unset($_SESSION['old']);
$_SESSION['last_kode'] = $kode;
redirect(BASE_URL . '/sukses.php');
