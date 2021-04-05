<?php

namespace App\Controller;

use App\Entity\Resultat;
use App\Form\RemonteeType;
use App\Repository\ArrondissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request, ArrondissementRepository $repository, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RemonteeType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Resultat $resultat */
            $resultat = $form->getData();

            $extraData = $form->getExtraData();

            if (isset($extraData['arrondissement']) && $extraData['arrondissement']) {
                $arrondissement = $repository->findOneBy(['id' => $extraData['arrondissement']]);
                if ($arrondissement) {
                    $resultat->setArrondissement($arrondissement);
                    $arrondissement->setResultat($resultat);

                    $em->persist($resultat);
                    $em->flush();

                    $this->addFlash('success', 'Résultat ajouté avec succès !');

                    return $this->redirectToRoute('home');
                }
            }
        }
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
