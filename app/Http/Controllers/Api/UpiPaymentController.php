<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class UpiPaymentController extends Controller
{
    // 🔹 Create UPI transaction
    public function createPayment(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'user_id' => 'nullable|integer',
            'payment_method' => 'required|string', // 'upi'
            'transaction_ref' => 'required|string',
        ]);

        $transaction = Transaction::create([
            'user_id' => $data['user_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => 'inr',
            'status' => 'pending',
            'payment_type_id' => 2, // UPI
            'transaction_ref' => $data['transaction_ref'],
        ]);

        return response()->json([
            'success' => true,
            'transaction_ref' => $data['transaction_ref'],
            'transaction_id' => $transaction->id
        ]);
    }

    // 🔹 Update UPI payment status
    public function upiCallback(Request $request)
{
    $data = $request->validate([
            'transaction_ref' => 'required|string',
            'status' => 'required|string|in:success,failure,pending',
        
    ]);

    $tx = Transaction::where('transaction_ref', $data['transaction_ref'])->first();

    if (!$tx) {
        return response()->json([
            'success' => false,
            'message' => 'Transaction not found'
        ], 404);
    }

    $tx->update(['status' => $data['status']]);

    return response()->json([
        'success' => true,
        'transaction_id' => $tx->id,
        'status' => $tx->status
    ]);
}
}