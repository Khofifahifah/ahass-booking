<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusController extends Controller
{
    public function form(): View
    {
        return view('status.form');
    }

    public function show(Request $request): View
    {
        $data = $request->validate([
            'kode' => ['required', 'string'],
            'telepon' => ['required', 'string'],
        ]);

        $booking = Booking::query()
            ->with(['items.package'])
            ->where('kode', strtoupper(trim($data['kode'])))
            ->where('telepon', trim($data['telepon']))
            ->first();

        if (! $booking) {
            return view('status.form')->with('error', 'Booking tidak ditemukan. Periksa kode dan nomor HP.');
        }

        return view('status.form', compact('booking'));
    }
}
