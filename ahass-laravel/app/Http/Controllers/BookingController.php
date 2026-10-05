<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use App\Services\SlotService;
use Illuminate\Http\JsonResponse;
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

    public function store(Request $request, SlotService $slots): JsonResponse
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
            return response()->json([
                'ok' => false,
                'error' => 'jam_tidak_valid',
                'message' => 'Slot jam tidak valid.',
            ], 422);
        }

        $package = Package::query()
            ->where('id', $data['package_id'])
            ->where('jenis', 'servis')
            ->where('aktif', true)
            ->first();

        if (! $package) {
            return response()->json([
                'ok' => false,
                'error' => 'paket_tidak_ditemukan',
                'message' => 'Paket servis tidak ditemukan.',
            ], 422);
        }

        $partIds = $data['parts'] ?? [];
        $parts = $partIds
            ? Package::query()->where('jenis', 'part')->where('aktif', true)->whereIn('id', $partIds)->get()
            : collect();

        $items = collect([$package])->concat($parts);
        $total = $items->sum('harga');

        if (! $slots->available($data['tanggal'], $data['jam'])) {
            return response()->json([
                'ok' => false,
                'error' => 'slot_penuh',
                'message' => 'Slot jam tersebut sudah penuh. Maksimal 3 kendaraan per jam.',
            ], 422);
        }

        $booking = DB::transaction(function () use ($data, $package, $items, $total, $slots) {
            if (! $slots->available($data['tanggal'], $data['jam'])) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
                    'ok' => false,
                    'error' => 'slot_penuh',
                    'message' => 'Slot jam tersebut sudah penuh. Maksimal 3 kendaraan per jam.',
                ], 422));
            }

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

        $booking->load('package');

        return response()->json([
            'ok' => true,
            'message' => 'Pendaftaran servis berhasil dikirim.',
            'booking' => [
                'kode' => $booking->kode,
                'nama_pelanggan' => $booking->nama_pelanggan,
                'no_polisi' => $booking->no_polisi,
                'tipe_motor' => $booking->tipe_motor,
                'tanggal' => $booking->tanggal->format('d/m/Y'),
                'jam' => $booking->jam,
                'paket' => $booking->package->nama,
                'total' => format_rupiah($booking->total),
            ],
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $tanggal = (string) $request->query('tanggal', '');

        $rows = Booking::query()
            ->with('package')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('kode', 'like', "%{$q}%")
                        ->orWhere('nama_pelanggan', 'like', "%{$q}%")
                        ->orWhere('no_polisi', 'like', "%{$q}%");
                });
            })
            ->when(preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal), fn ($query) => $query->whereDate('tanggal', $tanggal))
            ->orderByDesc('tanggal')
            ->orderBy('jam')
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(fn (Booking $row) => [
                'kode' => $row->kode,
                'nama_pelanggan' => $row->nama_pelanggan,
                'no_polisi' => $row->no_polisi,
                'tipe_motor' => $row->tipe_motor,
                'tanggal' => $row->tanggal->format('d/m/Y'),
                'jam' => $row->jam,
                'paket' => $row->package->nama,
                'status' => $row->status,
                'status_label' => status_label($row->status),
            ]);

        return response()->json(['data' => $rows]);
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
