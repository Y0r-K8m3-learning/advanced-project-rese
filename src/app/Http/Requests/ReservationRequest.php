<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        return [
            'date' => 'required|date|after_or_equal:today',
            'time' => ['required', function ($attribute, $value, $fail) {
                $reservationDate = request('date');
                $currentDate = now()->toDateString();
                $currentTime = now()->format('H:i');

                // 予約日が今日で、かつ時刻が現在時刻より前の場合はエラー
                if ($reservationDate === $currentDate && $value < $currentTime) {
                    $fail('予約時間は現在時刻より後である必要があります。');
                }
            }],
            'number' => 'required|integer|min:1|max:10',
        ];
    }
}
