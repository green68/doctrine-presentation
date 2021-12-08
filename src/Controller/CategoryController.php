<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    #[Route('/categories', name: 'categories_list')]
    public function list(ManagerRegistry $doctrine): Response
    {
        /** @var array $categories */
        $categories = $doctrine->getRepository(Category::class)->findAll();

        // le dump provoque une erreur sur products
        $test = [1,2,3,4];
        dump($test);
        dump($categories);

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/remove-products/{id}', name: 'categories_products_remove')]
    public function removeProducts(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        /** @var Category $category */
        $category = $doctrine->getRepository(Category::class)->find($id);
        $products = $category->getProducts();

        /** @var Product $value */
        foreach ($products as $key => $value) {
            $category->removeProduct($value);
        }
        $entityManager->persist($category);
        $entityManager->flush();

        dump($category, $products);

        return $this->redirectToRoute('categories_list'); 
    }
}
