<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'image' => 'required|image|mimes:jpeg,png|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:400',
        ];
    }

    public function messages()
    {
        return [
            'restaurant_id.required' => '店舗情報が存在しません。ページを更新して再度お試しください。',
            'restaurant_id.integer' => '店舗情報が不正です。',
            'image.required' => '画像をアップロードしてください',
            'image.image' => '画像をアップロードしてください。',
            'image.mimes' => 'jpeg,png形式の画像をアップロードしてください。',
            'rating.required' => '評価を入力してください。',
            'rating.integer' => '評価は整数で入力してください。',
            'rating.min' => '評価は1以上を指定してください。',
            'rating.max' => '評価は5以下を指定してください。',
            'comment.required' => 'コメントを入力してください。',
            'comment.string' => 'コメントは文字列で入力してください。',
            'comment.max' => 'コメントは400文字以内で入力してください。',
        ];
    }
}
