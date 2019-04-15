<?php

namespace App\Domains\Conversion;

use App\Domains\Currency\ApiLimitExceedException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

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
            return $this->buildErrorResponse($e);
        }

        return response()->json(['total' => round($value, 4)]);
    }

    /**
     * @param $exception
     *
     * @return JsonResponse
     */
    private function buildErrorResponse($exception): JsonResponse
    {
        return response()
            ->json(
                ['error' => $exception->getMessage()],
                $exception->getCode(),
                ["Retry-After" => $this->getSecondsUntilTomorrow()]
            );
    }

    /**
     * @return int
     */
    private function getSecondsUntilTomorrow(): int
    {
        $now = now();
        $tomorrow = today()->addDay();
        return $tomorrow->timestamp - $now->timestamp;
    }
}
