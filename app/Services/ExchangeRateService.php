<?php

namespace App\Services;

use Exception;

class ExchangeRateService
{
    public function getRate(): float
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://open.er-api.com/v6/latest/USD',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (! $err) {
                $data = json_decode($response, true);
                if (isset($data['rates']['EUR'])) {
                    return (float) $data['rates']['EUR'];
                }
            }
        } catch (Exception $e) {

        }

        return (float) config('appfront.products.exchange_rate');
    }
}
