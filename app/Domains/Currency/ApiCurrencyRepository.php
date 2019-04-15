<?php

namespace App\Domains\Currency;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class ApiCurrencyRepository implements CurrencyRepository
{
    private $currencies;

    public function getCurrencyPrice(string $currency): float
    {
        $currency = strtoupper($currency);
        if (is_null($this->currencies)) {
            $this->updateData();
        }

        return $this->currencies->$currency->buy;
    }

    private function updateData()
    {
        $seconds = 300;
        $this->currencies = Cache::remember('api-currency-data', $seconds, function () {
            return $this->getApiData();
        });
    }

    private function getApiData()
    {
        try {
            $apiKey = config('currency.hgbrasil_api_key');
            $client = new Client();
            $response = $client->get("https://api.hgbrasil.com/finance?key=$apiKey");
        } catch (ClientException $e) {
            if ($e->getCode() === Response::HTTP_FORBIDDEN) {
                throw new ApiLimitExceedException("Daily limit exceeded", Response::HTTP_SERVICE_UNAVAILABLE);
            }

            throw $e;
        }

        $json = json_decode($response->getBody());
        return $json->results->currencies;
    }
}
