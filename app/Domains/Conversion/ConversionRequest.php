<?php


namespace App\Domains\Conversion;

use Illuminate\Foundation\Http\FormRequest;

class ConversionRequest extends FormRequest
{

    public function rules()
    {
        return [
            'from' => 'required',
            'to' => 'required',
            'amount' => 'required',
        ];
    }
}
