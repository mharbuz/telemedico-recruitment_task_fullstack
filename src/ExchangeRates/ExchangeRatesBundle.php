<?php

declare(strict_types=1);

namespace ExchangeRates;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ExchangeRatesBundle extends Bundle
{
    public function createContainerExtension(): ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new ExchangeRatesExtension();
        }
        return $this->extension;
    }
}
