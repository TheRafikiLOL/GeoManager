<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LanguageRepository;
use App\Repository\CurrencyRepository;

#[Route('/autocomplete')]
class AutocompleteController extends AbstractController
{

    #[Route('/languages', name: 'autocomplete_all_languages')]
    public function allLanguages(LanguageRepository $languageRepository): Response {
        
        $languages = $languageRepository->findBy([], ['name' => 'ASC']);

        $languageOptions = array_map(function($language) {
            return [
                'id' => $language->getId(),
                'text' => "{$language->getName()} ({$language->getCode()})"
            ];
        }, $languages);

        return $this->json($languageOptions);

    }


    #[Route('/currencies', name: 'autocomplete_all_currencies')]
    public function allCurrencies(CurrencyRepository $currencyRepository): Response {
        
        $currencies = $currencyRepository->findBy([], ['name' => 'ASC']);

        $currencyOptions = array_map(function($currency) {
            return [
                'id' => $currency->getId(),
                'text' => "{$currency->getName()} ({$currency->getSymbol()})"
            ];
        }, $currencies);

        return $this->json($currencyOptions);

    }

}
