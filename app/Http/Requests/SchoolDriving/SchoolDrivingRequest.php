<?php

namespace App\Http\Requests\SchoolDriving;

use Illuminate\Foundation\Http\FormRequest;

class SchoolDrivingRequest extends FormRequest
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
            'id' => 'required|int',
            'school_cd' => 'required|string|regex:/^[a-zA-Z0-9]+$/|min:4|max:4',
            'name' => 'required|string|max:32',
            'name_kana' => 'required|string|max:64',
            'user_id' =>  'required|int',
            'login_id' =>  'required|string',
            'password' => 'nullable|string|regex:/^[a-zA-Z0-9]+$/|min:6|max:8',
            'school_staff_no' => 'required|string|regex:/^[a-zA-Z0-9]+$/|max:10',
            'school_staff_name' => 'required|string|max:128',
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
            'id' => '教習所ID',
            'school_cd' => '教習所CD',
            'name' => '教習所名',
            'name_kana' => '教習所名フリガナ',
            'user_id' => 'ユーザーID',
            'login_id' => 'ログインID',
            'password' => 'パスワード',
            'school_staff_no' => '職員番号',
            'school_staff_name' => '氏名',
        ];
    }
}
