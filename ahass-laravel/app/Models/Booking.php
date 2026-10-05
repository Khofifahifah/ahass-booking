<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'kode',
        'nama_pelanggan',
        'telepon',
        'no_polisi',
        'tipe_motor',
        'keluhan',
        'package_id',
        'tanggal',
        'jam',
        'status',
        'total',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'total' => 'integer',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }
}
