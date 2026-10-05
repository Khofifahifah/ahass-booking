# Sistem Booking Servis AHASS (Laravel)

Aplikasi booking servis Honda AHASS, sekarang memakai **Laravel 12**.

## Persyaratan

- PHP 8.2+ (XAMPP)
- Composer
- MySQL (XAMPP)

## Cara menjalankan

1. Nyalakan Apache dan MySQL di XAMPP.
2. Di folder `ahass-laravel`:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

3. Buka `http://127.0.0.1:8000`
4. Login admin: **admin** / **admin123**

Pengaturan database ada di `.env` (default database `ahass_laravel`, user `root`, password kosong).

Versi PHP native lama tetap ada di folder `ahass-booking` jika masih dibutuhkan.

## Fitur

- Form booking: data pelanggan, tanggal, slot jam, paket servis, part tambahan
- Kapasitas 4 motor per jam (08.00–16.00, istirahat 12.00)
- Cek status booking dengan kode + nomor HP
- Panel admin: dashboard, daftar pendaftaran, ubah status, slot harian, kelola paket/part
