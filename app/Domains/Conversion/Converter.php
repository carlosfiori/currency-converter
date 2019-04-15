<?php

namespace App\Domains\Conversion;

use App\Domains\Currency\CurrencyRepository;

class Converter
{
    const BASE_CURRENCY = 'BRL';

    const ALLOWED_CONVERSIONS = [
        'BRL',
        'USD',
        'EUR',
        'GBP',
        'ARS',
        'BTC',
    ];

    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function convert($from, $to, $amount): float
    {
        if ($from === $to) {
            return $amount;
        }

        $isUsingBaseCurrency = in_array(self::BASE_CURRENCY, [$to, $from]);
        if ($isUsingBaseCurrency) {
            return $this->convertUsingBaseCurrency($from, $to, $amount);
        }

        $fromBuyPrice = $this->currencyRepository->getCurrencyPrice($from);
        $toBuyPrice = $this->currencyRepository->getCurrencyPrice($to);

        return ($amount * $fromBuyPrice) / $toBuyPrice;
    }

    private function convertUsingBaseCurrency($from, $to, $amount): float
    {
        if ($from == self::BASE_CURRENCY) {
            return $amount / $this->currencyRepository->getCurrencyPrice($to);
        }
        return $amount * $this->currencyRepository->getCurrencyPrice($from);
    }
}
