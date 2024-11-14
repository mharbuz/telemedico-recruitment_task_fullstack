<?php

declare(strict_types=1);

namespace ExchangeRates\CurrencySpread;

use ExchangeRates\Currency\Currency;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'currency_commision_provider')]
class CurrencyCommissionProviderEurUsd implements CurrencyCommissionProviderInterface
{
    public function supports(Currency $currency): bool
    {
        return $currency->getSymbol() === 'USD' || 
            $currency->getSymbol() === 'EUR';
    }

    public function apply(Currency $currency): Currency
    {
        $currency->setSpreads(
            round($currency->getRateToBaseCurrency()->getRate() + 0.07, 5), 
            round($currency->getRateToBaseCurrency()->getRate() - 0.05, 5)
        );
        
        return $currency;
    }
}
