<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_verify(): void
{
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(400);
        exit('Token tidak valid. Muat ulang halaman.');
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function format_rupiah(int $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function status_label(string $status): string
{
    return [
        'pending' => 'Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'progress' => 'Dikerjakan',
        'done' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ][$status] ?? $status;
}

function time_slots(): array
{
    $slots = [];
    for ($hour = SLOT_START; $hour <= SLOT_END; $hour++) {
        if ($hour === SLOT_LUNCH) {
            continue;
        }
        $slots[] = sprintf('%02d:00', $hour);
    }
    return $slots;
}

function slot_usage(string $tanggal): array
{
    $stmt = pdo()->prepare(
        "SELECT jam, COUNT(*) AS total
         FROM bookings
         WHERE tanggal = ? AND status <> 'cancelled'
         GROUP BY jam"
    );
    $stmt->execute([$tanggal]);
    $map = [];
    foreach ($stmt->fetchAll() as $row) {
        $map[$row['jam']] = (int) $row['total'];
    }
    return $map;
}

function slot_available(string $tanggal, string $jam, ?int $ignoreId = null): bool
{
    $sql = "SELECT COUNT(*) FROM bookings WHERE tanggal = ? AND jam = ? AND status <> 'cancelled'";
    $params = [$tanggal, $jam];
    if ($ignoreId) {
        $sql .= ' AND id <> ?';
        $params[] = $ignoreId;
    }
    $stmt = pdo()->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn() < SLOT_CAPACITY;
}

function generate_kode(): string
{
    do {
        $kode = 'AH' . date('ymd') . strtoupper(bin2hex(random_bytes(2)));
        $stmt = pdo()->prepare('SELECT COUNT(*) FROM bookings WHERE kode = ?');
        $stmt->execute([$kode]);
    } while ((int) $stmt->fetchColumn() > 0);

    return $kode;
}

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect(BASE_URL . '/admin/login.php');
    }
}

function motor_types(): array
{
    return [
        'Beat', 'Vario 125', 'Vario 160', 'Scoopy', 'Genio', 'PCX', 'ADV',
        'Stylo', 'CBR 150R', 'CB150R', 'Sonic 150R', 'Revo', 'Supra X', 'Lainnya',
    ];
}
