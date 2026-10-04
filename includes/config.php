<?php
declare(strict_types=1);

define('APP_NAME', 'AHASS Booking');
define('WORKSHOP_NAME', 'AHASS Honda Sentosa');
define('WORKSHOP_ADDRESS', 'Jl. Raya Servis No. 88, Jakarta');
define('WORKSHOP_PHONE', '021-555-0188');

define('DB_HOST', 'localhost');
define('DB_NAME', 'ahass_booking');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('SLOT_START', 8);
define('SLOT_END', 16);
define('SLOT_LUNCH', 12);
define('SLOT_CAPACITY', 4);

define('BASE_PATH', dirname(__DIR__));

$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$marker = '/ahass-booking/';
$pos = stripos($script, $marker);
if ($pos !== false) {
    define('BASE_URL', substr($script, 0, $pos + strlen($marker) - 1));
} else {
    define('BASE_URL', '/AHASS/ahass-booking');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
