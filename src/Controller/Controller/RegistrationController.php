<?php

namespace App\Controller\Controller;

use App\Entity\Email;
use App\Entity\User;
use App\Form\Form\RegistrationFormType;
use App\Repository\EmailRepository;
use App\Security\Authenticator\CustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{


    public function __construct(
        private readonly EmailRepository $emailRepository
    )
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, CustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $emailAddress = $form->get('email')->getData();
            $email = $this->emailRepository->findOneBy([
                'address' => $emailAddress,
            ]);

            if (!$email instanceof Email) {
                $email = new Email(address: $emailAddress, owner: $user);
                $user->setEmail($email);
                $email->setOwner($user);
                $entityManager->persist($email);
            } else {
                if ($email->getOwner() instanceof User) {
                    $form->addError(new FormError(Email::DUPLICATE_EMAIL_ADDRESS));
                } else {
                    $user->setEmail($email);
                    $email->setOwner($user);
                }
            }


            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
