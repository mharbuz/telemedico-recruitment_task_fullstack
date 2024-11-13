<?php

namespace ExchangeRates;

use ExchangeRates\Currency\Currency;

interface CurrencyCommissionProviderInterface
{
    public function supports(Currency $currency): bool;
    public function apply(Currency $currency): Currency;
}
