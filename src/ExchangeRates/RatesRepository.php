<?php

declare(strict_types=1);

namespace ExchangeRates;

interface RatesRepository
{
    public function load(array $currencySymbols, \DateTimeImmutable $date): array;
}
