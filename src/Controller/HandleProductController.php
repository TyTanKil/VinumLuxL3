<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\Media;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


#[Route('admin/handle/product')]
class HandleProductController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }


    #[Route('/', name: 'app_handle_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('handle_product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_handle_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
    
        
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('path')->getData();
            if ($file) {
                $media = new Media();
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->guessExtension();
                $newFilename = uniqid() . '.' .$extension ;
                $file->move($this->getParameter('kernel.project_dir').'/public/uploads/products', $newFilename);
                $media->setPath('/uploads/products/' . $newFilename);
                $media->setAlt($originalFilename);
                $media->setProduct($product);
                $media->setType($extension);
                $this->em->persist($media);
                $product->addMedium($media);
            }
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_handle_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('handle_product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_handle_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('handle_product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_handle_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);


        
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('path')->getData();
            if ($file) {
                $media = new Media();
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->guessExtension();
                $newFilename = uniqid() . '.' .$extension ;
                $file->move($this->getParameter('kernel.project_dir').'/public/uploads/products', $newFilename);
                $media->setPath('/uploads/products/' . $newFilename);
                $media->setAlt($originalFilename);
                $media->setProduct($product);
                $media->setType($extension);
                $this->em->persist($media);
                $product->addMedium($media);
            }
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_handle_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('handle_product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_handle_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_handle_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
