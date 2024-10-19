<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function syncCurrencies(Country $country, array $currencies): void
    {
        foreach ($country->getCurrencies() as $existingCurrency) {
            $country->removeCurrency($existingCurrency);
        }

        foreach ($currencies as $code => $data) {
            $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => $code]);

            if (!$currency) {
                $currency = new Currency();
                $currency->setCode($code);
            }
            $currency->setName($data['name']);
            $currency->setSymbol($data['symbol']);

            $this->entityManager->persist($currency);

            $country->addCurrency($currency);
        }
    }

}
