<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Language;
use App\Entity\Currency;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use App\Repository\LanguageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountryController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    // ----------- //
    // -- RUTAS -- //
    // ----------- //

    #[Route('/countries', name: 'app_country')]
    public function index(CountryRepository $countryRepository): Response {

        $countries = $countryRepository->findAll();

        return $this->render('country/index.html.twig', [
            'countries' => $countries,
        ]);
    }

    #[Route('/countries/new', name: 'app_country_create')]
    public function new(Request $request, CountryRepository $countryRepository): Response {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            self::manageRegister($country, $request, $countryRepository);
        }

        return $this->render('country/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/countries/{id}', name: 'app_country_view')]
    public function show(Country $country): Response
    {
        return $this->render('country/show.html.twig', [
            'country' => $country,
        ]);
    }

    #[Route('/countries/{id}/edit', name: 'app_country_edit')]
    public function edit(Request $request, Country $country, CountryRepository $countryRepository): Response {
        $form = $this->createForm(CountryType::class, $country);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            self::manageRegister($country, $request, $countryRepository, true);
        }

        return $this->render('country/edit.html.twig', [
            'form' => $form->createView(),
            'country' => $country,
            'languagesOptions' => self::getLanguagesSelectOptions($country),
            'currenciesOptions' => self::getCurrenciesSelectOptions($country)
        ]);
    }

    #[Route('/countries/{id}/delete', name: 'app_country_delete', methods: ['POST'])]
    public function delete(Request $request, Country $country, CountryRepository $countryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getId(), $request->request->get('_token'))) {
            $countryRepository->remove($country);
        }

        return $this->redirectToRoute('app_country', [], Response::HTTP_SEE_OTHER);
    }


    // ------------------------ //
    // -- FUNCIONES INTERNAS -- //
    // ------------------------ //

    private function manageRegister(Country $country, Request $request, CountryRepository $countryRepository, $edit = false) {

        // Control de relaciones en idiomas
        $languagesIds = $request->get('languages', []);

        $languageRepository = $this->entityManager->getRepository(Language::class);
        
        if ( !$edit ) {
            foreach ($languagesIds as $languageId) {
                $language = $languageRepository->find($languageId);
                $country->addLanguage($language);
            }
        } else {
            $existingLanguages = $country->getLanguages()->toArray();

            foreach ($existingLanguages as $existingLanguage) {
                if (!in_array($existingLanguage->getId(), $languagesIds)) {
                    $country->removeLanguage($existingLanguage);
                }
            }

            foreach ($languagesIds as $languageId) {
                if (!$country->getLanguages()->contains($languageId)) {
                    $language = $languageRepository->find($languageId);
                    $country->addLanguage($language);
                }
            }
        }

        // Control de relaciones en monedas
        $currenciesIds = $request->get('currencies', []);

        $currencyRepository = $this->entityManager->getRepository(Currency::class);
        
        if ( !$edit ) {
            foreach ($currenciesIds as $currencyId) {
                $currency = $currencyRepository->find($currencyId);
                $country->addCurrency($language);
            }
        } else {
            $existingCurrencies = $country->getCurrencies()->toArray();

            foreach ($existingCurrencies as $existingCurrency) {
                if (!in_array($existingCurrency->getId(), $currenciesIds)) {
                    $country->removeCurrency($existingCurrency);
                }
            }

            foreach ($currenciesIds as $currencyId) {
                if (!$country->getCurrencies()->contains($currencyId)) {
                    $currency = $currencyRepository->find($currencyId);
                    $country->addCurrency($currency);
                }
            }
        }

        
        $countryRepository->save($country, true);
        $this->entityManager->flush();
        

        if ( $edit ) return $this->redirectToRoute('app_country_view', ['id' => $country->getId()], Response::HTTP_SEE_OTHER);
        else         return $this->redirectToRoute('app_country');
    }


    // ------------------- //
    // -- FUNCIONES API -- //
    // ------------------- //



    // ------------------------ //
    // -- FUNCIONES INTERNAS -- //
    // ------------------------ //

    private function getLanguagesSelectOptions(Country $country) {

        $relatedLanguages = $country->getLanguages();

        $languagesOptions = [];
        foreach ($relatedLanguages as $language) {
            $languagesOptions[] = [
                'id' => $language->getId(),
                'text' => "{$language->getName()} ({$language->getCode()})",
                'selected' => true
            ];
        }
    
        return $languagesOptions;
    }

    private function getCurrenciesSelectOptions(Country $country) {

        $relatedCurrencies = $country->getCurrencies();

        $currenciesOptions = [];
        foreach ($relatedCurrencies as $currency) {
            $currenciesOptions[] = [
                'id' => $currency->getId(),
                'text' => "{$currency->getName()} ({$currency->getSymbol()})",
                'selected' => true
            ];
        }
    
        return $currenciesOptions;
    }


}