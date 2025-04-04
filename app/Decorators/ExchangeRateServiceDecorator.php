<?php

namespace App\Decorators;

use App\Services\ExchangeRateService;

/**
 * The purpose of this class is to put a cache layer between the actual
 * service and the calling controller without touching their code.
 */
class ExchangeRateServiceDecorator
{
    public function __construct(private ExchangeRateService $exchangeRateService, private int $ttl)
    {
        //
    }

    public function getRate(): float
    {
        if (cache()->has('exchange_rate')) {
            return (float) cache()->get('exchange_rate');
        }

        $exchangeRate = $this->exchangeRateService->getRate();

        cache()->put('exchange_rate', $exchangeRate, $this->ttl);

        return $exchangeRate;
    }
}
