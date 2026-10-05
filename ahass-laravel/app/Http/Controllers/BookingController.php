<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use App\Services\SlotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(): View
    {
        return view('booking.create', [
            'packages' => Package::query()->where('aktif', true)->where('jenis', 'servis')->orderBy('harga')->get(),
            'parts' => Package::query()->where('aktif', true)->where('jenis', 'part')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request, SlotService $slots): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'telepon' => ['required', 'string', 'max:20'],
            'no_polisi' => ['required', 'string', 'max:20'],
            'tipe_motor' => ['required', 'in:'.implode(',', config('ahass.motor_types'))],
            'keluhan' => ['nullable', 'string'],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam' => ['required', 'string'],
            'package_id' => ['required', 'integer'],
            'parts' => ['nullable', 'array'],
            'parts.*' => ['integer'],
        ]);

        if (! in_array($data['jam'], $slots->timeSlots(), true)) {
            return back()->withInput()->with('error', 'Slot jam tidak valid.');
        }

        if (! $slots->available($data['tanggal'], $data['jam'])) {
            return back()->withInput()->with('error', 'Slot jam tersebut sudah penuh. Pilih jam lain.');
        }

        $package = Package::query()
            ->where('id', $data['package_id'])
            ->where('jenis', 'servis')
            ->where('aktif', true)
            ->first();

        if (! $package) {
            return back()->withInput()->with('error', 'Paket servis tidak ditemukan.');
        }

        $partIds = $data['parts'] ?? [];
        $parts = $partIds
            ? Package::query()->where('jenis', 'part')->where('aktif', true)->whereIn('id', $partIds)->get()
            : collect();

        $items = collect([$package])->concat($parts);
        $total = $items->sum('harga');

        $booking = DB::transaction(function () use ($data, $package, $items, $total, $slots) {
            $booking = Booking::query()->create([
                'kode' => $slots->generateKode(),
                'nama_pelanggan' => $data['nama'],
                'telepon' => $data['telepon'],
                'no_polisi' => strtoupper(trim($data['no_polisi'])),
                'tipe_motor' => $data['tipe_motor'],
                'keluhan' => $data['keluhan'] ?? null,
                'package_id' => $package->id,
                'tanggal' => $data['tanggal'],
                'jam' => $data['jam'],
                'total' => $total,
            ]);

            foreach ($items as $item) {
                $booking->items()->create([
                    'package_id' => $item->id,
                    'qty' => 1,
                    'harga' => $item->harga,
                ]);
            }

            return $booking;
        });

        $request->session()->put('last_kode', $booking->kode);

        return redirect()->route('booking.success');
    }

    public function success(Request $request): View|RedirectResponse
    {
        $kode = $request->session()->get('last_kode');
        if (! $kode) {
            return redirect()->route('booking.create');
        }

        $booking = Booking::query()->where('kode', $kode)->first();
        if (! $booking) {
            return redirect()->route('booking.create');
        }

        return view('booking.success', compact('booking'));
    }
}
