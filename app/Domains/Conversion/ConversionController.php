<?php

namespace App\Domains\Conversion;

use App\Domains\Currency\ApiLimitExceedException;
use App\Http\Controllers\Controller;

class ConversionController extends Controller
{
    public function convert(ConversionRequest $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $amount = $request->get('amount');

        /** @var Converter $converter */
        $converter = app(Converter::class);
        try {
            $value = $converter->convert($from, $to, $amount);
        } catch (ApiLimitExceedException $e) {
            $now = now();
            $tomorrow = today()->addDay();
            $secondsUntilTomorrow = $tomorrow->timestamp - $now->timestamp;

            return response()
                ->json(
                    ['error' => $e->getMessage()],
                    $e->getCode(),
                    ["Retry-After" => $secondsUntilTomorrow]
                );
        }

        return response()->json(['total' => round($value, 4)]);
    }
}
