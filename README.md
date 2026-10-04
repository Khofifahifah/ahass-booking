# Sistem Booking Servis AHASS

Aplikasi web sederhana untuk pendaftaran servis pelanggan Honda AHASS: cek ketersediaan slot jam, pilih paket servis, dan tambah part.

## Persyaratan

- XAMPP (Apache + MySQL + PHP 8+)
- Database dibuat otomatis saat halaman pertama dibuka

## Cara menjalankan

1. Pastikan Apache dan MySQL di XAMPP sudah berjalan.
2. Buka `http://localhost/AHASS/ahass-booking/`
3. Login admin: **admin** / **admin123**

Pengaturan database ada di `includes/config.php` (default user `root`, password kosong).

## Fitur

- Form booking: data pelanggan, tanggal, slot jam, paket servis, part tambahan
- Kapasitas 4 motor per jam (08.00–16.00, istirahat 12.00)
- Cek status booking dengan kode + nomor HP
- Panel admin: dashboard, daftar pendaftaran, ubah status, slot harian, kelola paket/part
