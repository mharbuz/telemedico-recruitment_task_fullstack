<?php

declare(strict_types=1);

namespace ExchangeRates;

use ExchangeRates\Currency\Currency;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ExchangeRatesConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(ExchangeRatesExtension::getExtensionAlias());

        $this->addCurrenciesConfig($treeBuilder);

        return $treeBuilder;
    }

    /**
     * @param TreeBuilder $treeBuilder
     * @return void
     */
    private function addCurrenciesConfig(TreeBuilder $treeBuilder): void
    {
        $treeBuilder->getRootNode()
            ->children()
                ->enumNode('currencies')
                    ->values(['USD', 'EUR', 'CZK', 'IDR', 'BRL'])
                ->end()
            ->end();

    }

    public function getCurrencies(): array
    {
        return [new Currency('USD'), new Currency('EUR'), new Currency('CZK'), new Currency('IDR'), new Currency('BRL')];
    }
}
