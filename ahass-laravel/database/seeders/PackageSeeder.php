<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['jenis' => 'servis', 'nama' => 'Servis Ringan', 'deskripsi' => 'Ganti oli, cek rem, cek lampu, dan inspeksi cepat.', 'harga' => 75000, 'durasi_menit' => 45],
            ['jenis' => 'servis', 'nama' => 'Servis Berkala 4.000 km', 'deskripsi' => 'Oli, filter, busi, dan cek kelistrikan.', 'harga' => 150000, 'durasi_menit' => 90],
            ['jenis' => 'servis', 'nama' => 'Servis Berkala 8.000 km', 'deskripsi' => 'Paket lengkap termasuk pembersihan injektor.', 'harga' => 250000, 'durasi_menit' => 120],
            ['jenis' => 'servis', 'nama' => 'Servis Injeksi', 'deskripsi' => 'Pembersihan throttle body dan injektor.', 'harga' => 125000, 'durasi_menit' => 75],
            ['jenis' => 'servis', 'nama' => 'Tune Up', 'deskripsi' => 'Penyetelan mesin, CVT, dan sistem pengereman.', 'harga' => 200000, 'durasi_menit' => 100],
            ['jenis' => 'servis', 'nama' => 'Ganti Kampas Rem', 'deskripsi' => 'Pemeriksaan dan penggantian kampas rem.', 'harga' => 80000, 'durasi_menit' => 40],
            ['jenis' => 'part', 'nama' => 'Oli AHM MPX 1 0.8L', 'deskripsi' => 'Oli mesin matic resmi AHM.', 'harga' => 55000, 'durasi_menit' => 15],
            ['jenis' => 'part', 'nama' => 'Oli AHM MPX 2 1.2L', 'deskripsi' => 'Oli mesin matic kapasitas 1.2 liter.', 'harga' => 75000, 'durasi_menit' => 15],
            ['jenis' => 'part', 'nama' => 'Filter Udara', 'deskripsi' => 'Filter udara original Honda.', 'harga' => 45000, 'durasi_menit' => 20],
            ['jenis' => 'part', 'nama' => 'Busi Original', 'deskripsi' => 'Busi NGK/Denso sesuai tipe motor.', 'harga' => 35000, 'durasi_menit' => 15],
            ['jenis' => 'part', 'nama' => 'Kampas Rem Depan', 'deskripsi' => 'Kampas rem depan original.', 'harga' => 65000, 'durasi_menit' => 30],
            ['jenis' => 'part', 'nama' => 'Aki GS GTZ5S', 'deskripsi' => 'Aki kering untuk motor matic.', 'harga' => 250000, 'durasi_menit' => 30],
        ];

        foreach ($rows as $row) {
            Package::query()->create($row);
        }
    }
}
