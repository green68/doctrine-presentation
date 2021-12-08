<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Person;
use App\Repository\PersonRepository;

class PersonController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('person/index.html.twig', [
            'controller_name' => 'PersonController',
        ]);
    }

    #[Route('/person/create', name: 'person_create')]
    public function create(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $msgs = [];

        $person = (new Person())
            ->setFirstName('David')
            ->setLastName('Brylka');

        
        $entityManager->persist($person);

        $msgs[] = ["Création Person avant flush : id={$person->getId()} {$person->getFirstName()} {$person->getLastName()}"];
        $entityManager->flush();
        $msgs[] = ["tag" => "Création Person après flush : id={$person->getId()} {$person->getFirstName()} {$person->getLastName()}"];

        return $this->render('person/message.html.twig', [
            'messages' => $msgs
        ]);
    }
    
    #[Route('/person/delete/{id}', name: 'person_delete_id')]
    public function deleteId(int $id, ManagerRegistry $doctrine): Response
    {
        /** @var EntityManager $entityManager */
        $entityManager = $doctrine->getManager();
        $msgs = [];

        /** @var Person $person */
        $person = $entityManager->getRepository(Person::class)->find($id);

        // selection du mode de suppression : true ou false
        $parcours = true;
            
        if (!$person) {
            $msgs[] = ["pas de person à supprimer sous l'id $id"];
        } else {
            if($parcours) {
                $msgs[] = ["Suppression de person : id={$person->getId()} {$person->getFirstName()} {$person->getLastName()}"];
                $entityManager->remove($person);
                $entityManager->flush();

            } else {
                $query = $entityManager->createQuery(
                    "DELETE FROM App\Entity\Person p 
                    WHERE  p.id = '{$person->getId()}'"
                );
                $result = $query->getResult();
                $msgs[] = ["Suppression de $result Persons"];
            }

        }
        

        return $this->render('person/message.html.twig', [
            'messages' => $msgs
        ]);
    }
    
    #[Route('/persons/create', name: 'persons_create')]
    public function createList(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $msgs = [];
        
        $persons = [["Laurent","Mercier"], ["Eric", "Vercheval"]];
        foreach ($persons as $key => $value) {
            $person = new Person();
            $person->setFirstName($value[0]);
            $person->setLastName($value[1]);
            
            $msgs[] = ["Création Person : id={$person->getId()} {$person->getFirstName()} {$person->getLastName()}"];
            $entityManager->persist($person);
        }
        
        $entityManager->flush();
        
        return $this->render('person/message.html.twig', [
            'messages' => $msgs
        ]);
    }
    
    #[Route('persons/list', name: 'persons_list')]
    public function list(PersonRepository $personRepository): Response
    {
        
        $persons = $personRepository->findAll();
        
        // TODO : ne fonctionne pas, créé une erreur
        // dump($persons);

        return $this->render('person/list.html.twig', [
            'persons' => $persons
        ]);
    }
    
    #[Route('persons/delete/{parcours}', name: 'persons_delete')]
    public function deleteAll(string $parcours = "true", ManagerRegistry $doctrine): Response
    {
        $msgs = [];

        /** @var EntityManager $entityManager */
        $entityManager = $doctrine->getManager();
        
        $persons = $entityManager->getRepository(Person::class)->findAll();

        // selection du mode de suppression : true ou false
        if ($parcours == "true") {
            foreach ($persons as $person) {
                $msgs[] = ["Suppression de person : id={$person->getId()} {$person->getFirstName()} {$person->getLastName()}"];
                $entityManager->remove($person);
            }
            $entityManager->flush();
        } else {
            $query = $entityManager->createQuery(
                'DELETE FROM App\Entity\Person'
            );
            $result = $query->getResult();
            $msgs[] = ["Suppression de $result Persons"];
        }
        
        return $this->render('person/message.html.twig', [
            'messages' => $msgs
        ]);
    }
    

    /* Exemples du slide */
    #[Route('personcreate', name: 'person_create_one')]
    public function createOne(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        $person = (new Person())
            ->setFirstName("Prénom")
            ->setLastName("Nom");

        $entityManager->persist($person);
        $entityManager->flush();
        
        $text = "Création Person : id:{$person->getId()} {$person->getFirstName()} {$person->getLastName()}";
        return new Response($text);
    }
    

    #[Route('personread/{id}', name: 'person_read')]
    public function readOne(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        $person = $entityManager->getRepository(Person::class)->find($id);
        if (!$person) {
            return new Response("id non valide", 400);
            // throw $this->createNotFoundException("id non valide");
        }
        $text = "Lecture Person : id:{$person->getId()} {$person->getFirstName()} {$person->getLastName()}";
        return new Response($text);
    }

    #[Route('personupdate/{id}', name: 'person_update')]
    public function updateOne(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        $person = $entityManager->getRepository(Person::class)->find($id);
        if (!$person) {
            throw $this->createNotFoundException("id non valide");
        }
        $person->setLastName("UnAutreNom");
        $entityManager->persist($person);
        $entityManager->flush();

        $text = "Màj Person : id:{$person->getId()} {$person->getFirstName()} {$person->getLastName()}";
        return new Response($text);
    }

    #[Route('persondelete/{id}', name: 'person_delete_one')]
    public function delete(int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        $person = $entityManager->getRepository(Person::class)->find($id);
        if (!$person) {
            throw $this->createNotFoundException("id non valide");
        }
        $entityManager->remove($person);
        $entityManager->flush();

        $text = "Suppression Person : id:{$person->getId()} {$person->getFirstName()} {$person->getLastName()}";
        return new Response($text);
    }
    
}
