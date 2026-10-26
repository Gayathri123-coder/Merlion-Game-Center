<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingStartTimeController extends Controller
{
    public function index(Request $request)
    {
        $placeId  = $request->header('place-id');
        $consoleId = $request->query('console_id');
        $dateInput = $request->query('date', Carbon::today()->toDateString());

        if (!$placeId) {
            return response()->json([
                'success' => false,
                'message' => 'place_id required'
            ], 422);
        }

        // Normalize date
        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateInput)->toDateString();
        } catch (\Exception $e) {
            try {
                $date = Carbon::createFromFormat('d-m-Y', $dateInput)->toDateString();
            } catch (\Exception $e2) {
                $date = Carbon::today()->toDateString();
            }
        }

        // ✅ Fetch consoles ONLY for this place
        $consoles = DB::table('console_master')
            ->where('status', 1)
            ->where('place_id', $placeId)
            ->get();

        if ($consoleId) {
            $consoles = $consoles->where('id', $consoleId);
        }

        $result = [];

        foreach ($consoles as $console) {

            // Linked consoles
            $linkedIds = [];
            if (!empty($console->linked_console_id)) {
                $linkedIds = array_map('intval', explode(',', $console->linked_console_id));
            }

            $allConsoleIds = array_merge([$console->id], $linkedIds);

            // ✅ Fetch bookings ONLY for this place
            $bookings = DB::table('submission_data')
                ->where('place_id', $placeId)
                ->whereIn('console_id', $allConsoleIds)
                ->whereDate('date', $date)
                ->where('status', 1)
                ->get();

            $bookedSlots = [];

            foreach ($bookings as $booking) {

                try {
                    $start = Carbon::parse($booking->start_time);
                    $end   = Carbon::parse($booking->end_time);
                } catch (\Exception $e) {
                    continue;
                }

                while ($start < $end) {
                    $bookedSlots[] = $start->format('H:i');
                    $start->addMinutes(30);
                }
            }

            // Build slots 09:00 → 22:30
            $slots = [];
            $slotStart = Carbon::createFromTime(9, 0);
            $slotEnd   = Carbon::createFromTime(22, 30);

            while ($slotStart <= $slotEnd) {
                $time = $slotStart->format('H:i');
                $slots[] = [
                    'time'   => $time,
                    'status' => in_array($time, $bookedSlots) ? 1 : 0
                ];
                $slotStart->addMinutes(30);
            }

            $result[] = [
                'console_id'   => $console->id,
                'console_name' => $console->console_name,
                'slots'        => $slots
            ];
        }

        return response()->json([
            'success'  => true,
            'date'     => $date,
            'consoles' => $result
        ]);
    }
}