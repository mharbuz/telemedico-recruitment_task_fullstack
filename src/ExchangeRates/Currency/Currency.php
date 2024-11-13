<?php

declare(strict_types=1);

namespace ExchangeRates\Currency;

use ExchangeRates\ExchangeRatesExtension;

class Currency
{
    private $symbol;

    private $rates = [];

    private $sellPrice = null;

    private $buyPrice = null;

    public function __construct(CurrencySymbol $symbol, array $rates = [])
    {
        $this->symbol = $symbol;
        foreach ($rates as $rate) {
            $this->addRate($rate);
        }
    }

    public function getSymbol(): string
    {
        return $this->symbol->getSymbol();
    }

    public function __toString(): string
    {
        return $this->symbol->__toString();
    }

    public function setRateToBaseCurrency(CurrencyRate $rate): void
    {
        if ($rate->getCurrencyBase() !== $this->symbol) {
            throw new \InvalidArgumentException('Invalid rate for currency ' . $this->symbol);
        }

        if ($rate->getCurrencyTarget() !== new CurrencySymbol(ExchangeRatesExtension::BASE_CURRENCY)) {
            throw new \InvalidArgumentException('Invalid rate target currency for currency ' . $this->symbol);
        }

        $this->rates[] = $rate;
    }

    public function addRate(CurrencyRate $rate): void
    {
        if ($rate->getCurrencyBase() !== $this->symbol) {
            throw new \InvalidArgumentException('Invalid rate for currency ' . $this->symbol);
        }

        $this->rates[$rate->getCurrencyTarget()->getSymbol()] = $rate;
    }

    public function getRate(CurrencySymbol $rateTo): array
    {
        $rate = array_filter($this->rates, function (CurrencyRate $rate) use ($rateTo) {
            return $rate->getCurrencyTarget() === $rateTo;
        });

        if (empty($rate)) {
            //TODO: throw custom exception
            throw new \InvalidArgumentException('Rate not found from ' . $this->symbol . ' to ' . $rateTo);
        }

        return $this->rates;
    }

    public function getRateToBaseCurrency(): CurrencyRate
    {
        $rate = array_filter($this->rates, function (CurrencyRate $rate) {
            return $rate->getCurrencyTarget()->getSymbol() === ExchangeRatesExtension::BASE_CURRENCY;
        });

        if (count($rate) == 0) {
            throw new \InvalidArgumentException('Rate to base currency not found for ' . $this->symbol);
        }

        return array_pop($rate);
    }

    public function setSpreads(?float $sellPrice, ?float $buyPrice): void
    {
        $this->sellPrice = $sellPrice;
        $this->buyPrice = $buyPrice;
    }

    public function toArray(): array
    {
        return [
            'symbol' => $this->symbol->getSymbol(),
            'sellPrice' => $this->sellPrice,
            'buyPrice' => $this->buyPrice,
            'mediumRate' => $this->getRateToBaseCurrency()->getRate(),
        ];
    }
}
