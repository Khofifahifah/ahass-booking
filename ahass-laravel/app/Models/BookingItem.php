<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'package_id',
        'qty',
        'harga',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'harga' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
