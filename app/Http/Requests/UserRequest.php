<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        if ($this->method() == 'PUT' || $this->method() == 'PATCH') {
            $email = '|unique:users,email,' . $this->user;
            $phone = '|unique:users,phone,' . $this->user;
            $password = 'nullable|';
        } else {
            $email = '|unique:users,email';
            $phone = '|unique:users,phone';
            $password = 'required|';
        }

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255' . $email,
            'password' => $password . 'string|min:8|confirmed',
            'password_confirmation' => $password . 'string|min:8',
            'phone' => 'required|string|regex:/^\+90 \(\d{3}\) \d{3} \d{2} \d{2}$/' . $phone,
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
            'name.required' => 'Ad ve soyad alanı zorunludur.',
            'name.string' => 'Ad ve soyad alanı metin olmalıdır.',
            'name.max' => 'Ad ve soyad alanı en fazla 255 karakter olabilir.',
            'email.required' => 'E-posta alanı zorunludur.',
            'email.string' => 'E-posta alanı metin olmalıdır.',
            'email.email' => 'E-posta alanı geçerli bir e-posta adresi olmalıdır.',
            'email.max' => 'E-posta alanı en fazla 255 karakter olabilir.',
            'email.unique' => 'Bu e-posta adresi daha önce kullanılmış, lütfen başka bir e-posta adresi deneyiniz.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.string' => 'Şifre alanı metin olmalıdır.',
            'password.min' => 'Şifre alanı en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Şifre alanı ile Şifre tekrar alanı eşleşmiyor.',
            'phone.required' => 'Telefon alanı zorunludur.',
            'phone.string' => 'Telefon alanı metin olmalıdır.',
            'phone.regex' => 'Telefon alanı geçerli bir telefon numarası olmalıdır. Örnek: +90 (555) 555 55 55',
            'phone.unique' => 'Bu telefon numarası daha önce kullanılmış, lütfen başka bir telefon numarası deneyiniz.',
        ];
    }
}
