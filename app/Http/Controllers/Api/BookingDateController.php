<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingDateController extends Controller
{
    public function index(Request $request)
    {
        
        $placeId = $request->header('place-id');

        if (!$placeId) {
            return response()->json([
                'success' => false,
                'message' => 'place_id required'
            ], 422);
        }

        $dates = [];

        for ($i = 0; $i < 7; $i++) {
            $day = Carbon::today()->addDays($i);

            if ($i == 0) {
                $label = "Today";
            } elseif ($i == 1) {
                $label = "Tomorrow";
            } else {
                $label = $day->format('D');
            }

            $dates[] = [
                'label' => $label,
                'date'  => $day->format('d-m-Y'),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $dates
        ]);
    }
}