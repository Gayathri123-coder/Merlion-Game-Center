<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionController extends Controller
{
    public function getSessionTime(Request $request)
    {
        // Validate input
        $request->validate([
            'duration_id' => 'required|integer',
            'start_time'  => 'required|date_format:H:i',
        ]);

        $durationId = (int) $request->duration_id;

        // Fetch duration from DB
        $durationValue = DB::table('hours_master')
            ->where('id', $durationId)
            ->where('status', '1')
            ->value('hours');

        if (!$durationValue) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive duration_id',
                'debug'   => [
                    'requested_id' => $durationId,
                    'db_value'     => $durationValue
                ]
            ], 400);
        }

        // Parse HH:MM to minutes
        $duration = $this->parseDurationToMinutes($durationValue);

        if ($duration <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parsed duration is zero or invalid',
                'debug'   => ['raw_value' => $durationValue]
            ], 400);
        }

        // Calculate end time
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime   = $startTime->copy()->addMinutes($duration);

        return response()->json([
            'success'    => true,
            'start_time' => $startTime->format('H:i'),
            'end_time'   => $endTime->format('H:i'),
            'debug'      => [
                'duration_value_db' => $durationValue,
                'duration_minutes'  => $duration
            ]
        ]);
    }

    private function parseDurationToMinutes($value)
    {
        $value = trim($value); // remove spaces

        if (strpos($value, ':') !== false) {
            list($hours, $minutes) = explode(':', $value);
            return ((int)$hours) * 60 + ((int)$minutes);
        }

        return is_numeric($value) ? (int)$value : 0;
    }
}
