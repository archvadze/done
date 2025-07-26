<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show payment form
     */
    public function show()
    {
        return view('payments.show');
    }

    /**
     * Create Stripe checkout session (or mock payment)
     */
    public function createCheckout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'currency' => 'required|string|in:USD,EUR,GEL',
            'type' => 'required|string|in:donation,subscription',
        ]);

        $user = Auth::user();
        $amount = $request->amount;
        $currency = $request->currency;

        // Check if we're in test mode (no real Stripe keys)
        $stripeSecretKey = config('services.stripe.secret_key');
        
        if (empty($stripeSecretKey) || str_starts_with($stripeSecretKey, 'sk_test_example')) {
            // Mock payment for development
            return $this->createMockPayment($user, $amount, $currency, $request->type);
        }

        // Real Stripe integration
        return $this->createStripeCheckout($user, $amount, $currency, $request->type);
    }

    /**
     * Create mock payment for testing
     */
    private function createMockPayment(User $user, float $amount, string $currency, string $type)
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => $currency,
            'provider' => 'stripe',
            'status' => 'completed', // Simulate successful payment
            'payment_id' => 'mock_' . uniqid(),
            'session_id' => 'mock_session_' . uniqid(),
            'metadata' => [
                'type' => $type,
                'mock' => true,
                'test_mode' => true,
            ],
        ]);

        // Update user balance
        $user->increment('balance', $amount);

        return redirect()->route('payments.success', ['payment' => $payment->id])
            ->with('success', "Mock payment of {$amount} {$currency} completed successfully!");
    }

    /**
     * Create real Stripe checkout session
     */
    private function createStripeCheckout(User $user, float $amount, string $currency, string $type)
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($currency),
                        'product_data' => [
                            'name' => ucfirst($type) . ' - Acumen Craft',
                        ],
                        'unit_amount' => $amount * 100, // Stripe uses cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payments.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payments.show'),
                'metadata' => [
                    'user_id' => $user->id,
                    'type' => $type,
                ],
            ]);

            // Store payment record
            Payment::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => $currency,
                'provider' => 'stripe',
                'status' => 'pending',
                'session_id' => $session->id,
                'metadata' => [
                    'type' => $type,
                    'stripe_session_id' => $session->id,
                ],
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $paymentId = $request->get('payment');

        if ($paymentId) {
            // Mock payment success
            $payment = Payment::findOrFail($paymentId);
            return view('payments.success', compact('payment'));
        }

        if ($sessionId) {
            // Real Stripe payment success
            $payment = Payment::where('session_id', $sessionId)->firstOrFail();
            
            // Verify with Stripe and update payment status
            Stripe::setApiKey(config('services.stripe.secret_key'));
            $session = Session::retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                $payment->update([
                    'status' => 'completed',
                    'payment_id' => $session->payment_intent,
                ]);
                
                // Update user balance
                $payment->user->increment('balance', $payment->amount);
            }

            return view('payments.success', compact('payment'));
        }

        return redirect()->route('payments.show')->with('error', 'Payment session not found.');
    }

    /**
     * Payment history
     */
    public function history()
    {
        $payments = Auth::user()->payments()->latest()->paginate(10);
        return view('payments.history', compact('payments'));
    }

    /**
     * Stripe webhook handler
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            return response('Webhook signature verification failed.', 400);
        }

        // Handle the event
        switch ($event['type']) {
            case 'checkout.session.completed':
                $session = $event['data']['object'];
                $this->handleCheckoutCompleted($session);
                break;
            case 'payment_intent.succeeded':
                $paymentIntent = $event['data']['object'];
                $this->handlePaymentSucceeded($paymentIntent);
                break;
            default:
                // Unhandled event type
        }

        return response('', 200);
    }

    private function handleCheckoutCompleted($session)
    {
        $payment = Payment::where('session_id', $session['id'])->first();
        if ($payment) {
            $payment->update([
                'status' => 'completed',
                'payment_id' => $session['payment_intent'],
            ]);
            
            // Update user balance
            $payment->user->increment('balance', $payment->amount);
        }
    }

    private function handlePaymentSucceeded($paymentIntent)
    {
        // Additional payment verification logic if needed
    }
}
