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

        // Crear el query builder
        $queryBuilder = $countryRepository->createQueryBuilder('c');

        // Aplicar filtros
        if (isset($_REQUEST['columns'])) {
            foreach ($_REQUEST['columns'] as $index => $column) {
                $searchValue = $column['search']['value'] ?? '';

                if ($index == 1 && !empty($searchValue)) {
                    $queryBuilder->andWhere('c.name LIKE :searchName')
                                 ->setParameter('searchName', '%' . $searchValue . '%');
                }

                if ($index == 2 && !empty($searchValue)) {
                    $queryBuilder->andWhere('c.region LIKE :searchRegion')
                                 ->setParameter('searchRegion', '%' . $searchValue . '%');
                }

                if ($index == 3 && !empty($searchValue)) {
                    $queryBuilder->andWhere('c.subregion LIKE :searchSubregion')
                                 ->setParameter('searchSubregion', '%' . $searchValue . '%');
                }

                if ($index == 4 && !empty($searchValue)) {
                    $queryBuilder->andWhere('c.area = :searchArea')
                                 ->setParameter('searchArea', $searchValue);
                }

                if ($index == 5 && !empty($searchValue)) {
                    $queryBuilder->andWhere('c.population = :searchPopulation')
                                 ->setParameter('searchPopulation', $searchValue);
                }
            }
        }

        // Aplicar ordenamiento
        $order = $_REQUEST['order'] ?? null;
        if ($order) {
            $columnIndex = $order[0]['column'];
            $direction = $order[0]['dir'];

            switch ($columnIndex) {
                case 1:
                    $queryBuilder->orderBy('c.name', $direction);
                    break;
                case 2:
                    $queryBuilder->orderBy('c.region', $direction);
                    break;
                case 3:
                    $queryBuilder->orderBy('c.subregion', $direction);
                    break;
                case 4:
                    $queryBuilder->orderBy('c.area', $direction);
                    break;
                case 5:
                    $queryBuilder->orderBy('c.population', $direction);
                    break;
            }
        }

        // Obtener el conteo de registros filtrados
        $filteredRecords = (clone $queryBuilder)
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Cargar la paginación
        $countries = $queryBuilder->setFirstResult($start)
                                   ->setMaxResults($length)
                                   ->getQuery()
                                   ->getResult();

        // Total de registros sin filtrar
        $totalRecords = $countryRepository->count([]);

        $data = [];
        foreach ($countries as $country) {
            $csrfToken = $this->csrfTokenManager->getToken('delete' . $country->getId())->getValue();

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

        return new JsonResponse([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }
}
