<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'image.required' => 'Lütfen bir resim seçiniz.',
            'image.mimes' => 'Lütfen sadece jpeg, png, jpg, gif, svg uzantılı bir resim dosyası seçiniz.',
            'image.max' => 'Lütfen 2MB\'den küçük bir resim dosyası seçiniz.',
        ];
    }
}
