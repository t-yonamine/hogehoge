<?php

namespace App\Http\Requests\EffectMeasurements;

use App\Enums\LaType;
use App\Enums\ResultType;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class EffectMeasurementRequest extends FormRequest
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
            'period_date' => 'required|date_format:Y-m-d',
            'period_from' => 'required|date_format:H:i',
            'question_num' => 'required|regex:/^[0-9]+$/|max:3',
            'score' => 'required|regex:/^[0-9]+$/|max:3',
            'result' =>  'required|'. new EnumValue(ResultType::class, false),
            'remarks' => 'required|string|max:100',
            'la_type' => 'required|in:' . LaType::EFF_MEAS_1N()->value . ',' . LaType::EFF_MEAS_2N()->value,
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
            'period_date' => '実施日付',
            'period_from' => '実施時間',
            'question_num' => '問題番号',
            'score' => '点数',
            'result' => '合否',
            'remarks' => 'メモ'
        ];
    }
}
