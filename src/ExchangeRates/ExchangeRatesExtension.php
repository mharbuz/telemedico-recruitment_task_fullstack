<?php

declare(strict_types=1);

namespace ExchangeRates;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;


class ExchangeRatesExtension extends Extension
{
    public const ALIAS = 'exchange_rates';

    public const BASE_CURRENCY = 'PLN';

    public function getConfiguration(array $config, ContainerBuilder $container): ExchangeRatesConfiguration
    {
        return new ExchangeRatesConfiguration();
    }

    public function load(array $configs, ContainerBuilder $container): void
{
    $loader = new YamlFileLoader(
        $container,
        new FileLocator(__DIR__.'/Resources/config')
    );
    $loader->load('config.yaml');
}

    public static function getExtensionAlias(): string
    {
        return static::ALIAS;
    }
}