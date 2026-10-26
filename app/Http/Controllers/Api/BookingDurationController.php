<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BookingDurationController extends Controller
{
    public function index()
    {
        $durations = DB::table('hours_master')
            ->where('status', '1')
            ->select('id', 'hours')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $durations
        ]);
    }
}