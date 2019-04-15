<?php

namespace App\Domains\Currency;

interface CurrencyRepository
{

    public function getCurrencyPrice(string $to): float;
}
