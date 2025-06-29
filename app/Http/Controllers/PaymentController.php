<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:stripe,paypal',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        
        // Ensure user can pay for this order
        if ($order->buyer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($validated['payment_method'] === 'stripe') {
            return $this->createStripePayment($order);
        }

        // Add PayPal integration here
        return $this->createPayPalPayment($order);
    }

    private function createStripePayment(Order $order)
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $order->amount * 100, // Convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'order_id' => $order->id,
                    'buyer_id' => $order->buyer_id,
                    'seller_id' => $order->seller_id,
                ],
            ]);

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'stripe',
                'amount' => $order->amount,
                'currency' => 'USD',
                'status' => Payment::STATUS_PENDING,
                'payment_intent_id' => $paymentIntent->id,
                'metadata' => [
                    'stripe_payment_intent' => $paymentIntent->id,
                ],
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function success(Request $request, Payment $payment)
    {
        // Verify payment with Stripe
        if ($payment->payment_method === 'stripe') {
            $paymentIntent = PaymentIntent::retrieve($payment->payment_intent_id);
            
            if ($paymentIntent->status === 'succeeded') {
                $payment->update([
                    'status' => Payment::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);

                // Update order status
                $payment->order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'status' => Order::STATUS_IN_PROGRESS,
                ]);

                // Send notifications
                $payment->order->seller->notify(new \App\Notifications\PaymentReceived($payment));
                
                return redirect()->route('orders.show', $payment->order)
                    ->with('success', 'Payment successful! Your order is now in progress.');
            }
        }

        return redirect()->route('orders.show', $payment->order)
            ->with('error', 'Payment verification failed.');
    }

    public function cancel(Payment $payment)
    {
        $payment->update(['status' => Payment::STATUS_CANCELLED]);
        
        return redirect()->route('orders.show', $payment->order)
            ->with('error', 'Payment was cancelled.');
    }
}
