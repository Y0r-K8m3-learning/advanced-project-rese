<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
{

    // 口コミの最大文字数
    const MAX_REVIEW_LENGTH = 400;
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
            'image' => 'image|mimes:jpeg,png|extensions:jpeg,png|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:' . self::MAX_REVIEW_LENGTH,

        ];
    }

    public function messages()
    {
        return [
            'restaurant_id.required' => '店舗情報が存在しません。ページを更新して再度お試しください。',
            'restaurant_id.integer' => '店舗情報が不正です。',
            'image.image' => 'jpeg,png形式の画像をアップロードしてください。',
            'image.mimes' => 'jpeg,png形式の画像をアップロードしてください。',
            'image.extensions' => 'jpeg,png形式の画像をアップロードしてください。',
            'image.max' => '画像サイズは2MB以下でアップロードしてください。',
            'rating.required' => '評価を入力してください。',
            'rating.integer' => '評価は整数で入力してください。',
            'rating.min' => '評価は1以上を指定してください。',
            'rating.max' => '評価は5以下を指定してください。',
            'comment.required' => '口コミを入力してください。',
            'comment.max' => '口コミは' . self::MAX_REVIEW_LENGTH . '文字以内で入力してください。',
        ];
    }
}
