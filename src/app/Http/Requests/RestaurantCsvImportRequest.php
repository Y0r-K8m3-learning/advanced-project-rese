<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantCsvImportRequest extends FormRequest
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
            'csvFile' => 'required|file|mimes:csv|max:2048',

        ];
    }

    public function messages()
    {
        return [

            'csvFile.required' => 'csvファイルを選択してください。',
            'csvFile.image' => 'csv形式の画像をアップロードしてください。',
            'csvFile.mimes' => 'csv形式の画像をアップロードしてください。',
            'csvFile.extensions' => 'csv形式の画像をアップロードしてください。',
            'csvFile.max' => 'csvファイルは2MB以下でアップロードしてください。',

        ];
    }
}
