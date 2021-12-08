<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Product;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            (new Category())->setName("Lessive"),
            (new Category())->setName("Detergent") 
        ];
        $manager->persist($categories[0]);
        $manager->persist($categories[1]);

        $products = ["Persil", "Ariel", "OMO", "Dash", "Skip"];
        
        foreach ($products as $key => $value) {
            $product = new Product();
            $product->setName($products[rand(0,count($products)-1)]);
    
            // relation
            $product->setCategory($categories[rand(0,1)]);
            
            $manager->persist($product);
        }
        $manager->flush();
    }
}
