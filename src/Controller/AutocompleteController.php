<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LanguageRepository;
use App\Repository\CurrencyRepository;

#[Route('/autocomplete')]
class AutocompleteController extends AbstractController
{

    #[Route('/languages', name: 'autocomplete_all_languages')]
    public function allLanguages(Request $request, LanguageRepository $languageRepository): Response {
        $term = $request->query->get('term', '');

        $queryBuilder = $languageRepository->createQueryBuilder('l');

        if (!empty($term)) {
            $queryBuilder->andWhere('l.name LIKE :term')
                         ->setParameter('term', '%' . $term . '%');
        }

        $queryBuilder->orderBy('l.name', 'ASC');

        $languages = $queryBuilder->getQuery()->getResult();

        $languageOptions = array_map(function($language) {
            return [
                'id' => $language->getId(),
                'text' => "{$language->getName()} ({$language->getCode()})"
            ];
        }, $languages);

        return $this->json($languageOptions);
    }


    #[Route('/currencies', name: 'autocomplete_all_currencies')]
    public function allCurrencies(Request $request, CurrencyRepository $currencyRepository): Response {
        $term = $request->query->get('term', '');

        $queryBuilder = $currencyRepository->createQueryBuilder('c');

        if (!empty($term)) {
            $queryBuilder->andWhere('c.name LIKE :term')
                         ->setParameter('term', '%' . $term . '%');
        }

        $queryBuilder->orderBy('c.name', 'ASC');

        $currencies = $queryBuilder->getQuery()->getResult();

        $currencyOptions = array_map(function($currency) {
            return [
                'id' => $currency->getId(),
                'text' => "{$currency->getName()} ({$currency->getSymbol()})"
            ];
        }, $currencies);

        return $this->json($currencyOptions);
    }

}
