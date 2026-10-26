<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BookingPlayerController extends Controller
{
    //  List consoles with max players (NO place-id)
 public function index(Request $request)
{
    $consoleId = $request->query('console_id');

    //  Global max players
    $row = DB::table('no_of_player_master')->first();
    if (!$row) {
        return response()->json([
            'success' => false,
            'message' => 'No player config found'
        ], 404);
    }
    $globalMax = (int) $row->player_number;

    //  Console-specific limit (if console_id passed)
    $consoleLimit = $globalMax;

    if ($consoleId) {
        $console = DB::table('console_master')
            ->where('id', $consoleId)
            ->where('status', 1)
            ->select('limited_person')
            ->first();

        if ($console && $console->limited_person) {
            $consoleLimit = min($globalMax, (int) $console->limited_person);
        }
    }

    //  Build dropdown 1 → final limit
    $dropdown = [];
    for ($i = 1; $i <= $consoleLimit; $i++) {
        $dropdown[] = [
            'value' => $i,
            'label' => $i === 1 ? '1 Player' : $i . ' Players'
        ];
    }

    return response()->json([
        'success' => true,
        'data' => $dropdown
    ]);
}

}