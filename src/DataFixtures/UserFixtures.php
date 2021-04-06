<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
         $user = new Utilisateur();
         $user
             ->setEmail('user@email.com')
             ->setPassword(
                 $this->encoder->encodePassword($user, 'password')
             )
             ->setNom('Admin')
             ->setPrenoms('Test')
             ->setRoles(['ROLE_SUPER_ADMIN'])
         ;
         $manager->persist($user);

        $manager->flush();
    }
}
