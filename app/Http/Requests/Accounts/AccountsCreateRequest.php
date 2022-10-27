<?php

namespace App\Http\Requests\Accounts;

use Illuminate\Foundation\Http\FormRequest;

class AccountsCreateRequest extends FormRequest
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
            'login_id' => 'required|regex:/^[a-zA-Z0-9]+$/|string|min:4|max:4',
            'password' => 'required|string|regex:/^[a-zA-Z0-9]+$/|max:8|min:6',
            'staff_no' => 'required|string|regex:/^[a-zA-Z0-9]+$/|max:10',
            'name' => 'required|string|max:128',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'login_id' => 'ログインID',
            'password' => 'パスワード',
            'staff_no' => '担当者番号',
            'name' => '氏名',
        ];
    }
}
