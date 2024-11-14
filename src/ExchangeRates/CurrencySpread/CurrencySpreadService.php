<?php

declare(strict_types=1);

namespace ExchangeRates\CurrencySpread;


use ExchangeRates\Currency\Currency;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;


class CurrencySpreadService
{
    protected $providers;

    public function __construct(
        #[TaggedIterator('currency_commision_provider')] 
        iterable $providers
    )
    {
        $this->providers = $providers;
    }

    public function __invoke(Currency $currency): Currency
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($currency)) {
                $currency = $provider->apply($currency);
            }
        }

        return $currency;
    }
}
