<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class StripePaymentsController extends Controller
{
    public function index()
    {
        return view('paymentindex');
    }

    public function payment(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('stripe.stripe_secret_key'));

        try {
            \Stripe\Charge::create([
                'source' => $request->stripeToken,
                'amount' => 1000,
                'currency' => 'jpy',
            ]);
        } catch (Exception $e) {
            return back()->with('flash_alert', '決済に失敗しました！(' . $e->getMessage() . ')');
        }
        return back()->with('status', '決済が完了しました！');
    }

    public function complete()
    {
        return view('complete');
    }
}
