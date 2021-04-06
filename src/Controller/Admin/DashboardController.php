<?php

namespace App\Controller\Admin;

use App\Entity\Arrondissement;
use App\Entity\Departement;
use App\Entity\Resultat;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        $doctrine = $this->getDoctrine();

        // TNombre d'arrondissement remontés
        $nbArrondissementRemontee = $doctrine->getRepository(Resultat::class)->count([]);
        $nbArrondissementRestant = $doctrine->getRepository(Arrondissement::class)->count([]) - $nbArrondissementRemontee;
        $nbRemonteesPie = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $nbRemonteesPie->setData([
            'labels' => ['Nombre de remontées', 'Nombre restant'],
            'datasets' => [
                [
                    'label' => 'Nombre d\'arrondissement remontés',
                    'backgroundColor' => [
                        'rgb(172, 31, 207)',
                        'rgb(30, 12, 22)',
                    ],
//                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [$nbArrondissementRemontee, $nbArrondissementRestant],
                    'hoverOffset' => 4,
                ],
            ],
        ]);

        // Taux de remontée et de participation par département
        $departementIds = $doctrine->getRepository(Departement::class)->findAllIdList();
        $label = [];
        $tauxRemonnteeParDep = [];
        $voixParDuoTTParDep = [];
        $voixParDuoRLCParDep = [];
        $voixParDuoFCBEParDep = [];
        $tauxParticipationParDep = [];
        $repo = $doctrine->getRepository(Resultat::class);
        foreach ($departementIds as $departementId) {
            $id = $departementId['id'];
            $label[] = $departementId['nom'];

            $nbRemontees = $repo->tauxRemonteeParCommune($id)[0]['nb_count'];
            $nbArrondissement = $repo->nbArrondissementParDepartement($id)[0]['nb_count'];
            $tauxRemonnteeParDep[] = $nbArrondissement > 0 ? round($nbRemontees * 100 / $nbArrondissement) : 0;

            $totauxVoixParDep = $repo->totalDesVoixObtenusParDepartement($id)[0];
            $suffrageExprimes = intval($totauxVoixParDep['nb_voix_rlc']) + intval($totauxVoixParDep['nb_voix_fcbe']) + intval($totauxVoixParDep['nb_voix_duo_tt']);
            $voixParDuoRLCParDep[] = $suffrageExprimes > 0 ? round(intval($totauxVoixParDep['nb_voix_rlc']) * 100 / $suffrageExprimes, 2) : 0;
            $voixParDuoFCBEParDep[] = $suffrageExprimes > 0 ? round(intval($totauxVoixParDep['nb_voix_fcbe']) * 100 / $suffrageExprimes, 2) : 0;
            $voixParDuoTTParDep[] = $suffrageExprimes > 0 ? round(intval($totauxVoixParDep['nb_voix_duo_tt']) * 100 / $suffrageExprimes, 2) : 0;

            $tauxParticipationParDep[] = intval($totauxVoixParDep['nb_inscrits']) > 0 ? round(intval($totauxVoixParDep['nb_votants']) * 100 / intval($totauxVoixParDep['nb_inscrits']), 2) : 0;
        }
        $tauxRemonntee = $chartBuilder->createChart(Chart::TYPE_BAR);
        $tauxRemonntee->setData([
            'labels' => $label,
            'datasets' => [
                [
                    'label' => 'Taux de remontée',
                    'backgroundColor' => 'rgb(174, 5, 38)',
//                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $tauxRemonnteeParDep,
                ],
                [
                    'label' => 'Taux de participation',
                    'backgroundColor' => 'rgb(233, 183, 33)',
//                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $tauxParticipationParDep,
                ],
            ],
        ]);
        $tauxRemonntee->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' => ['min' => 0, 'max' => 100]],
                ],
            ],
        ]);

        // Voix par département
        $voixDuos = $chartBuilder->createChart(Chart::TYPE_BAR);
        $voixDuos->setData([
            'labels' => $label,
            'datasets' => [
                [
                    'label' => 'RLC - KOHOUE & AGOSSA',
                    'backgroundColor' => 'rgb(239, 120, 0)',
//                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $voixParDuoRLCParDep,
                ],
                [
                    'label' => 'FCBE - DJIMBA & HOUNKPE',
                    'backgroundColor' => 'rgb(30, 112, 50)',
//                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $voixParDuoFCBEParDep,
                ],
                [
                    'label' => 'TT - TALON & TALATA',
                    'backgroundColor' => 'rgb(48, 79, 147)',
//                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $voixParDuoTTParDep,
                ],
            ],
        ]);
        $voixDuos->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' => ['min' => 0, 'max' => 100]],
                ],
            ],
        ]);

        // Tendances nationales
        $totauxVoix = $repo->totalDesVoixObtenus()[0];
        $suffrageExprimesTotal = intval($totauxVoix['nb_voix_rlc']) + intval($totauxVoix['nb_voix_fcbe']) + intval($totauxVoix['nb_voix_duo_tt']);
        $tendancesNationalesData = [
            $suffrageExprimesTotal > 0 ? round(intval($totauxVoix['nb_voix_rlc']) * 100 / $suffrageExprimesTotal) : 0,
            $suffrageExprimesTotal > 0 ? round(intval($totauxVoix['nb_voix_fcbe']) * 100 / $suffrageExprimesTotal) : 0,
            $suffrageExprimesTotal > 0 ? round(intval($totauxVoix['nb_voix_duo_tt']) * 100 / $suffrageExprimesTotal) : 0
        ];
//        dd($totauxVoix, $tendancesNationalesData, $suffrageExprimesTotal);
        $tendancesNationales = $chartBuilder->createChart(Chart::TYPE_PIE);
        $tendancesNationales->setData([
            'labels' => ['RLC', 'FCBE', 'TT'],
            'datasets' => [
                [
//                    'label' => 'My First dataset',
                    'backgroundColor' => [
                        'rgb(239, 120, 0)',
                        'rgb(30, 112, 50)',
                        'rgb(48, 79, 147)'
                    ],
//                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $tendancesNationalesData,
                    'hoverOffset' => 4,
                ],
            ],
        ]);

        // Taux de participation
        $tauxParticipationData = intval($totauxVoix['nb_inscrits']) > 0 ? round(intval($totauxVoix['nb_votants']) * 100 / intval($totauxVoix['nb_inscrits']), 2) : 0;
        $tauxParticipation = $chartBuilder->createChart(Chart::TYPE_BAR);
        $tauxParticipation->setData([
            'labels' => ["$tauxParticipationData%"],
            'datasets' => [
                [
                    'label' => 'Pourcentage de votants',
                    'backgroundColor' => [
                        'rgb(9, 229, 229)',
                    ],
//                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [$tauxParticipationData],
                    'hoverOffset' => 4,
                ],
            ],
        ]);

        $tauxParticipation->setOptions([
            'scales' => [
                'yAxes' => [
                    ['ticks' => ['min' => 0, 'max' => 100]],
                ],
            ],
        ]);
        return $this->render('dashboard/index.html.twig', [
//            'chart' => $chart,
            'tauxParticipation' => $tauxParticipation,
            'nbRemonteesPie' => $nbRemonteesPie,
            'tauxRemonntee' => $tauxRemonntee,
            'voixDuos' => $voixDuos,
            'tendancesNationales' => $tendancesNationales,
        ]);
    }
}
