<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GlobalController extends AbstractController
{

    // ----------- //
    // -- RUTAS -- //
    // ----------- //

    #[Route('/', name: 'app_global')]
    public function index(): Response
    {
        return $this->render('global/index.html.twig', [
            'controller_name' => 'GlobalController',
        ]);
    }

    // -- ------------------ -- //
    // -- FUNCIONES GLOBALES -- //
    // -- ------------------ -- //

    public static function callToAPI($method, $value = "") {
        $url = "https://restcountries.com/v3.1";

        // Se valida que se le pase un método
        if (empty($method)) throw new \InvalidArgumentException("No se ha facilitado un método.");


        if ($value) $url .= "/{$method}/{$value}";
        else        $url .= "/{$method}";


        // Llamada a la API
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        // Se controlan errores de la llamada
        if (curl_errno($ch)) throw new \RuntimeException('cURL error: ' . curl_error($ch));

        curl_close($ch);

        return json_decode($response, true);
    }

}
