<?php

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\GlobalController;

class CountryController extends AbstractController
{

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
    public function new(Request $request, CountryRepository $countryRepository): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $countryRepository->save($country, true);

            return $this->redirectToRoute('app_country');
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
    public function edit(Request $request, Country $country, CountryRepository $countryRepository): Response
    {
        $form = $this->createForm(CountryType::class, $country);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $countryRepository->save($country, true);

            return $this->redirectToRoute('app_country_view', ['id' => $country->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('country/edit.html.twig', [
            'form' => $form->createView(),
            'country' => $country,
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


    // ------------------- //
    // -- FUNCIONES API -- //
    // ------------------- //


}