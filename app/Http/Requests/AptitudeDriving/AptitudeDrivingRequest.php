<?php

namespace App\Http\Requests\AptitudeDriving;

use App\Enums\ResultCharacter;
use App\Enums\ResultNumber;
use App\Enums\Seq;
use App\Enums\TestType;
use Illuminate\Foundation\Http\FormRequest;
use BenSampo\Enum\Rules\EnumValue;

class AptitudeDrivingRequest extends FormRequest
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
        $resultAToC = ResultCharacter::A . ',' . ResultCharacter::B . ',' . ResultCharacter::C;
        $resultAToE = ResultCharacter::A . ',' . ResultCharacter::B . ',' . ResultCharacter::C . ',' . ResultCharacter::D . ',' . ResultCharacter::E;
        return [
            'seq' => 'required|int|' . new EnumValue(Seq::class, false),
            'test_type' => 'required|string|in:' . TestType::OD,
            'test_date' => 'required|date_format:Y-m-d',
            'od_persty_pattern_1' => 'required|regex:/^[a-zA-Z0-9]+$/|string|max:3',
            'od_persty_pattern_2' => 'required|regex:/^[a-zA-Z0-9]+$/|string|max:3',
            'od_drv_aptitude' => 'required|string|' . new EnumValue(ResultNumber::class, false),
            'od_safe_aptitude' => 'required|string|in:' . $resultAToE,
            'od_specific_rxn' => 'required|string|in:' . ResultNumber::ONE . ',' . ResultNumber::TWO . ',' . ResultNumber::THREE,
            'od_a' => 'required|string|in:' . $resultAToE,
            'od_b' => 'required|string|in:' . $resultAToE,
            'od_c' => 'required|string|in:' . $resultAToE,
            'od_d' => 'required|string|' . new EnumValue(ResultCharacter::class, false),
            'od_e' => 'required|string|in:' . $resultAToE,
            'od_f' => 'required|string|in:' . $resultAToE,
            'od_g' => 'required|string|in:' . $resultAToE,
            'od_h' => 'required|string|in:' . $resultAToC,
            'od_i' => 'required|string|in:' . $resultAToC,
            'od_j' => 'required|string|in:' . $resultAToC,
            'od_k' => 'required|string|in:' . $resultAToC,
            'od_l' => 'required|string|in:' . $resultAToC,
            'od_m' => 'required|string|in:' . $resultAToC,
            'od_n' => 'required|string|in:' . $resultAToC,
            'od_o' => 'required|string|in:' . $resultAToC,
            'od_p' => 'required|string|in:' . $resultAToC,
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
            'seq' => '回数',
            'test_type' => '検査方式',
            'test_date' => '実施日',
            'od_persty_pattern_1' => '性格パターン１',
            'od_persty_pattern_2' => '性格パターン2',
            'od_drv_aptitude' => '運転適性度',
            'od_safe_aptitude' => '安全運転度',
            'od_specific_rxn' => '特異反応',
            'od_a' => 'A.注意力',
            'od_b' => 'B.判断力',
            'od_c' => 'C.柔軟性',
            'od_d' => 'D.決断力',
            'od_e' => 'E.緻密性',
            'od_f' => 'F.動作の安定性',
            'od_g' => 'G.適応性',
            'od_h' => 'H.身体的健康度',
            'od_i' => 'I.精神的健康度',
            'od_j' => 'J.社会的成熟度',
            'od_k' => 'K.情緒不安定',
            'od_l' => 'L.衝迫性・暴発性',
            'od_m' => 'M.自己中心性',
            'od_n' => 'N.神経質・過敏性',
            'od_o' => 'O.虚飾性',
            'od_p' => 'P.運転マナー',
        ];
    }
}
