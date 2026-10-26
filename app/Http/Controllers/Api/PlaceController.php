<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    // 🔹 Place dropdown list
    public function index(Request $request)
    {
        $places = DB::table('place_master')
            ->where('status', 1)
            ->orderBy('place_name', 'asc')
            ->get([
                'id',
                'place_name'
            ]);

        return response()->json([
            'success' => true,
            'data' => $places
        ]);
    }
}