<?php

declare(strict_types=1);

namespace App\Controller;

use ExchangeRates\ExchangeRatesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRatesController extends AbstractController
{
    protected $params;

    protected $exchangeRatesService;

    public function __construct(
        ContainerBagInterface $params,
        ExchangeRatesService $exchangeRatesService
    )
    {
        $this->params = $params;
        $this->exchangeRatesService = $exchangeRatesService;
    }

    public function index(Request $request): Response
    {
        $date = new \DateTimeImmutable($request->get('date', 'now'));

        try {
            $currencies = $this->exchangeRatesService->getAllCurrencyRates($date);
            echo "<pre>";
            //print_r($currencies);die;
        } catch (\InvalidArgumentException $e) {
            return JsonResponse::create(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return JsonResponse::create(['error' => $e->getMessage()], 500);
        }

        return JsonResponse::create([
            'currencies' => array_map(function ($currency) {
                return $currency->toArray();
            }, $currencies)
        ]);
    }
}
