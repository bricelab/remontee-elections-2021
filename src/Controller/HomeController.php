<?php

namespace App\Controller;

use App\Entity\Arrondissement;
use App\Entity\Resultat;
use App\Form\ModifierRemonteeStartType;
use App\Form\RemonteeType;
use App\Repository\ArrondissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
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
                    $oldResultat = $em->getRepository(Resultat::class)->findOneBy(['arrondissement' => $arrondissement]);
                    if ($oldResultat) {
                        $this->addFlash('error', 'Les résultats de cet arrondissement ont déjà été remontés !');
                    } else {
                        $sommeVoix = $resultat->getNbNuls() + $resultat->getNbVoixDuoTT() + $resultat->getNbVoixFcbe() + $resultat->getNbVoixRlc();
                        if ($arrondissement->getNbInscrits() >= $resultat->getNbVotants() && $sommeVoix === $resultat->getNbVotants()) {
                            $resultat->setArrondissement($arrondissement);
                            $arrondissement->setResultat($resultat);

                            $em->persist($resultat);
                            $em->flush();

                            $this->addFlash('success', 'Résultat ajouté avec succès !');

                            return $this->redirectToRoute('home');
                        } elseif ($resultat->getNbVotants() <= round($arrondissement->getNbInscrits() * 1.10) || $sommeVoix <= $resultat->getNbVotants() + 5) {
                            $resultat->setArrondissement($arrondissement);
                            $resultat->setWarningFlag(true);
                            $arrondissement->setResultat($resultat);

                            $em->persist($resultat);
                            $em->flush();

                            $this->addFlash('success', 'Résultat ajouté avec succès !');

                            return $this->redirectToRoute('home');
                        } else {
                            $this->addFlash('error', 'Les données entrées ne sont pas cohérentes !');
                        }
                    }
                } else {
                    $this->addFlash('error', 'Choisissez un arrondissement valide svp !');
                }
            } else {
                $this->addFlash('error', 'Choisissez un arrondissement valide svp !');
            }
        }
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/modifier-un-resultat-start', name: 'update_resultat_start')]
    public function updateStart(Request $request, ArrondissementRepository $repository): Response
    {
        $form = $this->createForm(ModifierRemonteeStartType::class)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $extraData = $form->getExtraData();

            if (isset($extraData['arrondissement']) && $extraData['arrondissement']) {
                $arrondissement = $repository->findOneBy(['id' => $extraData['arrondissement']]);
                if ($arrondissement) {
                    return $this->redirectToRoute('update_resultat_end', ['id' => $arrondissement->getId()]);
                } else {
                    $this->addFlash('error', 'Choisissez un arrondissement valide svp !');
                }
            } else {
                $this->addFlash('error', 'Choisissez un arrondissement valide svp !');
            }
        }
        return $this->render('home/update_start.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/modifier-un-resultat/{id}/end', name: 'update_resultat_end')]
    public function updateEnd(Arrondissement $arrondissement, Request $request, EntityManagerInterface $em): Response
    {
        $resultat = $arrondissement->getResultat();
        $form = $this->createForm(RemonteeType::class, $resultat)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sommeVoix = $resultat->getNbNuls() + $resultat->getNbVoixDuoTT() + $resultat->getNbVoixFcbe() + $resultat->getNbVoixRlc();
            if ($arrondissement->getNbInscrits() >= $resultat->getNbVotants() && $sommeVoix === $resultat->getNbVotants()) {
                $em->flush();

                $this->addFlash('success', 'Résultat modifié avec succès !');

                return $this->redirectToRoute('home');
            } elseif ($resultat->getNbVotants() <= round($arrondissement->getNbInscrits() * 1.10) || $sommeVoix <= $resultat->getNbVotants() + 5) {
                $resultat->setWarningFlag(true);

                $em->persist($resultat);
                $em->flush();

                $this->addFlash('success', 'Résultat ajouté avec succès !');

                return $this->redirectToRoute('home');
            } else {
                $this->addFlash('error', 'Les données entrées ne sont pas cohérentes !');
            }
        }
        return $this->render('home/update_end.html.twig', [
            'form' => $form->createView(),
            'arrondissement' => $arrondissement,
        ]);
    }
//* @Security("is_granted('ROLE_SUPERVISEUR') or is_granted('ROLE_DASHBOARD')")
}
