<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function getRate(): float
    {
        $response = $this->fetchExchangeRate();

        if ($response->failed()) {
            return $this->getDefaultRate();
        }

        $rate = (float) $response->json('rates.EUR');

        return $rate ?: $this->getDefaultRate();
    }

    private function getDefaultRate(): float
    {
        return (float) config('appfront.products.exchange_rate');
    }

    private function fetchExchangeRate($currency = 'USD', $timeout = 5): Response
    {
        return Http::timeout($timeout)->get('https://open.er-api.com/v6/latest/'.$currency);
    }
}
