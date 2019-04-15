<?php

namespace App\Domains\Conversion;

use App\Http\Controllers\Controller;

class ConversionController extends Controller
{
    public function convert(ConversionRequest $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $amount = $request->get('amount');

        $converter = app(Converter::class);
        $value = $converter->convert($from, $to, $amount);

        return response()->json(['total' => round($value, 4)]);
    }
}
