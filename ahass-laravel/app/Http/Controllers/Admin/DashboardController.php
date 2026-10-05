<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();

        return view('admin.dashboard', [
            'hariIni' => Booking::query()->whereDate('tanggal', $today)->where('status', '<>', 'cancelled')->count(),
            'pending' => Booking::query()->where('status', 'pending')->count(),
            'total' => Booking::query()->count(),
            'omzet' => (int) Booking::query()->whereIn('status', ['confirmed', 'progress', 'done'])->sum('total'),
            'recent' => Booking::query()->with('package')->latest('id')->limit(8)->get(),
        ]);
    }
}
