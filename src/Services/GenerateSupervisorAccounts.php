<?php


namespace App\Services;


use App\Entity\Departement;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GenerateSupervisorAccounts
{
    /**
     * Entity manager
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * User password encoder
     *
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    /**
     * CreateSupervisorAccounts constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }

    public function generateAccounts(): array
    {
        /** @var Departement[]|null $departements */
        $departements = $this->em->getRepository(Departement::class)->findBy([], ['nom' => 'ASC']);
        $accounts = [];
        foreach ($departements as $departement) {
            $email = sprintf('%s@email.com', strtolower($departement->getNom()));
            $user = $this->em->getRepository(Utilisateur::class)->findOneBy(["email" => $email]);
            if (!$user) {
                $user = new Utilisateur();
                $user->setEmail($email);

                $this->em->persist($user);
            }

            $password = $this->generateRandomString();
            $user->setPassword($this->encoder->encodePassword($user, $password));
            $user->setRoles(['ROLE_SUPERVISEUR']);
            $this->em->flush();

            $accounts[] = [
                'departement' => $departement->getNom(),
                'email' => $email,
                'password' => $password
            ];
        }
        return $accounts;
    }

    private function generateRandomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
