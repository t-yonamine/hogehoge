<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportCsvRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'files' => 'required|file|mimes:csv,txt'
        ];
    }

    public function messages()
    {
        return [
            'files' =>  __('messages.MSE00006'),
        ];
    }
}
