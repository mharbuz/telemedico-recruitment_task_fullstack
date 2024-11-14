<?php

declare(strict_types=1);

namespace Unit;

use ExchangeRates\Currency\Currency;
use ExchangeRates\Currency\CurrencyRate;
use ExchangeRates\Currency\CurrencySymbol;
use ExchangeRates\CurrencySpread\CurrencyCommissionProviderEurUsd;
use ExchangeRates\CurrencySpread\CurrencyCommissionProviderOther;
use ExchangeRates\CurrencySpread\CurrencySpreadService;
use PHPUnit\Framework\TestCase;

class CurrencySpreadServiceTest extends TestCase
{
    public function testUsdSpread()
    {
        $providers = [
            new CurrencyCommissionProviderOther(), 
            new CurrencyCommissionProviderEurUsd()
        ];
        $service = new CurrencySpreadService($providers);


        $currency = new Currency(
            new CurrencySymbol('USD'), 
            [
            new CurrencyRate(
                new CurrencySymbol('USD'),
                new CurrencySymbol('PLN'), 
                4.55)
        ]);

        $result = $service($currency);

        $this->assertEquals(4.62, $result->getSellPrice());
        $this->assertEquals(4.50, $result->getBuyPrice());
    }

    public function testJpySpread()
    {
        $providers = [
            new CurrencyCommissionProviderOther(), 
            new CurrencyCommissionProviderEurUsd()
        ];
        $service = new CurrencySpreadService($providers);

        $currency = new Currency(
            new CurrencySymbol('JPY'), 
            [
            new CurrencyRate(
                new CurrencySymbol('JPY'),
                new CurrencySymbol('PLN'), 
                0.035)
        ]);

        $result = $service($currency);

        $this->assertEquals(0.185, $result->getSellPrice());
        $this->assertEquals(null, $result->getBuyPrice());
    }
}