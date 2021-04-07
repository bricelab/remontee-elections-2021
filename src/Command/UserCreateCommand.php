<?php

namespace App\Command;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'app:user:create';
    protected static $defaultDescription = 'Add a short description for your command';

    /**
     * Entity manager
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * Kernel
     *
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    /**
     * User password encoder
     *
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    /**
     * UserCreateCommand constructor.
     * @param EntityManagerInterface $em
     * @param KernelInterface $kernel
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $em, KernelInterface $kernel, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->kernel = $kernel;
        $this->encoder = $encoder;

        parent::__construct();
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
            $user = $this->em->getRepository(Utilisateur::class)->findOneBy(["email" => $email]);
            if($user){
                $io->error(sprintf('User with email "%s" already exists', $email));
                return Command::FAILURE;
            }
            else{
                $io->note(sprintf('User with email "%s" doesn\'t exist', $email));
                $io->note(sprintf('Creating user with email "%s" ', $email));

                $user = new Utilisateur();
                $user->setEmail($email);
                $user->setNom($nom)->setPrenoms($prenom);
                $user->setPassword($this->encoder->encodePassword($user, $password));
                if (!$role) {
                    $role = 'ROLE_USER';
                }
                $user->setRoles([$role]);

                $this->em->persist($user);
                $this->em->flush();
            }
            $io->success(sprintf('User with email "%s" has been created successfully.', $email));

            return Command::SUCCESS;
        }

        $io->error('Please provide valide email address');

        return Command::FAILURE;
    }
}
