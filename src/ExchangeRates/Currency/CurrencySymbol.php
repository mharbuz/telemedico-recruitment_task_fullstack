<?php

declare(strict_types=1);

namespace ExchangeRates\Currency;

class CurrencySymbol
{
    private $symbol;

    public function __construct(string $symbol)
    {
        $this->symbol = $symbol;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function __toString(): string
    {
        return $this->symbol;
    }
}
