<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class StripeApiController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * Create PaymentIntent and return client_secret
     */
    public function createPaymentIntent(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string',
            'user_id' => 'nullable|integer',
            'payment_type_id' => 'nullable|integer', // optional
        ]);

        $currency = $data['currency'] ?? 'inr';
        $amountInMinor = intval(round($data['amount'] * 100)); // rupees -> paise
        $paymentTypeId = $data['payment_type_id'] ?? 1;

        try {
            // Create Stripe PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInMinor,
                'currency' => $currency,
                'payment_method_types' => ['card'],
            ]);

            // Save transaction in payments table
            $tx = Transaction::create([
                'user_id' => $data['user_id'] ?? null,
                'order_id' => Str::uuid(),
                'amount' => $data['amount'],
                'currency' => $currency,
                'status' => 'pending',
                'payment_intent_id' => $paymentIntent->id, // ✅ correct column
                'payment_type_id' => $paymentTypeId,
            ]);

            return response()->json([
                'success' => true,
                'clientSecret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'transaction_id' => $tx->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm PaymentIntent server-side (optional)
     */
    public function confirmPayment(Request $request)
    {
        $data = $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method_id' => 'required|string',
        ]);

        try {
            $pi = PaymentIntent::retrieve($data['payment_intent_id']);
            $pi->confirm(['payment_method' => $data['payment_method_id']]);

            // Update transaction status
            $tx = Transaction::where('payment_intent_id', $pi->id)->first();
            if ($tx) {
                $tx->update([
                    'payment_method_id' => $data['payment_method_id'],
                    'status' => $pi->status == 'succeeded' ? 'success' : $pi->status
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => $pi->status,
                'payment_intent' => $pi
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe confirm error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stripe webhook for payment events
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            if ($webhookSecret) {
                $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } else {
                $event = json_decode($payload);
            }

            if ($event->type === 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;
                $tx = Transaction::where('payment_intent_id', $paymentIntent->id)->first();
                if ($tx) {
                    $tx->update([
                        'status' => 'success',
                        'payment_method_id' => $paymentIntent->payment_method ?? null,
                    ]);
                }
            } elseif ($event->type === 'payment_intent.payment_failed') {
                $paymentIntent = $event->data->object;
                $tx = Transaction::where('payment_intent_id', $paymentIntent->id)->first();
                if ($tx) {
                    $tx->update(['status' => 'failed']);
                }
            }

            return response()->json(['received' => true]);

        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Webhook exception: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook error'], 500);
        }
    }
}
