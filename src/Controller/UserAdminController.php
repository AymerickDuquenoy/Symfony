<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class UserAdminController extends AbstractController
{
    #[Route('/', name: 'admin_users')]
    public function listUsers(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_users_delete')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        if ($user->getRoles() === ['ROLE_SUPER_ADMIN']) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer un super admin.');
            return $this->redirectToRoute('admin_users');
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('admin_users');
    }
}
