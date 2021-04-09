<?php

namespace App\Command;

use App\Services\GenerateSupervisorAccounts;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserGenerateSupervisorsCommand extends Command
{
    protected static $defaultName = 'app:user:generate-supervisors';
    protected static string $defaultDescription = 'Add a short description for your command';

    private GenerateSupervisorAccounts $generator;

    /**
     * UserGenerateSupervisorsCommand constructor.
     * @param GenerateSupervisorAccounts $generator
     */
    public function __construct(GenerateSupervisorAccounts $generator)
    {
        $this->generator = $generator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->note('Generating Supervisor accounts');

        $accounts = $this->generator->generateAccounts();

        $io->title('LISTE DES COMPTES CREES :');
        foreach ($accounts as $account) {
            $io->text(sprintf('%s => Adresse mail : %s | Mot de passe : %s', $account['departement'], $account['email'], $account['password']));
        }

        $io->success('Everything went well');

        return Command::SUCCESS;
    }
}
