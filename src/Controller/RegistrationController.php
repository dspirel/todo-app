<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RegistrationFormType;
use App\Entity\User;

class RegistrationController extends AbstractController
{
    #[Route(path: '/auth/register', name: 'app_registration', methods: ['GET', 'POST'])]
    public function new(Request $request, 
                        UserPasswordHasherInterface $passwordHasher,
                        EntityManagerInterface $entityManagerInterface): Response
    {
        //if logged in redirect
        if ($this->getUser()) {
            return $this->redirectToRoute('app_tasks');
        }

        $user = new User();

        //create form
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            //hash password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user, 
                    $form->get('plainPassword')->getData())
            );

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('authentication/register.html.twig', [
            'form' => $form,
        ]);
    }
}
