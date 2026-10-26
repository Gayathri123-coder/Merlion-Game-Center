<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Submissiondata;
use App\Models\CreateAccountModel;
use App\FandBMaster; //for F&B details
use Carbon\Carbon;

class BookingHistoryController extends Controller
{
   
    public function history(Request $request)
    {
        

        $mobileNo = $request->header('mobile_no') 
                    ?? $request->query('mobile_no') 
                    ?? (auth()->user()->mobile_no ?? null);

        if (!$mobileNo) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not provided'
            ], 400);
        }

        $user = CreateAccountModel::where('mobile_no', $mobileNo)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $now = Carbon::now();

        $bookings = Submissiondata::with(['GetHoursInfo', 'GetConsoleInfo'])
            ->where('filter_mobile_no', $user->mobile_no) 
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $mapBooking = function($b) use ($now) {
            $checkConsoles = [$b->console_id];

            if ($b->GetConsoleInfo && $b->GetConsoleInfo->linked_console_id) {
                $linked = array_map('intval', explode(',', $b->GetConsoleInfo->linked_console_id));
                $checkConsoles = array_merge($checkConsoles, $linked);
            }
            $checkConsoles = array_unique($checkConsoles);

            $bookedSlots = Submissiondata::whereIn('console_id', $checkConsoles)
                ->where('date', $b->date)
                ->where('status', 1)
                ->get()
                ->map(function($slot) {
                    try {
                        $s = Carbon::parse($slot->start_time);
                        $e = Carbon::parse($slot->end_time);
                    } catch (\Exception $ex) {
                        return [];
                    }

                    $slots = [];
                    for ($t = $s->copy(); $t < $e; $t->addMinutes(30)) {
                        $slots[] = $t->format('H:i');
                    }
                    return $slots;
                })->flatten()->toArray();

            $opening = Carbon::parse('09:00');
            $closing = Carbon::parse('23:00');
            $allSlots = [];
            for ($time = $opening->copy(); $time->lt($closing); $time->addMinutes(30)) {
                $allSlots[$time->format('H:i')] = in_array($time->format('H:i'), $bookedSlots) ? 1 : 0;
            }

            $statusLabel = Carbon::parse($b->date.' '.$b->end_time)->gt($now) ? 'Upcoming' : 'Completed';

            $fandb_items = [];
            if (!empty($b->fandb_informations)) {

                //  Universal decode: works for JSON or serialized
                $fandbData = $b->fandb_informations;
                $decoded = null;
                if (is_string($fandbData)) {
                    $decoded = @unserialize($fandbData);
                    if ($decoded === false && json_decode($fandbData, true) !== null) {
                        $decoded = json_decode($fandbData, true);
                    }
                }

                if (is_array($decoded)) {
                    foreach ($decoded as $item) {
                        $foodId = $item['food_id'] ?? $item['id'] ?? null;
                        $quantity = $item['quantity'] ?? $item['qty'] ?? 1;
                        $name = $item['name'] ?? null;

                        if ($foodId) {
                            $food = FandBMaster::find($foodId);
                        } elseif (is_numeric($name)) {
                            $food = FandBMaster::find($name);
                        } elseif ($name) {
                            $food = FandBMaster::where('name', $name)->first();
                        } else {
                            $food = null;
                        }

                        if (!$food && is_array($item) && count($item) == 1) {
                            $idValue = reset($item);
                            if (is_numeric($idValue)) {
                                $food = FandBMaster::find($idValue);
                            }
                        }

                        if ($food) {
                            $fandb_items[] = [
                                'name' => $food->name,
                                'quantity' => $quantity,
                                'price' => $food->price,
                                'image' => $food->food_image ? asset($food->food_image) : null,
                            ];
                        } elseif ($name) {
                            $fandb_items[] = [
                                'name' => $name,
                                'quantity' => $quantity,
                                'price' => null,
                                'image' => null,
                            ];
                        }
                    }
                }
            }
            $fandb_total = 0;
foreach ($fandb_items as $fi) {
    $fandb_total += ($fi['price'] ?? 0) * ($fi['quantity'] ?? 1);
}
$final_total = $b->total_cost + $fandb_total;


            return [
                'id' => $b->id,
                'console_name' => $b->GetConsoleInfo->console_name ?? null,
                'console_icon' => $b->GetConsoleInfo && $b->GetConsoleInfo->console_icon
                    ? asset('public/' . $b->GetConsoleInfo->console_icon)
                    : null,
                'session_hours' => $b->GetHoursInfo->hours ?? null,
                'date' => Carbon::parse($b->date)->format('M j, Y'),
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'status_label' => $statusLabel,
                'total_cost' => $b->total_cost,
                'final_total'=>$final_total,
                'no_of_players' => (int) $b->no_of_players,
                'slots_status' => $allSlots,
                'fandb_items' => $fandb_items,
            ];
        };

        $upcoming = $bookings->filter(function($b) use ($now) {
            return Carbon::parse($b->date.' '.$b->end_time)->gt($now);
        })->map($mapBooking)->values();

        $past = $bookings->filter(function($b) use ($now) {
            return Carbon::parse($b->date.' '.$b->end_time)->lte($now);
        })->map($mapBooking)->values();

        return response()->json([
            'success' => true,
            'upcoming' => $upcoming,
            'past' => $past,
        ]);
    }

   
    public function show(Request $request, $id)
    {
         $placeId = $request->header('place-id');

    if (!$placeId) {
        return response()->json([
            'success' => false,
            'message' => 'place_id required'
        ], 422);
    }
        $mobileNo = $request->header('mobile_no') 
                    ?? (auth()->user()->mobile_no ?? null);

        if (!$mobileNo) {
            return response()->json([
                'success' => false,
                'message' => 'Mobile number not provided'
            ], 400);
        }

        $user = CreateAccountModel::where('mobile_no', $mobileNo)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $now = Carbon::now();

        $b = Submissiondata::with(['GetHoursInfo','GetConsoleInfo'])
            ->where('filter_mobile_no', $user->mobile_no)
             ->where('place_id', $placeId)
            ->where('id', $id)
            ->first();

        if (!$b) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        $checkConsoles = [$b->console_id];
        if ($b->GetConsoleInfo && $b->GetConsoleInfo->linked_console_id) {
            $linked = array_map('intval', explode(',', $b->GetConsoleInfo->linked_console_id));
            $checkConsoles = array_merge($checkConsoles, $linked);
        }
        $checkConsoles = array_unique($checkConsoles);

        $bookedSlots = Submissiondata::whereIn('console_id', $checkConsoles)
            ->where('date', $b->date)
            ->where('status', 1)
            ->get()
            ->map(function($slot) {
                $s = Carbon::parse($slot->start_time);
                $e = Carbon::parse($slot->end_time);
                $slots = [];
                for ($t = $s->copy(); $t < $e; $t->addMinutes(30)) {
                    $slots[] = $t->format('H:i');
                }
                return $slots;
            })->flatten()->toArray();

        $opening = Carbon::parse('09:00');
        $closing = Carbon::parse('23:00');
        $allSlots = [];
        for ($time = $opening->copy(); $time->lt($closing); $time->addMinutes(30)) {
            $allSlots[$time->format('H:i')] = in_array($time->format('H:i'), $bookedSlots) ? 1 : 0;
        }

        $statusLabel = Carbon::parse($b->date.' '.$b->end_time)->gt($now) ? 'Upcoming' : 'Completed';

        $fandb_items = [];
        if (!empty($b->fandb_informations)) {

            //  Universal decode: works for JSON or serialized
            $fandbData = $b->fandb_informations;
            $decoded = null;
            if (is_string($fandbData)) {
                $decoded = @unserialize($fandbData);
                if ($decoded === false && json_decode($fandbData, true) !== null) {
                    $decoded = json_decode($fandbData, true);
                }
            }

            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    $foodId = $item['food_id'] ?? $item['id'] ?? null;
                    $quantity = $item['quantity'] ?? $item['qty'] ?? 1;
                    $name = $item['name'] ?? null;

                    if ($foodId) {
                        $food = FandBMaster::find($foodId);
                    } elseif (is_numeric($name)) {
                        $food = FandBMaster::find($name);
                    } elseif ($name) {
                        $food = FandBMaster::where('name', $name)->first();
                    } else {
                        $food = null;
                    }

                    if (!$food && is_array($item) && count($item) == 1) {
                        $idValue = reset($item);
                        if (is_numeric($idValue)) {
                            $food = FandBMaster::find($idValue);
                        }
                    }

                    if ($food) {
                        $fandb_items[] = [
                            'name' => $food->name,
                            'quantity' => $quantity,
                            'price' => $food->price,
                            'image' => $food->food_image ? asset($food->food_image) : null,
                        ];
                    } elseif ($name) {
                        $fandb_items[] = [
                            'name' => $name,
                            'quantity' => $quantity,
                            'price' => null,
                            'image' => null,
                        ];
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $b->id,
                'console_name' => $b->GetConsoleInfo->console_name ?? null,
                'console_icon' => $b->GetConsoleInfo && $b->GetConsoleInfo->console_icon
                    ? asset('public/' . $b->GetConsoleInfo->console_icon)
                    : null,
                'session_hours' => $b->GetHoursInfo->hours ?? null,
                'date' => Carbon::parse($b->date)->format('M j, Y'),
                'start_time' => $b->start_time,
                'end_time' => $b->end_time,
                'status_label' => $statusLabel,
                'total_cost' => $b->total_cost,
                'no_of_players' => (int) $b->no_of_players,
                'slots_status' => $allSlots,
                'fandb_items' => $fandb_items,
            ]
        ]);
    }
}