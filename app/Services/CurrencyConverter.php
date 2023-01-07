<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyConverter
{
    private $apiKey; // configration in .env file

    protected $baseUrl = 'https://free.currconv.com/api/v7';

    public function __construct(string $apiKey) // لعدم تثبيت المفتاح ممكن يكون متغير
    {
        $this->apiKey = $apiKey;
    }

    public function convert(string $from, string $to, float $amount = 1): float
    {
        $q = "{$from}_{$to}";
        $response = Http::baseUrl($this->baseUrl) // HttpClient Protocol
            ->get('/convert', [
                'q' => $q,
                'compact' => 'y', // response result is simple
                'apiKey' => $this->apiKey,
            ]);

        $result = $response->json();

        return $result[$q]['val'] * $amount;
    }
}
