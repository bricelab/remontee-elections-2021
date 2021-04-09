<?php

namespace App\Command;

use App\Repository\UtilisateurRepository;
use App\Services\CreateNewAccount;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'app:user:create';
    protected static $defaultDescription = 'Add a short description for your command';

    /**
     * @var CreateNewAccount
     */
    private CreateNewAccount $createNewAccount;
    /**
     * @var UtilisateurRepository
     */
    private UtilisateurRepository $repository;


    /**
     * UserCreateCommand constructor.
     * @param CreateNewAccount $createNewAccount
     * @param UtilisateurRepository $repository
     */
    public function __construct(CreateNewAccount $createNewAccount, UtilisateurRepository $repository)
    {
        parent::__construct();
        $this->createNewAccount = $createNewAccount;
        $this->repository = $repository;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED, 'Adresse mail')
            ->addArgument('password', InputArgument::REQUIRED, 'Mot de passe')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role principal')
            ->addArgument('nom', InputArgument::OPTIONAL, 'Nom')
            ->addArgument('prenom', InputArgument::OPTIONAL, 'Prénoms')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $role = $input->getArgument('role');
        $nom = $input->getArgument('nom');
        $prenom = $input->getArgument('prenom');

        if ($email) {
            $io->note(sprintf('Checking if user with email "%s" exists', $email));
            $user = $this->repository->findOneBy(["email" => $email]);
            if($user){
                $io->error(sprintf('User with email "%s" already exists', $email));
                return Command::FAILURE;
            }
            else{
                $io->note(sprintf('User with email "%s" doesn\'t exist', $email));
                $io->note(sprintf('Creating user with email "%s" ', $email));

                if (!$role) {
                    $role = 'ROLE_SUPERVISEUR';
                }
                $this->createNewAccount->create($email, $password, $role, $nom, $prenom);
            }
            $io->success(sprintf('User with email "%s" has been created successfully.', $email));

            return Command::SUCCESS;
        }

        $io->error('Please provide valide email address');

        return Command::FAILURE;
    }
}
