<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Http\Requests\ReservationRequest;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;

class StripePaymentsController extends Controller
{
    private $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }


    public function index(ReservationRequest $request)
    {

        return view('paymentindex', [
            'number' => $request->number,
            'time' => $request->time,
            'date' =>
            $request->date,
            'restaurant_id' => $request->restaurant_id,
        ]);
    }

    public function payment(Request $request)
    {
        if (!Auth::check()) {
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
            // Payment Intents APIを使用（3DS対応）
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $request->total_price,
                'currency' => 'jpy',
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'return_url' => route('reservation.complete'),
            ]);

            if ($paymentIntent->status === 'requires_action') {
                // 3DS認証が必要な場合はセッションに保存してリダイレクト
                session(['payment_intent_id' => $paymentIntent->id]);
                session(['reservation_data' => $request->all()]);
                return redirect()->away($paymentIntent->next_action->redirect_to_url->url);
            } else if ($paymentIntent->status === 'succeeded') {
                // 決済成功 - 予約を作成
                $this->reservationService->createReservation($request);
                return redirect()->route('reservation.complete')->with('success', '決済が完了しました！');
            }
        } catch (\Stripe\Exception\CardException $e) {
            return redirect()->route('reservation.complete')->with('error', '決済に失敗しました：' . $e->getError()->message);
        } catch (\Exception $e) {
            return redirect()->route('reservation.complete')->with('error', '決済処理でエラーが発生しました：' . $e->getMessage());
        }
        
    }

    public function complete()
    {
        return view('reservation_complete');
    }
}
