<?php

namespace App\Command;

use App\Entity\Arrondissement;
use App\Entity\Commune;
use App\Entity\Departement;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

class CsvImportCommand extends Command
{
    protected static $defaultName = 'app:csv:import';
    protected static $defaultDescription = 'Import a csv file using league/csv';

    private EntityManagerInterface $em;
    private KernelInterface $kernel;

    /**
     * CsvImportCommand constructor.
     * @param EntityManagerInterface $em
     * @param KernelInterface $kernel
     */
    public function __construct(EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->em = $em;
        $this->kernel = $kernel;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
//            ->addArgument('dataType', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
//        $dataType = $input->getArgument('dataType');

//        if ($dataType) {
//            $io->note(sprintf('You passed an argument: %s', $dataType));
//        }

        $status = false;
        $error_details = '';

        $file_name = $this->kernel->getProjectDir().'/imports/liste-arrondissement.csv';
        $io->note(sprintf('Importing arrondissements from  %s', $file_name));
        $csv = Reader::createFromPath($file_name);
        $input_bom = $csv->getInputBOM();
        if($input_bom!==Reader::BOM_UTF8) {
            //let's set the output BOM
            $csv->setOutputBOM(Reader::BOM_UTF8);
            //let's convert the incoming data from iso-88959-15 to utf-8
            $csv->addStreamFilter('convert.iconv.ISO-8859-15/UTF-8');
        }

        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);
        $header = $csv->getHeader(); //returns the CSV header record
        $records = $csv->getRecords(); //returns all the CSV records as an Iterator object
        $cpt = 0;
        $new = 0;
        $doublon = [];

        foreach ($records as $k => $record) {
            $departementName = $record[$header[0]];
            $ce = intval($record[$header[1]]);
            $communeName = $record[$header[2]];
            $arrondissementName = $record[$header[3]];
            $nbInscrits = intval($record[$header[4]]);

            $arrondissement = self::findOrCreateArrondissement($arrondissementName, $communeName, $departementName, $nbInscrits, $ce, $this->em, $new, $doublon);;


            if($arrondissement!==null) {
                $this->em->persist($arrondissement);
                $this->em->flush();
                $cpt++;
            } else {
                $io->error('Erreur.');
                return Command::FAILURE;
            }

        }

        $io->success("Everything went well. ($cpt arrondissements traités dont $new nouveaux)");
        dump($doublon);

        return Command::SUCCESS;
    }

    public static function findOrCreateArrondissement($arrondissementName, $communeName, $departementName, $nbInscrits, $ce, $manager, &$new, &$doublon): ?Arrondissement
    {
        if($arrondissementName === null || trim($arrondissementName)==''){
            return null;
        }

        $arrondissement = $manager->getRepository(Arrondissement::class)->findOneBy(['nom' => $arrondissementName, 'nbInscrits' => $nbInscrits]);
        if(null===$arrondissement) {
            $arrondissement = new Arrondissement();
            $commune = self::findOrCreateCommune($communeName, $departementName, $ce, $manager);
            if (!$commune){
                return null;
            }
            $arrondissement
                ->setNom($arrondissementName)
                ->setCommune($commune)
                ->setNbInscrits($nbInscrits)
            ;
            $manager->persist($arrondissement);
            $manager->flush();
            $new++;
        } else {
            $doublon[] = [
                'ancien' => $departementName,
                'commune' => $communeName,
                'arrondissement' => $arrondissementName
            ];
        }

        return $arrondissement;
    }

    public static function findOrCreateCommune($communeName, $departementName, $ce, $manager): ?Commune
    {
        if($communeName === null || trim($communeName)=='')
            return null;

        $commune = $manager->getRepository(Commune::class)->findOneBy(['nom' => $communeName, 'ce' => $ce]);
        if(null===$commune) {
            $commune = new Commune();
            $departement = self::findOrCreateDepartement($departementName, $manager);
            if (!$departement){
                return null;
            }
            $commune
                ->setNom($communeName)
                ->setDepartement($departement)
                ->setCe($ce)
            ;
            $manager->persist($commune);
            $manager->flush();
        }

        return $commune;
    }


    public static function findOrCreateDepartement($departementName, $manager): ?Departement
    {
        if($departementName === null || trim($departementName)==''){
            return null;
        }

        $departement = $manager->getRepository(Departement::class)->findOneBy(['nom' => $departementName]);
        if(null === $departement) {
            $departement = new Departement();
            $departement->setNom($departementName);
            $manager->persist($departement);
            $manager->flush();
        }

        return $departement;
    }
}
