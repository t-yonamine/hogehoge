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
            'seq' => '??????',
            'test_type' => '????????????',
            'test_date' => '?????????',
            'od_persty_pattern_1' => '?????????????????????',
            'od_persty_pattern_2' => '??????????????????2',
            'od_drv_aptitude' => '???????????????',
            'od_safe_aptitude' => '???????????????',
            'od_specific_rxn' => '????????????',
            'od_a' => 'A.?????????',
            'od_b' => 'B.?????????',
            'od_c' => 'C.?????????',
            'od_d' => 'D.?????????',
            'od_e' => 'E.?????????',
            'od_f' => 'F.??????????????????',
            'od_g' => 'G.?????????',
            'od_h' => 'H.??????????????????',
            'od_i' => 'I.??????????????????',
            'od_j' => 'J.??????????????????',
            'od_k' => 'K.???????????????',
            'od_l' => 'L.?????????????????????',
            'od_m' => 'M.???????????????',
            'od_n' => 'N.?????????????????????',
            'od_o' => 'O.?????????',
            'od_p' => 'P.???????????????',
        ];
    }
}
