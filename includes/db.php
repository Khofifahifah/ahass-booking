<?php
declare(strict_types=1);

function pdo(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function init_database(): void
{
    $root = new PDO(
        'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $root->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $root->exec('USE `' . DB_NAME . '`');

    $root->exec(<<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
SQL);

    $root->exec(<<<SQL
CREATE TABLE IF NOT EXISTS packages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jenis ENUM('servis','part') NOT NULL,
    nama VARCHAR(120) NOT NULL,
    deskripsi TEXT NULL,
    harga INT UNSIGNED NOT NULL DEFAULT 0,
    durasi_menit SMALLINT UNSIGNED NOT NULL DEFAULT 60,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB
SQL);

    $root->exec(<<<SQL
CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(16) NOT NULL UNIQUE,
    nama_pelanggan VARCHAR(120) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    no_polisi VARCHAR(20) NOT NULL,
    tipe_motor VARCHAR(80) NOT NULL,
    keluhan TEXT NULL,
    package_id INT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    jam CHAR(5) NOT NULL,
    status ENUM('pending','confirmed','progress','done','cancelled') NOT NULL DEFAULT 'pending',
    total INT UNSIGNED NOT NULL DEFAULT 0,
    catatan_admin TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_booking_package FOREIGN KEY (package_id) REFERENCES packages(id)
) ENGINE=InnoDB
SQL);

    $root->exec(<<<SQL
CREATE TABLE IF NOT EXISTS booking_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    package_id INT UNSIGNED NOT NULL,
    qty SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    harga INT UNSIGNED NOT NULL,
    CONSTRAINT fk_item_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_item_package FOREIGN KEY (package_id) REFERENCES packages(id)
) ENGINE=InnoDB
SQL);

    $count = (int) $root->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $root->prepare('INSERT INTO users (username, password_hash, nama) VALUES (?, ?, ?)');
        $stmt->execute(['admin', $hash, 'Admin Bengkel']);
    }

    $pkg = (int) $root->query('SELECT COUNT(*) FROM packages')->fetchColumn();
    if ($pkg === 0) {
        $seed = [
            ['servis', 'Servis Ringan', 'Ganti oli, cek rem, cek lampu, dan inspeksi cepat.', 75000, 45],
            ['servis', 'Servis Berkala 4.000 km', 'Oli, filter, busi, dan cek kelistrikan.', 150000, 90],
            ['servis', 'Servis Berkala 8.000 km', 'Paket lengkap termasuk pembersihan injektor.', 250000, 120],
            ['servis', 'Servis Injeksi', 'Pembersihan throttle body dan injektor.', 125000, 75],
            ['servis', 'Tune Up', 'Penyetelan mesin, CVT, dan sistem pengereman.', 200000, 100],
            ['servis', 'Ganti Kampas Rem', 'Pemeriksaan dan penggantian kampas rem.', 80000, 40],
            ['part', 'Oli AHM MPX 1 0.8L', 'Oli mesin matic resmi AHM.', 55000, 15],
            ['part', 'Oli AHM MPX 2 1.2L', 'Oli mesin matic kapasitas 1.2 liter.', 75000, 15],
            ['part', 'Filter Udara', 'Filter udara original Honda.', 45000, 20],
            ['part', 'Busi Original', 'Busi NGK/Denso sesuai tipe motor.', 35000, 15],
            ['part', 'Kampas Rem Depan', 'Kampas rem depan original.', 65000, 30],
            ['part', 'Aki GS GTZ5S', 'Aki kering untuk motor matic.', 250000, 30],
        ];
        $stmt = $root->prepare('INSERT INTO packages (jenis, nama, deskripsi, harga, durasi_menit) VALUES (?, ?, ?, ?, ?)');
        foreach ($seed as $row) {
            $stmt->execute($row);
        }
    }
}
