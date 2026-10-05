<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$tanggal = (string) ($_GET['tanggal'] ?? date('Y-m-d'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tanggal tidak valid']);
    exit;
}

$usage = slot_usage($tanggal);
$slots = [];
foreach (time_slots() as $jam) {
    $used = $usage[$jam] ?? 0;
    $slots[] = [
        'jam' => $jam,
        'terisi' => $used,
        'kapasitas' => SLOT_CAPACITY,
        'sisa' => max(0, SLOT_CAPACITY - $used),
    ];
}

echo json_encode(['tanggal' => $tanggal, 'slots' => $slots]);
