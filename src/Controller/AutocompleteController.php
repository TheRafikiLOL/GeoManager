<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LanguageRepository;

#[Route('/autocomplete')]
class AutocompleteController extends AbstractController
{

    #[Route('/languages', name: 'autocomplete_all_languages')]
    public function allLanguages(LanguageRepository $languageRepository): Response {
        
        $languages = $languageRepository->findBy([], ['name' => 'ASC']);

        $languageOptions = array_map(function($language) {
            return [
                'id' => $language->getId(),
                'text' => $language->getName()
            ];
        }, $languages);

        return $this->json($languageOptions);

    }
}
