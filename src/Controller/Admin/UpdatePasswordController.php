<?php


namespace App\Controller\Admin;


use App\Form\UpdatePasswordType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UpdatePasswordController extends AbstractController
{

    /**
     * @param Request $request
     * @param UtilisateurRepository $repository
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @IsGranted("ROLE_USER")
     */
    #[Route('/update-password', name: 'update_password', methods: ['GET', 'POST'])]
    public function updatePassword(Request $request, UtilisateurRepository $repository, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(UpdatePasswordType::class)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $old = $form->get('oldPassword')->getData();
            $new = $form->get('newPassword')->getData();

            if ($encoder->isPasswordValid($user, $old)) {
                $repository->upgradePassword($user, $encoder->encodePassword($user, $new));

                if (in_array('ROLE_SUPERVISEUR', $user->getRoles())) {
                    return $this->redirectToRoute('home');
                }

                return $this->redirectToRoute('dashboard');
            } else {
                $this->addFlash('error', 'Mot de passe incorrect !');
            }
        }
        return $this->render('utilisateur/update_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
