<?php

namespace App\Http\Controllers;

use App\Services\SlotService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(SlotService $slots): View
    {
        $today = now()->toDateString();
        $payload = $slots->payload($today);
        $open = collect($payload['slots'])->sum('sisa');

        return view('home', [
            'today' => $today,
            'open' => $open,
            'slots' => $payload['slots'],
        ]);
    }
}
