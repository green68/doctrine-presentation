<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'products_list')]
    public function list(ManagerRegistry $doctrine): Response
    {

        $products = $doctrine->getRepository(Product::class)->findAll();

        // le dump provoque une erreur sur products
        $test = [1,2,3,4];
        dump($test);
        // dump($products);
        // dd($test, $products);

        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    // #[Route('/product/categories/{id}', name: 'product_categories_del')]
    // public function create(int $id, ManagerRegistry $doctrine): Response
    // {
    //     $msgs = [];

    //     $product = $doctrine->getRepository(Product::class)->find($id);
    //     $category = $product->getCategory();
        
    //     foreach ($variable as $key => $value) {
    //         # code...
    //     }

    //     $entityManager->persist($category);
    //     $entityManager->persist($product);

    //     $entityManager->flush();

    //     $msgs[] = "Sauvegarde d'un produit id:".$product->getId().
    //     " et une nouvelle category id:".$category->getId();

    //     return $this->render('product/message.html.twig', [
    //         'msgs' => $msgs,
    //     ]);
    // }

    #[Route('/product/show/{id}', name: 'product_show')]
    public function show(int $id,ManagerRegistry $doctrine): Response
    {

        /** @var ProductRepository $productRepository */
        $productRepository = $doctrine->getRepository(Product::class);
        $product = $productRepository->findOneByIdJoinedToCategory($id);
        $category = $product->getCategory();
        
        dump(get_class($category));

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'category' => $product->getCategory(),
        ]);
    }

    #[Route('/product/showlazy/{id}', name: 'product_show_lazy')]
    public function showLazy(int $id, ManagerRegistry $doctrine): Response
    {
        $product = $doctrine->getRepository(Product::class)->find($id);
        $category = $product->getCategory();
        
        dump(get_class($category));

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'category' => $product->getCategory(),
            'lazy' => true,
        ]);
    }
}
