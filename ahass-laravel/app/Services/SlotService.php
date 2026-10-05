<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SlotService
{
    public function timeSlots(): array
    {
        $slots = [];
        for ($hour = (int) config('ahass.slot_start'); $hour <= (int) config('ahass.slot_end'); $hour++) {
            if ($hour === (int) config('ahass.slot_lunch')) {
                continue;
            }
            $slots[] = sprintf('%02d:00', $hour);
        }

        return $slots;
    }

    public function usage(string $tanggal): Collection
    {
        return Booking::query()
            ->whereDate('tanggal', $tanggal)
            ->where('status', '<>', 'cancelled')
            ->selectRaw('jam, COUNT(*) as total')
            ->groupBy('jam')
            ->pluck('total', 'jam');
    }

    public function available(string $tanggal, string $jam, ?int $ignoreId = null): bool
    {
        $query = Booking::query()
            ->whereDate('tanggal', $tanggal)
            ->where('jam', $jam)
            ->where('status', '<>', 'cancelled');

        if ($ignoreId) {
            $query->where('id', '<>', $ignoreId);
        }

        return $query->count() < (int) config('ahass.slot_capacity');
    }

    public function payload(string $tanggal): array
    {
        $usage = $this->usage($tanggal);
        $capacity = (int) config('ahass.slot_capacity');
        $slots = [];

        foreach ($this->timeSlots() as $jam) {
            $used = (int) ($usage[$jam] ?? 0);
            $slots[] = [
                'jam' => $jam,
                'terisi' => $used,
                'kapasitas' => $capacity,
                'sisa' => max(0, $capacity - $used),
            ];
        }

        return ['tanggal' => $tanggal, 'slots' => $slots];
    }

    public function generateKode(): string
    {
        do {
            $kode = 'AH'.now()->format('ymd').strtoupper(Str::random(4));
        } while (Booking::query()->where('kode', $kode)->exists());

        return $kode;
    }
}
