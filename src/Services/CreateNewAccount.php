<?php


namespace App\Services;


use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateNewAccount
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
     * CreateNewAccount constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }

    public function create($email, $password, $role, $nom = null, $prenom = null): Utilisateur
    {
        $user = new Utilisateur();
        $user->setEmail($email);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $user->setNom($nom)->setPrenoms($prenom);
        $user->setRoles([$role]);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
