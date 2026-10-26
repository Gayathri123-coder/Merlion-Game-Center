<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Submissiondata;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\PriceMaster;

class CreateConsoleController extends Controller
{
    // Convert duration string → total minutes
    private function parseDurationToMinutes($duration)
    {
        $duration = strtolower(trim($duration));
        $minutes = 0;

        if (preg_match('/^(\d+):(\d+)$/', $duration, $matches)) {
            $minutes = intval($matches[1]) * 60 + intval($matches[2]);
        } elseif (preg_match('/(\d+)\s*hour/', $duration, $matches)) {
            $minutes = intval($matches[1]) * 60;
        } elseif (preg_match('/(\d+)\s*min/', $duration, $matches)) {
            $minutes = intval($matches[1]);
        }

        return $minutes;
    }

    // Convert minutes → "HH:MM"
    private function formatMinutesToHHMM($minutes)
    {
        $hours = floor($minutes / 60);
        $mins  = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }

    // How many 30-min slots
    private function getSlotCount($duration)
    {
        $minutes = $this->parseDurationToMinutes($duration);
        return ceil($minutes / 30);
    }

    public function submitBooking(Request $request)
{
    $placeId = $request->header('place-id');

if (!$placeId) {
    return response()->json([
        'success' => false,
        'message' => 'place_id required'
    ], 422);
}

    // Map duration → hours_id if not sent
    if (!$request->has('hours_id')) {
        $hoursRow = DB::table('hours_master')
            ->where('hours', $request->duration)
            ->where('status', '1')
            ->first();

        $request->merge([
            'hours_id' => $hoursRow ? $hoursRow->id : null,
        ]);
    }

    if (!$request->hours_id) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid duration. No matching hours found.',
        ], 422);
    }

    // Map mobile and other info
    $request->merge([
        'no_of_players'       => $request->players ?? 1,
        'player_informations' => $request->player_informations ?? '',
        'fandb_informations'  => $request->fandb_informations ?? '',
        'payment_type'        => $request->payment_type ?? 0,
        'reference_id'        => $request->reference_id ?? 0,
    ]);

    $mobileNo = $request->input('mobile_no') ?? $request->header('mobile_no') ?? (auth()->user()->mobile_no ?? null);
    $user = null;

    if ($mobileNo) {
        $user = DB::table('create_account')->where('mobile_no', $mobileNo)->first();

        if (!$user) {
            $userId = DB::table('create_account')->insertGetId([
                'fullname'   => 'Player-' . substr($mobileNo, -4),
                'mobile_no'  => $mobileNo,
                'password'   => bcrypt(str()->random(8)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $user = DB::table('create_account')->where('id', $userId)->first();
        }
    }

    $filterName = $user->fullname ?? 'Guest';
    $request->merge([
        'filter_mobile_no' => $mobileNo,
        'filter_name'      => $filterName,
    ]);

    // Validation
    $request->validate([
        'console_id'    => 'required|integer|exists:console_master,id',
        'hours_id'      => 'required|integer|exists:hours_master,id',
        'date'          => 'required|date',
        'start_time'    => 'required|string',
        'duration'      => 'required|string',
        'no_of_players' => 'required|integer',
    ]);

    // Fetch console info
  $console = DB::table('console_master')
    ->where('id', $request->console_id)
    ->where('place_id', $placeId)
    ->first();

    if (!$console) {
        return response()->json(['success' => false, 'message' => 'Invalid console selected.'], 422);
    }

    if ($request->no_of_players > $console->limited_person) {
        return response()->json([
            'success' => false,
            'message' => "Maximum allowed players for {$console->console_name} is {$console->limited_person}.",
        ], 422);
    }

    // Build start & end datetime
    $startTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->start_time, 'Asia/Kolkata');
    $durationMinutes = $this->parseDurationToMinutes($request->duration);
    $endTime = $startTime->copy()->addMinutes($durationMinutes);

    // Today + past time check
    $currentDateTime = Carbon::now('Asia/Kolkata');
    if ($startTime->lt($currentDateTime)) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot book past time slots.'
        ], 422);
    }

    // Calculate requested slots
    $slotsNeeded = ceil($durationMinutes / 30);
    $requestedSlots = [];
    for ($i = 0; $i < $slotsNeeded; $i++) {
        $requestedSlots[] = $startTime->copy()->addMinutes($i * 30)->format('H:i');
    }

    // Check linked consoles for overlapping bookings
    $checkConsoles = [$console->id];
    if ($console->linked_console_id) {
        $linked = array_map('intval', explode(',', $console->linked_console_id));
        $checkConsoles = array_merge($checkConsoles, $linked);
    }
    $checkConsoles = array_unique($checkConsoles);

    // Fetch already booked slots
    $bookedSlots = Submissiondata::whereIn('console_id', $checkConsoles)
        ->where('date', $request->date)
        ->where('status', 1)
        ->get()
        ->map(function ($b) {
            $s = Carbon::parse($b->date . ' ' . $b->start_time);
            $e = Carbon::parse($b->date . ' ' . $b->end_time);
            $slots = [];
            for ($t = $s->copy(); $t->lt($e); $t->addMinutes(30)) {
                $slots[] = $t->format('H:i');
            }
            return $slots;
        })->flatten()->toArray();

    // Check overlapping
    foreach ($requestedSlots as $slot) {
        if (in_array($slot, $bookedSlots)) {
            return response()->json(['success' => false, 'message' => 'Some slots already booked.'], 409);
        }
    }

    // Build all slots status
    $allSlots = [];
    $opening = Carbon::parse('09:00');
    $closing = Carbon::parse('23:00');
    for ($time = $opening->copy(); $time->lt($closing); $time->addMinutes(30)) {
        $timeStr = $time->format('H:i');
        $allSlots[$timeStr] = in_array($timeStr, $bookedSlots) ? 1 : 0;
    }
    for ($t = $startTime->copy(); $t->lt($endTime); $t->addMinutes(30)) {
        $allSlots[$t->format('H:i')] = 1;
    }

    $formattedDuration = $this->formatMinutesToHHMM($durationMinutes);
    $slotCount = $this->getSlotCount($request->duration);

    // Calculate price
   $priceRow = PriceMaster::where('console_id', $request->console_id)
    ->where('place_id', $placeId)
    ->where('hours_id', $request->hours_id)
    ->where('no_of_players', $request->no_of_players)
    ->first();

    $totalCost = $priceRow ? floatval($priceRow->price) : 0;

    // 🔹 RETURN PREVIEW / SUMMARY ONLY, NO DB INSERT YET
