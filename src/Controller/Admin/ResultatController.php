<?php

namespace App\Controller\Admin;

use App\Entity\Resultat;
use App\Form\ResultatType;
use App\Repository\ResultatRepository;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use SplTempFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResultatController
 * @package App\Controller\Admin
 * @IsGranted("ROLE_USER")
 */
#[Route('/admin/resultat')]
class ResultatController extends AbstractController
{
    #[Route('/', name: 'resultat_index', methods: ['GET'])]
    public function index(ResultatRepository $resultatRepository): Response
    {
        return $this->render('resultat/index.html.twig', [
            'resultats' => $resultatRepository->findBy([], ['updatedAt' => 'DESC']),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/new', name: 'resultat_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $resultat = new Resultat();
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resultat->getArrondissement()->setResultat($resultat);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resultat);
            $entityManager->flush();

            return $this->redirectToRoute('resultat_index');
        }

        return $this->render('resultat/new.html.twig', [
            'resultat' => $resultat,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/details', name: 'resultat_show', methods: ['GET'])]
    public function show(Resultat $resultat): Response
    {
        return $this->render('resultat/show.html.twig', [
            'resultat' => $resultat,
        ]);
    }

    /**
     * @param Request $request
     * @param Resultat $resultat
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}/edit', name: 'resultat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Resultat $resultat): Response
    {
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('resultat_index');
        }

        return $this->render('resultat/edit.html.twig', [
            'resultat' => $resultat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Resultat $resultat
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/{id}', name: 'resultat_delete', methods: ['POST'])]
    public function delete(Request $request, Resultat $resultat): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resultat->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $resultat->getArrondissement()->setResultat(null);
            $entityManager->remove($resultat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('resultat_index');
    }

    /**
     * @param ResultatRepository $repository
     * @return Response
     * @IsGranted("ROLE_USER")
     * @throws CannotInsertRecord
     */
    #[Route('/export-to-csv', name: 'resultat_export_csv', methods: ['GET'])]
    public function exportToCsv(ResultatRepository $repository): Response
    {
        $resultats = $repository->findAll();
//        dd($resultats);

        $csvWriter = Writer::createFromFileObject(new SplTempFileObject());

        $header = [
            'Date',
            'Département',
            'Commune',
            'Arrondissement',
            'Inscrits',
            'Votants',
            'RLC',
            'FCBE',
            'Duo Talon.Talata',
            'Votes nuls',
            'Observations',
        ];

        $csvWriter->insertOne($header);

        foreach ($resultats as $resultat) {
            $data = [
                $resultat->getCreatedAt()->format('d/m/Y H:i:s'),
                $resultat->getArrondissement()->getCommune()->getDepartement()->getNom(),
                $resultat->getArrondissement()->getCommune()->getNom(),
                $resultat->getArrondissement()->getNom(),
                $resultat->getArrondissement()->getNbInscrits(),
                $resultat->getNbVotants(),
                $resultat->getNbVoixRlc(),
                $resultat->getNbVoixFcbe(),
                $resultat->getNbVoixDuoTT(),
                $resultat->getNbNuls(),
                $resultat->getObservations(),
            ];

            $csvWriter->insertOne($data);
        }

        $flushThreshold = 1000; //the flush value should depend on your CSV size.
        $contentCallback = function () use ($csvWriter, $flushThreshold) {
            foreach ($csvWriter->chunk(1024) as $offset => $chunk) {
                echo $chunk;
                if ($offset % $flushThreshold === 0) {
                    flush();
                }
            }
        };

        $response = new StreamedResponse();
        $response->headers->set('Content-Encoding', 'none');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'remontees-presidentielles-2021.csv'
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Description', 'File Transfer');
        $response->setCallback($contentCallback);

        return $response;
    }
}
