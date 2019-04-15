<?php


namespace App\Domains\Conversion;

use Illuminate\Foundation\Http\FormRequest;

class ConversionRequest extends FormRequest
{

    public function rules()
    {
        $allowedCurrencies = $this->getAllowedCurrencies();
        $toFromRules = "required|in:$allowedCurrencies";
        return [
            'from' => $toFromRules,
            'to' => $toFromRules,
            'amount' => 'required',
        ];
    }

    private function getAllowedCurrencies()
    {
        return implode(',', Converter::ALLOWED_CONVERSIONS);
    }

    public function messages()
    {
        $allowedCurrencies = $this->getAllowedCurrencies();
        $message = "The selected :attribute must be one of them: $allowedCurrencies";
        return [
            'from.in' => $message,
            'to.in' => $message,
        ];
    }
}
