<?php

declare(strict_types=1);

namespace ExchangeRates;

use ExchangeRates\Currency\Currency;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'currency_commision_provider')]
class CurrencyCommissionProviderOther implements CurrencyCommissionProviderInterface
{
    public function supports(Currency $currency): bool
    {
        return $currency->getSymbol() !== 'USD' && $currency->getSymbol() !== 'EUR';
    }

    public function apply(Currency $currency): Currency
    {
        $currency->setSpreads(
            $currency->getRateToBaseCurrency()->getRate() + 0.15, 
            null
        );
        
        return $currency;
    }
}
