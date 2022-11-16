<?php

namespace App\Http\Requests\Students;

use Illuminate\Foundation\Http\FormRequest;
use Rabianr\Validation\Japanese\Rules\Katakana;

class StudentRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // Block B
            'name' => 'required|string|max:128',
            'name_kana' => ['required', 'string','max:128', new Katakana()],
            'birth_date' => 'required|date_format:Y-m-d|max:10',
            'gender' => 'required|string|min:1|in:1,2',
            'zip_code' => 'nullable|string|regex:/^[0-9]+$/|max:7',
            'address' => 'string|max:200',
            'citizen_card_check_sw' => 'nullable|in:1,on',
            'license_check_sw' => 'nullable|in:1,on',
            'other_check_sw' => 'nullable|in:1,on',
            'other_check_text' => 'nullable|string|max:40',
            // Block C
            'admission_date' => 'required|date_format:Y-m-d|max:10',
            'lesson_limit' => 'nullable|date_format:Y-m-d|max:10',
            'moving_out_date' => 'nullable|date_format:Y-m-d|max:10',
            'moving_in_date' => 'nullable|date_format:Y-m-d|max:10',
            'discharge_date' => 'nullable|date_format:Y-m-d|max:10',

            // Block D
            'lic_issue_date' => 'nullable|date_format:Y-m-d|max:10',
            'lic_expy_date' => 'nullable|date_format:Y-m-d|max:10',
            'lic_num' => 'nullable|max:20',

            // Block E
            'lesson_cond_glasses' => 'nullable',
            'lesson_cond_contact_lens' => 'nullable',
            'lesson_cond_hearing_aid' => 'nullable',
            'first_aid_crse_exempt_sw' => 'nullable|in:0,1',
            'first_aid_crse_exempt_txt' => 'nullable|string|max:40',

            // Block F
            'eyesight_naked_left_1' => 'required|numeric|max:10',
            'eyesight_naked_right_1' => 'required|numeric|max:10',
            'eyesight_naked_both_1' => 'required|numeric|max:10',
            'eyesight_correct_left_1' => 'required|numeric|max:10',
            'eyesight_correct_right_1' => 'required|numeric|max:10',
            'eyesight_correct_both_1' => 'required|numeric|max:10',
            'field_of_view_left_1' => 'required|numeric|max:10',
            'field_of_view_right_1' => 'required|numeric|max:10',
            'field_of_view_both_1' => 'required|numeric|max:10',
            'confirm_date_1' => 'nullable|date_format:Y-m-d|max:10',

            'eyesight_naked_left_2' => 'required|numeric|max:10',
            'eyesight_naked_right_2' => 'required|numeric|max:10',
            'eyesight_naked_both_2' => 'required|numeric|max:10',
            'eyesight_correct_left_2' => 'required|numeric|max:10',
            'eyesight_correct_right_2' => 'required|numeric|max:10',
            'eyesight_correct_both_2' => 'required|numeric|max:10',
            'field_of_view_left_2' => 'required|numeric|max:10',
            'field_of_view_right_2' => 'required|numeric|max:10',
            'field_of_view_both_2' => 'required|numeric|max:10',
            'confirm_date_2' => 'nullable|date_format:Y-m-d|max:10',

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
            // Block B
            'name' => '氏名',
            'name_kana' => 'カナ',
            'birth_date' => '生年月日',
            'gender' => '性別',
            'zip_code' => '住所（郵便番号）',
            'address' => '住所（住所）',
            'citizen_card_check_sw' => '確認資料(住民票)',
            'license_check_sw' => '確認資料(免許証)',
            'other_check_sw' => '確認資料(その他)',
            'other_check_text' => '確認資料(メモ)',
            // Block C
            'admission_date' => '入所年月日',
            'lesson_limit' => '教習開始年月日',
            'moving_out_date' => '転出年月日',
            'moving_in_date' => '転入年月日',
            'discharge_date' => '退所年月日',
            'lic_issue_date' => '交付年月日',
            'lic_expy_date' => '有効期限',
            'lic_num' => '免許証番号',

            // Block F
            'eyesight_naked_left_1' => '修了検定視力裸眼(左)',
            'eyesight_naked_right_1' => '修了検定視力裸眼(右)',
            'eyesight_naked_both_1' => '修了検定視力裸眼(両)',
            'eyesight_correct_left_1' => '修了検定視力矯正(左)',
            'eyesight_correct_right_1' => '修了検定視力矯正(右)',
            'eyesight_correct_both_1' => '修了検定視力矯正(両)',
            'field_of_view_left_1' => '修了検定視野(左)',
            'field_of_view_right_1' => '修了検定視野(右)',
            'field_of_view_both_1' => '修了検定視野(両)',
            'confirm_date_1' => '検査日',

            'eyesight_naked_left_2' => '修了検定視力裸眼(左)',
            'eyesight_naked_right_2' => '修了検定視力裸眼(右)',
            'eyesight_naked_both_2' => '修了検定視力裸眼(両)',
            'eyesight_correct_left_2' => '修了検定視力矯正(左)',
            'eyesight_correct_right_2' => '修了検定視力矯正(右)',
            'eyesight_correct_both_2' => '修了検定視力矯正(両)',
            'field_of_view_left_2' => '修了検定視野(左)',
            'field_of_view_right_2' => '修了検定視野(右)',
            'field_of_view_both_2' => '修了検定視野(両)',
            'confirm_date_1' =>  '検査日',

        ];
    }
}
