<?php

namespace App\Controller;

use App\Repository\CountryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/datatable')]
class DatatableController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager; // Inicializar la propiedad
    }

    #[Route('/countries/all', name: 'datatable_countries_all', methods: ['GET'])]
    public function countries(Request $request, CountryRepository $countryRepository): JsonResponse
    {
        // Obtener parámetros de DataTables
        $draw = $request->query->get('draw');
        $start = (int) $request->query->get('start', 0);
        $length = (int) $request->query->get('length', 10);

        // Obtener datos de la base de datos
        $queryBuilder = $countryRepository->createQueryBuilder('c');

        // Contar total de registros
        $totalRecords = $countryRepository->count([]);

        // Obtener registros con paginación
        $countries = $queryBuilder
            ->setFirstResult($start)
            ->setMaxResults($length)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($countries as $country) {
            // Generar el token CSRF para la acción de eliminar
            $csrfToken = $this->csrfTokenManager->getToken('delete' . $country->getId())->getValue();

            // Rellenar las columnas de datos, incluyendo las acciones
            $data[] = [
                'flag' => $country->getFlag() ? '<img class="table-flag" src="' . $country->getFlag() . '" alt="' . $country->getCode() . '">' : null,
                'name' => $country->getName(),
                'region' => $country->getRegion(),
                'subregion' => $country->getSubregion(),
                'area' => $country->getArea(),
                'population' => $country->getPopulation(),
                'actions' => '
                    <a href="' . $this->generateUrl('app_country_view', ['id' => $country->getId()]) . '" class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="top" title="Ver"><i class="fa fa-eye"></i></a>
                    <a href="' . $this->generateUrl('app_country_edit', ['id' => $country->getId()]) . '" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top" title="Editar"><i class="fa fa-edit"></i></a>
                    <button class="btn btn-sm btn-danger remove-country" 
                        data-url="' . $this->generateUrl('app_country_delete', ['id' => $country->getId()]) . '"
                        data-token="' . $csrfToken . '"
                        data-toggle="tooltip" 
                        data-placement="top" 
                        title="Eliminar"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                ',
            ];
        }

        // Respuesta en formato JSON para DataTables
        return new JsonResponse([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, // No hay filtrado, por lo tanto el total filtrado es igual al total
            'data' => $data,
        ]);
    }
}
