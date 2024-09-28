<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use App\Http\Requests\ReservationRequest;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;

class StripePaymentsController extends Controller
{

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }


    public function index(ReservationRequest $request)
    {

        return view('paymentindex', [
            'number' => $request->number,
            'time' => $request->time,
            'date' => $request->date,
        ]);
    }

    public function payment(Request $request)
    {

        if (!Auth::check()) {
            // ログインしていない場合、現在のURLをセッションに保存
            session(['redirect_url' => url()->current()]);
            return redirect()->route('login');
        }
        // 重複チェック
        $existingReservation = Reservation::where('restaurant_id', $request->input('restaurant_id'))
            ->where('reservation_date', $request->input('date'))
            ->where('reservation_time', $request->input('time'))
            ->first();

        if ($existingReservation) {
            return redirect()->back()->withErrors(['error' => 'この時間帯にはすでに予約が入っています。別の時間を選択してください。'])->withInput();
        }


        \Stripe\Stripe::setApiKey(config('stripe.stripe_secret_key'));

        try {
            // \Stripe\Charge::create([
            //     'source' => $request->stripeToken,
            //     'amount' => 1000,
            //     'currency' => 'jpy',
            // ]);

            // サービスクラスを使って予約を登録
            $this->reservationService->createReservation($request);
        } catch (Exception $e) {
            return back()->with('flash_alert', '決済に失敗しました！(' . $e->getMessage() . ')');
        }
        return view('reservation_complete')->with('status', '決済が完了しました！');

        //return back()->with('status', '決済が完了しました！');
    }

    public function complete()
    {
        return view('complete');
    }
}
