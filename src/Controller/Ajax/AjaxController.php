<?php


namespace App\Controller\Ajax;


use App\Entity\Commune;
use App\Entity\Departement;
use App\Repository\ArrondissementRepository;
use App\Repository\CommuneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/async', name: 'ajax_')]
class AjaxController extends AbstractController
{
    #[Route('/communes/{id}/departement', name: 'list_commune', methods: ['POST'])]
    public function asyncCommune(Departement $departement, Request $request, CommuneRepository$repository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $communes = $repository->findBy(['departement' => $departement]);

            return $this->json($communes);
        }
        return new Response('Bad Request !', Response::HTTP_BAD_REQUEST);
    }

    #[Route('/arrondissements/{id}/commune', name: 'list_arrondissement', methods: ['POST'])]
    public function asyncArrondissement(Commune $commune, Request $request, ArrondissementRepository$repository, SerializerInterface $serializer): Response
    {
        if ($request->isXmlHttpRequest()) {
            $content = json_decode($request->getContent(), true);
            if (isset($content['update']) && $content['update']) {
                $arrondissements = $repository->findWithResultByCommune($commune);
            } else {
                $arrondissements = $repository->findWithNoResultByCommune($commune);
            }
            return new JsonResponse(
                $serializer->serialize(
                    $arrondissements,
                    'json',
                    ['groups' => 'front_fetch']
                ),
                Response::HTTP_OK,
                [],
                true
            );
        }
        return new Response('Bad Request !', Response::HTTP_BAD_REQUEST);
    }
}
