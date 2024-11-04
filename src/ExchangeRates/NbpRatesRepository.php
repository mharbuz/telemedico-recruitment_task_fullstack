<?php

declare(strict_types=1);

namespace ExchangeRates;

use ExchangeRates\Currency\CurrencyRate;
use ExchangeRates\Currency\CurrencySymbol;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NbpRatesRepository implements RatesRepository
{
    const BASE_CURRENCY = 'PLN';
    protected $httpClient;
    protected $cache;

    public function __construct(
        HttpClientInterface $client,
        CacheInterface $cache
    )
    {
        $this->httpClient = $client;
        $this->cache = $cache;
    }
    public function load(array $currencySymbols, \DateTimeImmutable $date): array
    {
        $rates = $this->fetchRates($date);
        
        /*$this->cache->get('nbp_rates', function ($item) {
            //TODO: zmienić czas cache na 24h albo na czas do 12:00 kiedy to NBP publikuje nowe kursy
            $item->expiresAfter(1);
            return $this->fetchRates();
        });*/

        return $rates;
    }

    protected function fetchRates(\DateTimeImmutable $date): array
    {
        $tabA = $this->fetchRate('A', $date);
        $tabB = $this->fetchRate('B', $date);
        $tabC = $this->fetchRate('C', $date);

        $allCurrencies = array_merge($tabA[0]['rates'] ?? [], $tabB[0]['rates'] ?? [], $tabC[0]['rates'] ?? []);

        $allCurrencies = $this->restrictCurrenciesWithMidRate($allCurrencies);

        return $this->parseRates($allCurrencies);
    }   

    protected function fetchRate(string $table, \DateTimeImmutable $date): array
    {
        $response = $this->httpClient->request(
            'GET', 
            'http://api.nbp.pl/api/exchangerates/tables/' . $table . "/" . $date->format('Y-m-d'), 
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );
        
        if ($response->getStatusCode() !== 200) {
            return [];
        }

        return json_decode($response->getContent(), true);
    }

    protected function parseRates(array $rates): array
    {
        $parsedRates = [];
        foreach ($rates as $baseRate) {
            $currencyBase = new CurrencySymbol($baseRate['code']);
            $currencyTarget = new CurrencySymbol(ExchangeRatesExtension::BASE_CURRENCY);
            $rate = (float) $baseRate['mid'];
            $parsedRates[] = new CurrencyRate(
                $currencyBase,
                $currencyTarget,
                $rate
            );
        }

        return $parsedRates;
    }

    private function restrictCurrenciesWithMidRate(array $rates): array
    {
        return array_filter($rates, function ($rate) {
            return isset($rate['mid']);
        });
    }
}
