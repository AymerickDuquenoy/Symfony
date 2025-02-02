<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;



class ProfileController extends AbstractController
{   #[Route('/profile',name: 'app_profile')]
    public function profile(Request $request, EntityManagerInterface $em, UserInterface $user): Response
{


    // Continue with your logic
    $form = $this->createForm(ProfileFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        $this->addFlash('success', 'Profil mis à jour.');
        return $this->redirectToRoute('profile');
    }

    return $this->render('profile/index.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/delete', name: 'profile_delete')]
    #[IsGranted('ROLE_USER')]
    public function deleteProfile(EntityManagerInterface $em, UserInterface $user): Response
    {
        if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer un super admin.');
            return $this->redirectToRoute('profile');
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_homepage');
    }
}
