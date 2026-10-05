<?php

namespace App\Http\Controllers;

use App\Services\SlotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function index(Request $request, SlotService $slots): JsonResponse
    {
        $data = $request->validate([
            'tanggal' => ['required', 'date_format:Y-m-d'],
        ]);

        return response()->json($slots->payload($data['tanggal']));
    }
}
