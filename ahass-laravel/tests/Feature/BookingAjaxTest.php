<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use Database\Seeders\PackageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_slot_full_returns_json_error(): void
    {
        $this->seed(PackageSeeder::class);
        $package = Package::query()->where('jenis', 'servis')->first();
        $tanggal = now()->toDateString();
        $jam = '08:00';

        for ($i = 1; $i <= 3; $i++) {
            Booking::query()->create([
                'kode' => 'AHTEST'.$i,
                'nama_pelanggan' => 'Pelanggan '.$i,
                'telepon' => '0812345678'.$i,
                'no_polisi' => 'B '.$i.' TES',
                'tipe_motor' => 'Beat',
                'package_id' => $package->id,
                'tanggal' => $tanggal,
                'jam' => $jam,
                'total' => $package->harga,
            ]);
        }

        $response = $this->postJson(route('booking.store'), [
            'nama' => 'Pelanggan 4',
            'telepon' => '08123456789',
            'no_polisi' => 'B 4 TES',
            'tipe_motor' => 'Beat',
            'tanggal' => $tanggal,
            'jam' => $jam,
            'package_id' => $package->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'slot_penuh')
            ->assertJsonPath('ok', false);
    }

    public function test_booking_store_returns_json_success(): void
    {
        $this->seed(PackageSeeder::class);
        $package = Package::query()->where('jenis', 'servis')->first();

        $response = $this->postJson(route('booking.store'), [
            'nama' => 'Budi',
            'telepon' => '081234567890',
            'no_polisi' => 'B 1234 TES',
            'tipe_motor' => 'Beat',
            'tanggal' => now()->toDateString(),
            'jam' => '09:00',
            'package_id' => $package->id,
        ]);

        $response->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonStructure(['booking' => ['kode', 'jam']]);
    }
}
