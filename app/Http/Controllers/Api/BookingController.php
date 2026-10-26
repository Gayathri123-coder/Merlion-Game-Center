<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Submissiondata;
use App\Models\CreateAccountModel;
use App\PriceMaster;
use App\HoursMaster;
use Carbon\Carbon;

class BookingController extends Controller
{
   public function myBookings(Request $request)
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

    // Find user
    $user = CreateAccountModel::where('mobile_no', $mobileNo)->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found'
        ], 404);
    }

    $now = Carbon::now();

    // Fetch all bookings, no date filter
    $bookings = Submissiondata::with(['GetHoursInfo', 'GetConsoleInfo'])
        ->where('filter_mobile_no', $user->mobile_no) 
        ->orderBy('date', 'desc')
        ->orderBy('start_time', 'desc')
        ->get();

    // Map bookings
    $result = $bookings->map(function($b) use ($now) {
        $statusLabel = Carbon::parse($b->date.' '.$b->end_time)->gt($now) ? 'Upcoming' : 'Completed';
        return [
            'id'           => $b->id,
            'console_name' => $b->GetConsoleInfo->console_name ?? null,
            'console_icon' => $b->GetConsoleInfo && $b->GetConsoleInfo->console_icon
                            ? asset('public/'.$b->GetConsoleInfo->console_icon)
                            : null,
            'date'         => Carbon::parse($b->date)->format('M j, Y'),
            'start_time'   => $b->start_time,
            'end_time'     => $b->end_time,
            'hours'        => $b->total_hours,
            'status_label' => $statusLabel,
            'status'       => $b->status,
        ];
    });

    return response()->json([
        'success' => true,
        'data'    => $result
    ]);
}


   public function bookingSummary(Request $request, $id)

    {
     
        $booking = Submissiondata::with(['GetConsoleInfo','GetHoursInfo'])
            ->where('id', $id)
             
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        $totalHours = $booking->total_hours ?? '00:30'; // fallback default

        // Try to fetch matching hours row
        $hoursRow = HoursMaster::whereRaw("TIME_FORMAT(hours, '%H:%i') = ?", [$totalHours])
            ->first();

        // Fallback to first active hours row if not found
        if (!$hoursRow) {
            $hoursRow = HoursMaster::where('status', 1)->first();
        }

        // Ensure $hoursRow is valid
        if (!$hoursRow) {
            return response()->json([
                'success' => false,
                'message' => 'No valid hours found for this booking.'
            ], 422);
        }

        $totalHours = $hoursRow->hours; // update totalHours from row
        $hoursId    = $hoursRow->id;

        // Fetch price row safely
        $priceRow = PriceMaster::where('console_id', $booking->console_id)
            ->where('hours_id', $hoursId)
            ->where('no_of_players', $booking->no_of_players)
            ->first();

        $totalPrice = $priceRow->price ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'booking_id'     => $booking->id,
                'console_name'   => $booking->GetConsoleInfo->console_name ?? null,
                'console_icon'   => $booking->GetConsoleInfo && $booking->GetConsoleInfo->console_icon
                                     ? asset('public/'.$booking->GetConsoleInfo->console_icon)
                                     : null,
                'date'           => Carbon::parse($booking->date)->format('D, M d, Y'),
                'time_slot'      => $booking->start_time . ' - ' . $booking->end_time,
                'duration'       => $totalHours,
                'players'        => $booking->no_of_players . ' player' . ($booking->no_of_players > 1 ? 's' : ''),
                'price_breakdown'=> [
                    'label' => ($booking->GetConsoleInfo->console_name ?? '') . ' ' . $totalHours,
                    'calc'  => $booking->no_of_players . ' player(s)',
                    'amount'=> number_format($totalPrice, 2)
                ],
                'total_amount'   => number_format($totalPrice, 2),
            ]
        ]);
    }

}