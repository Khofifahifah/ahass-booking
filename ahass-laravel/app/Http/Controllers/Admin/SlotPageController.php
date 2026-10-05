<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\SlotService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlotPageController extends Controller
{
    public function index(Request $request, SlotService $slots): View
    {
        $tanggal = $request->query('tanggal', now()->toDateString());
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $tanggal)) {
            $tanggal = now()->toDateString();
        }

        $usage = $slots->usage($tanggal);
        $bookings = Booking::query()
            ->with('package')
            ->whereDate('tanggal', $tanggal)
            ->where('status', '<>', 'cancelled')
            ->orderBy('jam')
            ->orderBy('id')
            ->get()
            ->groupBy('jam');

        return view('admin.slots', [
            'tanggal' => $tanggal,
            'timeSlots' => $slots->timeSlots(),
            'usage' => $usage,
            'bookings' => $bookings,
        ]);
    }
}
