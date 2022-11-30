<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSearchRequest extends FormRequest
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
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1',
            'search' => 'string',
            'sort' => 'in:id,name,email,phone,created_at,updated_at',
            'order' => 'in:asc,desc',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            'page.integer' => 'Sayfa numarası sayısal olmalıdır!',
            'page.min' => 'Sayfa numarası 1\'den küçük olamaz!',
            'per_page.integer' => 'Sayfa başına kayıt sayısı sayısal olmalıdır!',
            'per_page.min' => 'Sayfa başına kayıt sayısı 1\'den küçük olamaz!',
            'search.string' => 'Arama metni metinsel olmalıdır!',
            'sort.string' => 'Sıralama alanı metinsel olmalıdır!',
            'order.in' => 'Sıralama yöntemi "asc" veya "desc" olmalıdır!',
        ];
    }
}
