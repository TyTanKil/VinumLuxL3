<?php

namespace App\Controller;

use App\Entity\CommandLine;
use App\Entity\Order;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\MyClass\Cart;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }
    
    #[Route('/order', name: 'app_order')]
    public function index(Cart $cart, Request $request): Response
    {

        if(!$this->getUser()->getAdress()->getValues()){
            return $this->redirectToRoute('app_account_add_adress', ['fromOrder'=>true]);
        }

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime('now');
            $nb = $date->format('YmdHis');
            $order->setOrderNumber($nb);
            $order->setValid(true);
            $order->setUser($this->getUser());
            $order->setDateTime(new DateTime('now'));
            $this->entityManager->persist($order);
            $this->entityManager->flush();   
            
            foreach ($cart->getFull() as $product) {
                $commandLine = new CommandLine();
                $commandLine->setCommand($order);
                $commandLine->setProduct($product['product']);
                $commandLine->setQuantity($product['quantity']);
                $this->entityManager->persist($commandLine);
            }
            $this->entityManager->flush();   


           return $this->redirectToRoute('app_account_orders');

        }

        return $this->render('order/index.html.twig', [
            'cart' => $cart ->getFull(),
            'total' => $cart ->getTotal(),
            'form' => $form->createView(),
        ]);
    }
}
