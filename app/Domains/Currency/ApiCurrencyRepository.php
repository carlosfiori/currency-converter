<?php

namespace App\Domains\Currency;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ApiCurrencyRepository implements CurrencyRepository
{
    private $currencies;

    public function getCurrencyPrice(string $currency): float
    {
        if (is_null($this->currencies)) {
            $this->getApiData();
        }

        $buy = $this->currencies->$currency->buy;
        return $buy;
    }

    private function getApiData()
    {
        $seconds = 300;
        $this->currencies = Cache::remember('api-currency-data', $seconds, function () {
            $client = new Client();
            $response = $client->get('https://api.hgbrasil.com/finance');
            $json = json_decode($response->getBody());
            return $json->results->currencies;
        });
    }
}
