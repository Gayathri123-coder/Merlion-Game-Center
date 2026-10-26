<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Razorpay\Api\Api;
use Exception;
use App\Models\Payments;
use App\Submissiondata;
use App\PriceMaster;
use App\FandBMaster;
use Carbon\Carbon; // ✅ Added Carbon import
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private $razorpay;

    public function __construct()
    {
        $mode = config('services.razorpay.mode');
        $key = config("services.razorpay.$mode.key");
        $secret = config("services.razorpay.$mode.secret");
        $this->razorpay = new Api($key, $secret);
    }

    public function createOrder(Request $request)
    {
        $placeId = $request->header('place-id');

if (!$placeId) {
    return response()->json([
        'status' => false,
        'message' => 'place-id header required'
    ], 422);
}

        try {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'booking_data' => 'required|array',
            ]);

            $amount = $request->amount;
            $data = $request->booking_data;
            $mode = config('services.razorpay.mode');

            // 🔹 Create Razorpay order
            $order = $this->razorpay->order->create([
                'receipt' => 'order_rcptid_' . time(),
                'amount' => $amount * 100,
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            $paymentStatus = $mode === 'test' ? 'success' : 'pending';

            // 🔹 Save payment info
            $payment = Payments::create([
                'user_id'  => auth()->id(),
                'order_id' => $order['id'],
                'amount'   => $amount,
                'currency' => 'INR',
                'status'   => $paymentStatus,
                'response_data' => json_encode($data),
            ]);

            $bookingId = null;

            // 🔹 Detect logged-in or provided mobile number
            $data['filter_mobile_no'] =
                auth()->check() && isset(auth()->user()->mobile_no)
                    ? auth()->user()->mobile_no
                    : ($data['filter_mobile_no']
                        ?? $data['mobile_no']
                        ?? $request->input('mobile_no')
                        ?? null);

            // 🔹 Get user fullname
            $loggedInName = 'Guest';
            if (!empty($data['filter_mobile_no'])) {
                $user = \DB::table('create_account')
                    ->where('mobile_no', $data['filter_mobile_no'])
                    ->first();

                $loggedInName = $user ? $user->fullname : 'Guest';
            }

            // 🔹 Player Information
            $totalPlayers = isset($data['players']) ? (int)$data['players'] : 1;
            $incomingPlayers = $data['player_informations'] ?? [];
            $players = [];

            $players[] = [
                'player' => 'Player - 1',
                'name' => $loggedInName,
                'mobile_no' => $data['filter_mobile_no']
            ];

            for ($i = 2; $i <= $totalPlayers; $i++) {
                if (isset($incomingPlayers[$i - 1])) {
                    $players[] = [
                        'player' => "Player - $i",
                        'name' => $incomingPlayers[$i - 1]['name'] ?? null,
                        'mobile_no' => $incomingPlayers[$i - 1]['mobile_no'] ?? null,
                    ];
                } else {
                    $players[] = [
                        'player' => "Player - $i",
                        'name' => null,
                        'mobile_no' => null,
                    ];
                }
            }

            // 🔹 F&B info
            $data['fandb_informations'] = $data['fandb_informations'] ?? [];
            $fandb_cost = 0;

            if (!empty($data['fandb_informations']) && is_array($data['fandb_informations'])) {
                foreach ($data['fandb_informations'] as $item) {
                    $foodId = $item['food_id'] ?? $item['id'] ?? null;
                    $quantity = $item['quantity'] ?? 1;

                    if ($foodId) {
                        $food = FandBMaster::find($foodId);
                        if ($food) {
                            $fandb_cost += $food->price * $quantity;
                        }
                    }
                }
            }

           // ✅ Calculate F&B cost already done above
$data['fandb_cost'] = $fandb_cost;

// ✅ Player info stays serialized (same as web)
$data['player_informations'] = serialize($players);

// ✅ Convert mobile JSON format F&B → web serialized format
$convertedFandB = [];
if (!empty($data['fandb_informations']) && is_array($data['fandb_informations'])) {
    foreach ($data['fandb_informations'] as $item) {
        $convertedFandB[] = [
            'name'  => (string)($item['food_id'] ?? $item['name'] ?? ''),
            'qty'   => (string)($item['quantity'] ?? 1),
            'price' => (string)($item['price'] ?? 0),
        ];
    }
}

// ✅ Serialize it (exact same format as web SubmissionSave)
$data['fandb_informations'] = serialize($convertedFandB);

// ✅ Other info
$data['filter_name'] = $loggedInName;
$data['duration'] = $data['duration'] ?? '01:00';

            // 🔹 Price recalculation
           $get_price = PriceMaster::where('place_id', $placeId)
    ->where('console_id', $data['console_id'])
    ->where('hours_id', $data['hours_id'])
    ->where('no_of_players', $data['players'])
    ->first();

      $recalculated_price = $get_price ? $get_price->price : 0;
//  Separate game and food costs, apply discount if present
$data['total_cost'] = $recalculated_price;  
$data['fandb_cost'] = $fandb_cost;          

$discount = isset($data['discount']) ? floatval($data['discount']) : 0.00;

// If discount is a percentage, convert it — (optional if you use %)
if ($discount > 0 && $discount < 1) {
    $discount = $discount * 100; // ensure 0.1 -> 10%
}

// Calculate final cost
$discountAmount = ($discount / 100) * $data['total_cost'];
$data['final_cost'] = ($data['total_cost'] - $discountAmount) + $data['fandb_cost'];


        // ✅ Safely convert times (handles 9:00, 9:00 AM etc.)
try {
    $start_time_24 = Carbon::parse($data['start_time'])->format('H:i:s');
    $end_time_24   = Carbon::parse($data['end_time'])->format('H:i:s');
} catch (\Exception $e) {
    return response()->json(['status' => false, 'message' => 'Invalid time format.'], 422);
}

$date_db = date('Y-m-d', strtotime($data['date']));
$exists = Submissiondata::where('console_id', $data['console_id'])
    ->when(!empty($data['submission_id']), function ($q) use ($data) {
        $q->where('id', '!=', $data['submission_id']); // exclude same record if editing
    })
    ->whereDate('date', $date_db)
    ->where(function ($query) use ($start_time_24, $end_time_24) {
        $query->where(function ($q) use ($start_time_24, $end_time_24) {
            $q->whereTime('start_time', '<', $end_time_24)
              ->whereTime('end_time', '>', $start_time_24);
        });
    })
    ->where(function ($q) use ($start_time_24, $end_time_24) {
        // ✅ Allow back-to-back bookings
        $q->whereTime('end_time', '!=', $start_time_24)
          ->whereTime('start_time', '!=', $end_time_24);
    })
    ->where('status', 1)
    ->exists();


            // 🔹 Create or update booking
            if (in_array($mode, ['test', 'live'])) {

                $booking = !empty($data['submission_id'])
                    ? Submissiondata::find($data['submission_id'])
                    : null;

                if (!$booking && isset($data['filter_mobile_no'], $data['date'], $data['start_time'])) {
                    $booking = Submissiondata::where('filter_mobile_no', $data['filter_mobile_no'])
                        ->where('date', $data['date'])
                        ->where('start_time', $data['start_time'])
                        ->first();
                }

                if ($booking) {
                    // Update
                    $booking->update([
                          'place_id'           => $placeId,
                        'console_id'          => $data['console_id'],
                        'hours_id'            => $data['hours_id'],
                        'date'                => $data['date'],
                        'start_time'          => $data['start_time'],
                        'end_time'            => $data['end_time'],
                        'no_of_players'       => $data['players'],
                        'player_informations' => $data['player_informations'],
                        'fandb_informations'  => $data['fandb_informations'],
                        'fandb_cost'          => $data['fandb_cost'],
                        'total_hours'         => $data['duration'],
                        'total_cost'          => $data['total_cost'],
                        'final_cost'          => $data['final_cost'],
                        'payment_type'        => $data['payment_type'] ?? 2,
                        'filter_mobile_no'    => $data['filter_mobile_no'],
                        'filter_name'         => $data['filter_name'],
                        'reference_id'        => $data['reference_id'] ?? null,
                        'status'              => 1,
                        'updated_by'          => auth()->id(),
                        'log_from'            => 'Mobile',
                        'updated_at'          => now(),
                    ]);
                    $bookingId = $booking->id;
                } else {
                    // Insert new
                    $bookingId = Submissiondata::insertGetId([
                         'place_id'           => $placeId,
                        'console_id'          => $data['console_id'],
                        'hours_id'            => $data['hours_id'],
                        'date'                => $data['date'],
                        'start_time'          => $data['start_time'],
                        'end_time'            => $data['end_time'],
                        'no_of_players'       => $data['players'],
                        'player_informations' => $data['player_informations'],
                        'fandb_informations'  => $data['fandb_informations'],
                        'fandb_cost'          => $data['fandb_cost'],
                        'total_hours'         => $data['duration'],
                        'total_cost'          => $data['total_cost'],
                        'final_cost'          => $data['final_cost'],
                        'payment_type'        => $data['payment_type'] ?? 2,
                        'filter_mobile_no'    => $data['filter_mobile_no'],
                        'filter_name'         => $data['filter_name'],
                        'reference_id'        => $data['reference_id'] ?? null,
                        'status'              => 1,
                        'created_by'          => auth()->id(),
                        'updated_by'          => auth()->id(),
                        'log_from'            => 'Mobile',
                        'created_at'          => now(),
                        'updated_at'          => now(),
                    ]);
                }
            }

            // ✅ Response
            return response()->json([
                'status' => true,
                'message' => $mode === 'test'
                    ? ($bookingId ? 'Booking updated successfully (Test Mode).' : 'Booking created successfully (Test Mode).')
                    : 'Order created successfully. Awaiting payment confirmation.',
              'data' => [
    'order_id'   => $order['id'],
    'booking_id' => $bookingId,
    'amount'     => $data['total_cost'],
    'currency'   => 'INR',
    'key'        => $this->getRazorpayKey(),
    'mode'       => $mode,
    'payment_status' => $paymentStatus,
    'game_cost'  => $recalculated_price,
    'fandb_cost' => $fandb_cost,
    'final_cost' => $data['final_cost'],
],

            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getRazorpayKey()
    {
        $mode = config('services.razorpay.mode');
        return config("services.razorpay.$mode.key");
    }
}