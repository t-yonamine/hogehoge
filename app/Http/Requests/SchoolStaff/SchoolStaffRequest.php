<?php

namespace App\Http\Requests\SchoolStaff;

use App\Enums\Degree;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class SchoolStaffRequest extends FormRequest
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
            'password' => 'nullable|string|regex:/^[a-zA-Z0-9]+$/|min:6|max:20',
            'name' => 'required|string|max:128',
            'role' => 'required',
            'lic_expy_date' => 'nullable|date_format:Y-m-d',
            //input
            'qualification_lic_l_mvl' => 'required|in:0,1',
            'qualification_lic_m_mvl' => 'required|in:0,1',
            'qualification_lic_s_mvl' => 'required|in:0,1',
            'qualification_lic_sl_mvl' => 'required|in:0,1',
            'qualification_lic_l_ml' => 'required|in:0,1',
            'qualification_lic_s_ml' => 'required|in:0,1',
            'qualification_lic_sm_mvl' => 'required|in:0,1',
            'qualification_lic_towing' => 'required|in:0,1',
            'qualification_lic_l_mvl_2' => 'required|in:0,1',
            'qualification_lic_m_mvl_2' => 'required|in:0,1',
            'qualification_lic_s_mvl_2' => 'required|in:0,1',
            //end
            'lic_l_mvl' => 'required_if:qualification_lic_l_mvl,1',
            'lic_m_mvl' => 'required_if:qualification_lic_m_mvl,1',
            'lic_s_mvl' => 'required_if:qualification_lic_s_mvl,1',
            'lic_sl_mvl' => 'required_if:qualification_lic_sl_mvl,1',
            'lic_l_ml' => 'required_if:qualification_lic_l_ml,1',
            'lic_s_ml' => 'required_if:qualification_lic_s_ml,1',
            'lic_sm_mvl' => 'required_if:qualification_lic_sm_mvl,1',
            'lic_towing' => 'required_if:qualification_lic_towing,1',
            'lic_l_mvl_2' => 'required_if:qualification_lic_l_mvl_2,1',
            'lic_m_mvl_2' => 'required_if:qualification_lic_m_mvl_2,1',
            'lic_s_mvl_2' => 'required_if:qualification_lic_s_mvl_2,1',
            'is_revoked' => 'required|in:0,1',
            'is_beginner' => 'required|in:0,1',
            'is_senior' => 'required|in:0,1',
            'is_first_aid_1' => 'required|in:0,1',
            'is_first_aid_2' => 'required|in:0,1',
            'is_sim_4' => 'required|in:0,1',
            'is_sim_2' => 'required|in:0,1',
            'is_aptitude_1' => 'required|in:0,1',
            'is_aptitude_2' => 'required|in:0,1',
            'is_highway' => 'required|in:0,1',
            'is_road' => 'required|in:0,1',
            'is_wireless' => 'required|in:0,1',
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
            'password' => 'パスワード',
            'school_staff_no' => '職員番号',
            'name' => '教習所名',
            'role' => '役割',
            'lic_expy_date' => '免許有効期限',
            // input qualification
            'qualification_lic_l_mvl' => '大型教習資格',
            'qualification_lic_m_mvl' => '中型教習資格',
            'qualification_lic_s_mvl' => '普通教習資格',
            'qualification_lic_sl_mvl' => '大特教習資格',
            'qualification_lic_l_ml' => '大型2輪教習資格',
            'qualification_lic_s_ml' => '普通2輪教習資格',
            'qualification_lic_sm_mvl' => '準中型教習資格',
            'qualification_lic_towing' => '牽引教習資格',
            'qualification_lic_l_mvl_2' => '大型2種教習資格',
            'qualification_lic_m_mvl_2' => '中型2種教習資格',
            'qualification_lic_s_mvl_2' => '普通2種教習資格',
            // ----
            'lic_l_mvl' => '大型教習資格',
            'lic_m_mvl' => '中型教習資格',
            'lic_s_mvl' => '普通教習資格',
            'lic_sl_mvl' => '大特教習資格',
            'lic_l_ml' => '大型2輪教習資格',
            'lic_s_ml' => '普通2輪教習資格',
            'lic_sm_mvl' => '準中型教習資格',
            'lic_towing' => '牽引教習資格',
            'lic_l_mvl_2' => '大型2種教習資格',
            'lic_m_mvl_2' => '中型2種教習資格',
            'lic_s_mvl_2' => '普通2種教習資格',
            'is_revoked' => '取消処分者講習資格',
            'is_beginner' => '初心',
            'is_senior' => '高齢者講習資格',
            'is_first_aid_1' => '応急資格1種',
            'is_first_aid_2' => '応急資格2種',
            'is_sim_4' => '4輪シミュレータ',
            'is_sim_2' => '2輪シミュレータ',
            'is_aptitude_1' => '適性検査資格_1種',
            'is_aptitude_2' => '適性検査資格_2種',
            'is_highway' => '高速資格',
            'is_road' => '路上資格',
            'is_wireless' => '無線資格',
        ];
    }

    public function messages()
    {
        return [
            'lic_l_mvl.required_if' => '大型教習資格が資格ありの場合、大型教習資格も指定してください。',
            'lic_m_mvl.required_if' => '中型教習資格が資格ありの場合、中型教習資格も指定してください。',
            'lic_s_mvl.required_if' => '普通教習資格が資格ありの場合、普通教習資格も指定してください。',
            'lic_sl_mvl.required_if' => '大特教習資格が資格ありの場合、大特教習資格も指定してください。',
            'lic_l_ml.required_if' => '大型2輪教習資格が資格ありの場合、大型2輪教習資格も指定してください。',
            'lic_s_ml.required_if' => '普通2輪教習資格が資格ありの場合、普通2輪教習資格も指定してください。',
            'lic_sm_mvl.required_if' => '準中型教習資格が資格ありの場合、準中型教習資格も指定してください。',
            'lic_towing.required_if' => '牽引教習資格が資格ありの場合、牽引教習資格も指定してください。',
            'lic_l_mvl_2.required_if' => '大型2種教習資格が資格ありの場合、大型2種教習資格も指定してください。',
            'lic_m_mvl_2.required_if' => '中型2種教習資格が資格ありの場合、中型2種教習資格も指定してください。',
            'lic_s_mvl_2.required_if' => '普通2種教習資格が資格ありの場合、普通2種教習資格も指定してください。'
        ];
    }
}
