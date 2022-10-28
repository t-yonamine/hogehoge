<?php

namespace App\Http\Requests\SchoolDriving;

use Illuminate\Foundation\Http\FormRequest;

class SchoolDrivingCreateRequest extends FormRequest
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
            'school_cd' => 'required|string|regex:/^[0-9]+$/|min:4|max:4',
            'name' => 'required|string|max:32',
            'name_kana' => 'required|string|max:64',
            'login_id' => 'required|string|regex:/^[a-zA-Z0-9]+$/|min:4|max:4',
            'password' => 'required|string|regex:/^[a-zA-Z0-9]+$/|min:6|max:8',
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
            'school_cd' => '教習所CD',
            'name' => '教習所名',
            'name_kana' => 'フリガナ',
            'login_id' => 'ログインID',
            'password' => 'パスワード',
            'school_staff_no' => '職員番号',
            'school_staff_name' => '氏名',
            'schools' => '教習生情報保存日数	',
        ];
    }
}
