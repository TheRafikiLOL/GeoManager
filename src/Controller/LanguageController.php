<?php

namespace App\Controller;

use App\Entity\Language;
use App\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class LanguageController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function syncLanguages(Country $country, array $languages): void
    {
        foreach ($country->getLanguages() as $existingLanguage) {
            $country->removeLanguage($existingLanguage);
        }

        foreach ($languages as $code => $name) {
            $language = $this->entityManager->getRepository(Language::class)->findOneBy(['code' => $code]);

            if (!$language) {
                $language = new Language();
                $language->setCode($code);
            }
            $language->setName($name);

            $this->entityManager->persist($language);

            $country->addLanguage($language);
        }
    }

}
