<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\CustomerAdress;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AdressType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
    #[Route('/account/add_adress', name: 'app_account_add_adress')]
    public function addAdress(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adress = new CustomerAdress();
        $form = $this->createForm(AdressType::class, $adress);
        $form ->handleRequest($request);

        $fromOrder =  $request->query->get('fromOrder');

        if($form->isSubmitted() && $form->isValid()){
            $adress->setUser($this->getUser());
            $entityManager->persist($adress);
            $entityManager->flush();

            if(isset($fromOrder)){
                return $this->redirectToRoute('app_order');
            }else{
                return $this->redirectToRoute('app_account');
            }
            
        }
        return $this->render('account/addadress.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/account/orders', name: 'app_account_orders')]
    public function orders(Request $request, EntityManagerInterface $entityManager): Response
    {
        
        return $this->render('account/orders.html.twig', [
           
        ]);
    }
}
