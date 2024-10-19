<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\Language;
use App\Entity\Currency;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use App\Repository\LanguageRepository;
use App\Controller\GlobalController;
use App\Controller\CurrencyController;
use App\Controller\LanguageController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/countries')]
class CountryController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LanguageController $languageController;
    private CurrencyController $currencyController;

    public function __construct(
        EntityManagerInterface $entityManager,
        LanguageController $languageController,
        CurrencyController $currencyController
    ) {
        $this->entityManager = $entityManager;
        $this->languageController = $languageController;
        $this->currencyController = $currencyController;
    }

    // ----------- //
    // -- RUTAS -- //
    // ----------- //

    #[Route('', name: 'app_country')]
    public function index(CountryRepository $countryRepository): Response {

        $countries = $countryRepository->findAll();

        return $this->render('country/index.html.twig', [ ]);
    }

    #[Route('/new', name: 'app_country_create')]
    public function new(Request $request, CountryRepository $countryRepository): Response {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            self::manageRegister($country, $request, $countryRepository);
        }

        return $this->render('country/new.html.twig', [
            'form' => $form->createView(),
            'languagesOptions' => '',
            'currenciesOptions' => ''
        ]);
    }

    #[Route('/{id}', name: 'app_country_view')]
    public function show(Country $country): Response
    {
        return $this->render('country/show.html.twig', [
            'country' => $country,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_country_edit')]
    public function edit(Request $request, Country $country, CountryRepository $countryRepository): Response {

        $oldFilePath  = $country->getFlag();

        $form = $this->createForm(CountryType::class, $country);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            self::manageRegister($country, $request, $countryRepository, true, $oldFilePath);
        }

        return $this->render('country/edit.html.twig', [
            'form' => $form->createView(),
            'country' => $country,
            'languagesOptions' => self::getLanguagesSelectOptions($country),
            'currenciesOptions' => self::getCurrenciesSelectOptions($country)
        ]);
    }

    #[Route('/{id}/delete', name: 'app_country_delete', methods: ['DELETE'])]
    public function delete(Request $request, Country $country, CountryRepository $countryRepository): JsonResponse {
        if ($this->isCsrfTokenValid('delete' . $country->getId(), $request->request->get('_token'))) {
            $countryRepository->remove($country);
            
            return $this->json(['success' => true, 'message' => 'El país ha sido eliminado.']);
        }

        return $this->json(['success' => false, 'message' => 'CSRF token inválido.'], JsonResponse::HTTP_FORBIDDEN);
    }
    


    // ------------------- //
    // -- FUNCIONES API -- //
    // ------------------- //

    #[Route('/api/all', name: 'api_country_sync_all')]
    public function apiContrySyncAll(CountryRepository $countryRepository, EntityManagerInterface $entityManager): Response {
        try {
            $responseData = GlobalController::callToAPI('all');

            if ( !empty($responseData) ) {

                foreach ( $responseData as $countryData ) {

                    $data = [
                        'code'       => $countryData['cca2'],
                        'name'       => $countryData['name']['common'],
                        'fullname'   => array_key_exists('name', $countryData) && array_key_exists('official', $countryData['name']) ? $countryData['name']['official'] : '',
                        'region'     => array_key_exists('region', $countryData) ? $countryData['region'] : '',
                        'subregion'  => array_key_exists('subregion', $countryData) ? $countryData['subregion'] : '',
                        'area'       => array_key_exists('area', $countryData) ? $countryData['area'] : null,
                        'population' => array_key_exists('population', $countryData) ? $countryData['population'] : 0,
                        'flag'       => array_key_exists('flags', $countryData) && array_key_exists('png', $countryData['flags']) ? $countryData['flags']['png'] : '',
                        'capital'    => array_key_exists('capital', $countryData) && is_array($countryData['capital']) && count($countryData['capital']) > 0 ? $countryData['capital'][0] : ''
                    ];

                    $country = $countryRepository->findOneBy(['code' => $data['code']]);

                    if ( empty($country) ) {
                        // Se importa uno nuevo
                        $country = new Country();
                        $country->setCode($data['code']);
                        $country->setName($data['name']);
                        $country->setFullname($data['fullname']);
                        $country->setRegion($data['region']);
                        $country->setSubregion($data['subregion']);
                        $country->setArea($data['area']);
                        $country->setPopulation($data['population']);
                        $country->setFlag($data['flag']);
                        $country->setCapital($data['capital']);

                        $entityManager->persist($country);
                    } else {
                        // Se actualizan los datos
                        $country->setCode($data['code']);
                        $country->setName($data['name']);
                        $country->setFullname($data['fullname']);
                        $country->setRegion($data['region']);
                        $country->setSubregion($data['subregion']);
                        $country->setArea($data['area']);
                        $country->setPopulation($data['population']);
                        $country->setFlag($data['flag']);
                        $country->setCapital($data['capital']);
                    }

                    $languages = (array_key_exists('languages', $countryData)) ? $countryData['languages'] : [];
                    $this->languageController->syncLanguages($country, $languages);

                    $currencies = (array_key_exists('currencies', $countryData)) ? $countryData['currencies'] : [];
                    $this->currencyController->syncCurrencies($country, $currencies);

                    $entityManager->flush();
                }

                return $this->json(['success' => true, 'message' => 'Se ha realizado la sincronización exitosamente.']);

            }

            return $this->json($responseData);
        } catch (\Exception $e) {
            // Manejo de errores
            return $this->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }


    // ------------------------ //
    // -- FUNCIONES INTERNAS -- //
    // ------------------------ //

    private function manageRegister(Country $country, Request $request, CountryRepository $countryRepository, $edit = false, $oldFilePath) {

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


        // Control de bandera
        $file = $request->files->get('country')['flag'] ?? null;

        if ($file) {
            $filename = uniqid() . '.' . $file->guessExtension();
            $directory = $this->getParameter('kernel.project_dir') . '\public\uploads\flags';

            if ($edit && $country->getFlag()) {
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
            
            $file->move($directory, $filename);
            $country->setFlag('uploads/flags/' . $filename);
        }


        
        $countryRepository->save($country, true);
        $this->entityManager->flush();
        

        if ( $edit ) return $this->redirectToRoute('app_country_view', ['id' => $country->getId()], Response::HTTP_SEE_OTHER);
        else         return $this->redirectToRoute('app_country');
    }

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