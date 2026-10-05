<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\SlotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $status = (string) $request->query('status', '');
        $q = trim((string) $request->query('q', ''));

        $rows = Booking::query()
            ->with('package')
            ->when($status !== '' && array_key_exists($status, config('ahass.statuses')), fn ($query) => $query->where('status', $status))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('kode', 'like', "%{$q}%")
                        ->orWhere('nama_pelanggan', 'like', "%{$q}%")
                        ->orWhere('no_polisi', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('tanggal')
            ->orderBy('jam')
            ->orderByDesc('id')
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $rows->map(fn (Booking $row) => [
                    'kode' => $row->kode,
                    'url' => route('admin.bookings.show', $row),
                    'nama_pelanggan' => $row->nama_pelanggan,
                    'telepon' => $row->telepon,
                    'no_polisi' => $row->no_polisi,
                    'jadwal' => $row->tanggal->format('d/m/Y').' '.$row->jam,
                    'paket' => $row->package->nama,
                    'total' => format_rupiah($row->total),
                    'status' => $row->status,
                    'status_label' => status_label($row->status),
                ]),
            ]);
        }

        return view('admin.bookings.index', compact('rows', 'status', 'q'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['package', 'items.package']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function update(Request $request, Booking $booking, SlotService $slots): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,progress,done,cancelled'],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        if ($data['status'] !== 'cancelled' && ! $slots->available($booking->tanggal->toDateString(), $booking->jam, $booking->id)) {
            return back()->with('error', 'Slot jam sudah penuh, tidak bisa mengaktifkan booking ini.');
        }

        $booking->update($data);

        return back()->with('ok', 'Status booking diperbarui.');
    }
}
