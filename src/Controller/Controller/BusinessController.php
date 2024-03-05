<?php

namespace App\Controller\Controller;

use App\Entity\Business;
use App\Entity\User;
use App\Form\Form\BusinessFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/business')]
class BusinessController extends AbstractController
{
    #[Route(path: '/create', name: 'create_business')]
    public function create(Request $request, #[CurrentUser] User $currentUser): Response
    {
        $business = new Business(owner: $currentUser);
        $businessForm = $this->createForm(type: BusinessFormType::class, data: $business);

        $businessForm->handleRequest($request);
        if ($businessForm->isSubmitted() && $businessForm->isValid()) {

        }

        return $this->render('business/create.html.twig', [
            'businessForm' => $businessForm,
        ]);
    }
}