<?php

declare(strict_types=1);

namespace ExchangeRates;


use ExchangeRates\Currency\Currency;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;


class ExchangeRatesService
{
    const BASE_CURRENCY = 'PLN';
    protected $params;
    protected $ratesRepository;

    public function __construct(
        ContainerBagInterface $params,
        RatesRepository $ratesRepository
    )
    {
        $this->params = $params;
        $this->ratesRepository = $ratesRepository;
    }

    public function getAllCurrencyRates(\DateTimeImmutable $date): array
    {
        $currencies = $this->getConfigCurrencies();

        
        $rates = $this->ratesRepository->load($currencies, $date);

        $rates = $this->restrictCurrencies($rates);
        
        $currencies = $this->buildCurrenciesFromRates($rates);

        return $currencies;
    }

    protected function getConfigCurrencies(): array
    {
        $config = ['currencies' => ['USD', 'EUR', 'GBP', 'CHF', 'JPY']];//$this->params->get('exchange_rates');

        return $config['currencies'];
    }


    protected function getRates(array $currencies): array
    {
        $rates = [];
        foreach ($currencies as $currency) {
            $rates[$currency] = 11;
        }

        return $rates;
    }

    protected function buildCurrenciesFromRates(array $rates): array
    {
            echo "<pre>";
            //print_r($rates);die;
        $currencies = [];
        foreach ($rates as $rate) {
            $currency = new Currency($rate->getCurrencyBase());
            $currency->addRate($rate);

            $currencies[] = $currency;
        }

        return $currencies;
    }

    protected function restrictCurrencies(array $rates): array
    {
        $currencies = $this->getConfigCurrencies();

        return array_filter($rates, function ($rate) use ($currencies) {
            return in_array($rate->getCurrencyBase(), $currencies);
        });
    }
}