return response()->json([
    'success'    => true,
    'message'    => 'Booking summary ready. Please pay to confirm.',
    'data'       => [
        'booking_id'   => null, // not yet inserted
         'place_id' => $placeId,
        'console_name' => $console->console_name,
        'console_icon' => $console->console_icon ? asset('storage/'.$console->console_icon) : null,
        'date'         => Carbon::parse($request->date)->format('D, M d, Y'),
        'time_slot'    => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
        'duration'     => $formattedDuration,
        'players'      => $request->no_of_players . ' player' . ($request->no_of_players > 1 ? 's' : ''),
        'price_breakdown'=> [
            'label' => $console->console_name . ' ' . $formattedDuration,
            'calc'  => $request->no_of_players . ' player(s)',
            'amount'=> number_format($totalCost, 2)
        ],
        'total_amount' => number_format($totalCost, 2),
        'mobile_no'    => $request->filter_mobile_no,
        'name'         => $request->filter_name,
        'player_informations' => $request->player_informations,
        'fandb_informations'  => $request->fandb_informations,
        'payment_type'        => $request->payment_type,
        'reference_id'        => $request->reference_id,
    ],
    'slot_count'   => $slotCount,
    'slots_status' => $allSlots
]);
}
public function getPrice(Request $request)
{
    $placeId = $request->header('place-id');

    if (!$placeId) {
        return response()->json([
            'success' => false,
            'message' => 'place_id required'
        ], 422);
    }

    $request->validate([
        'console_id'    => 'required|integer',
        'hours_id'      => 'required|integer',
        'no_of_players' => 'required|integer|min:1',
    ]);

    $price = PriceMaster::where('place_id', $placeId)   // ✅ DIRECT
        ->where('console_id', $request->console_id)
        ->where('hours_id', $request->hours_id)
        ->where('no_of_players', $request->no_of_players)
        ->value('price');

    return response()->json([
        'success' => true,
        'price'   => $price ? number_format($price, 2) : '0.00',
    ]);
}


}