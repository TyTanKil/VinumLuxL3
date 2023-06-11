<?php

namespace App\MyClass;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;



class Cart 
{
    private $items = [];

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requeStack)
    {
        $this->session = $requeStack->getSession();
        $this->entityManager = $entityManager;
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function add($id)
    {
        $cart=$this->session->get('cart',[]);

        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id]=1;
        }
        $this->session->set('cart',$cart);
    }

    public function remove(int $id)
    {
        $cart=$this->session->get('cart',[]);

        if(!empty($cart[$id])){
            unset($cart[$id]);
        }
        $this->session->set('cart',$cart);
    }

    public function removeAll()
    {
        return $this->session->remove('cart'); 
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): float
    {
        $total = 0.0;

        foreach ($this->getFull() as $item) {
            $total += $item['product']->getPriceHt() * $item['quantity'];
        }

        return $total;
    } 
    
    public function getFull()
    {
        $cartComplete=[];

        if($this->get()){
            foreach($this->get() as $id => $quantity){
                $product_object=$this->entityManager->getRepository(Product::class)->findOneById($id);
                if(!$product_object){
                    $this->remove($id);
                    continue;
                }
                $cartComplete[]=[
                    'product'=>$product_object,
                    'quantity'=>$quantity
                ];
            }
        }
        return $cartComplete;
    }
}