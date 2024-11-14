<?php

declare(strict_types=1);

namespace ExchangeRates\Currency;

class CurrencyRate
{
    private $currencyBase;
    private $currencyTarget;
    private $rate;

    public function __construct(CurrencySymbol $currencyBase, CurrencySymbol $currencyTarget, float $rate)
    {
        $this->currencyBase = $currencyBase;
        $this->currencyTarget = $currencyTarget;
        $this->rate = $rate;
    }

    public function getCurrencyBase(): CurrencySymbol
    {
        return $this->currencyBase;
    }

    public function getCurrencyTarget(): CurrencySymbol
    {
        return $this->currencyTarget;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function revert(): self
    {
        return new self($this->currencyTarget, $this->currencyBase, 1 / $this->rate);
    }
}
