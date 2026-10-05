<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'jenis',
        'nama',
        'deskripsi',
        'harga',
        'durasi_menit',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'durasi_menit' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }
}
