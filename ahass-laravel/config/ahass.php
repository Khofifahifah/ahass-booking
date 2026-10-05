<?php

return [
    'workshop_name' => env('WORKSHOP_NAME', 'AHASS Honda Sentosa'),
    'workshop_address' => env('WORKSHOP_ADDRESS', 'Jl. Raya Servis No. 88, Jakarta'),
    'workshop_phone' => env('WORKSHOP_PHONE', '021-555-0188'),
    'slot_start' => (int) env('SLOT_START', 8),
    'slot_end' => (int) env('SLOT_END', 16),
    'slot_lunch' => (int) env('SLOT_LUNCH', 12),
    'slot_capacity' => (int) env('SLOT_CAPACITY', 4),
    'motor_types' => [
        'Beat', 'Vario 125', 'Vario 160', 'Scoopy', 'Genio', 'PCX', 'ADV',
        'Stylo', 'CBR 150R', 'CB150R', 'Sonic 150R', 'Revo', 'Supra X', 'Lainnya',
    ],
    'statuses' => [
        'pending' => 'Menunggu',
        'confirmed' => 'Dikonfirmasi',
        'progress' => 'Dikerjakan',
        'done' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ],
];
