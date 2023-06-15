<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use App\Entity\Lesson;
use App\Entity\Person;
use App\Entity\Registration;
use App\Entity\Training;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class DataController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/database/data', name: 'fill_data')]
    public function fillData(EntityManagerInterface $entityManager): Response
    {
        // Create Person
        $person = new Person();
        $person->setEmail('test@gmail.com');
        $person->setPassword('$2y$13$AgjOO.YuSAw9h8H1vTa.Ces1B5tMEk4JW7WfX.Il5pOHBfK1SP4gi'); //wachtwoord0
        $person->setFirstName('Bot');
        $person->setLastName('Dot');
        $person->setRoles(['ROLE_MEMBER']);

        $person1 = new Person();
        $person1->setEmail('michall.kakoll@gmail.com');
        $person1->setPassword('$2y$13$k0kD/hCvif.8cm/CUKRW0efLzsgQj4ekcdDB63qihitzoAfwyir9i'); //wachtwoord1
        $person1->setFirstName('Michal');
        $person1->setLastName('Kakol');
        $person1->setRoles(['ROLE_ADMIN']);
        $person1->setStreet('Borneostraat 34');
        $person1->setPlace('Amsterdam');

        $person2 = new Person();
        $person2->setEmail('iris.soldaat@gmail.com');
        $person2->setPassword('$2y$13$lJUtlF1jbiS6R98/1QBnme5VstIcTQZIngrkgUaxV2gi2aN7Fc6mK'); //wachtwoord2
        $person2->setFirstName('Iris');
        $person2->setLastName('Soldaat');
        $person2->setRoles(['ROLE_INSTRUCTOR']);
        $person2->setStreet('Haagsestraat 39');
        $person2->setPlace('Den Haag');

        // Create Training
        $training = new Training();
        $training->setDescription('Kickboxing');
        $training->setDuration(60);
        $training->setExtraCost(10);

        $training1 = new Training();
        $training1->setDescription('Karate');
        $training1->setDuration(90);
        $training1->setExtraCost(15);

        $training2 = new Training();
        $training2->setDescription('Judo');
        $training2->setDuration(120);

        // Create Lesson
        $lesson = new Lesson();
        $lesson->setTraining($training);
        $lesson->setTimes(new \DateTime('09:00:00'));
        $lesson->setDates(new \DateTime());
        $lesson->setLocation('Den Haag');
        $lesson->setMaxPeople(10);
        $lesson->setInstructor($person2);

        $lesson1 = new Lesson();
        $lesson1->setTraining($training1);
        $lesson1->setTimes(new \DateTime('10:00:00'));
        $lesson1->setDates(new \DateTime('2023-06-21'));
        $lesson1->setLocation('Amsterdam');
        $lesson1->setMaxPeople(10);
        $lesson1->setInstructor($person2);

        $lesson2 = new Lesson();
        $lesson2->setTraining($training2);
        $lesson2->setTimes(new \DateTime('14:00:00'));
        $lesson2->setDates(new \DateTime('2023-06-22'));
        $lesson2->setLocation('Rotterdam');
        $lesson2->setMaxPeople(15);
        $lesson2->setInstructor($person2);

        // Create Registration
        $registration = new Registration();
        $registration->setPerson($person);
        $registration->setLesson($lesson);
        $registration->setPayment('100.00');

        $registration1 = new Registration();
        $registration1->setPerson($person1);
        $registration1->setLesson($lesson1);
        $registration1->setPayment('100.00');

        $registration2 = new Registration();
        $registration2->setPerson($person2);
        $registration2->setLesson($lesson2);
        $registration2->setPayment('150.00');

        // Add entities to relationships
        $lesson->addRegistration($registration);
        $person->addRegistration($registration);
        $person->addInstructor($lesson);


        // Persist entities
        $entityManager->persist($person);
        $entityManager->persist($person1);
        $entityManager->persist($person2);
        $entityManager->persist($training);
        $entityManager->persist($training1);
        $entityManager->persist($training2);
        $entityManager->persist($lesson);
        $entityManager->persist($lesson1);
        $entityManager->persist($lesson2);
        $entityManager->persist($registration);
        $entityManager->persist($registration1);
        $entityManager->persist($registration2);
        $entityManager->flush();

        return new Response('Data filled successfully.');
    }
    #[Route('/database/dub', name: 'dublicate')]
    public function removeDuplicates(EntityManagerInterface $entityManager): Response
    {
        // Define the entity classes and the unique column combinations to check for duplicates
        $entities = [
            Person::class => ['login_name'],
            Training::class => ['description'],
            Lesson::class => ['training'],
            Registration::class => ['person'],
        ];

        $deletedDuplicates = 0;

        // Loop through each entity
        foreach ($entities as $entityClass => $uniqueColumns) {
            // Get the repository for the entity
            $repository = $entityManager->getRepository($entityClass);

            // Get the alias for the entity
            $alias = strtolower(substr($entityClass, strrpos($entityClass, '\\') + 1, 1));

            // Create the query builder
            $queryBuilder = $repository->createQueryBuilder($alias);

            // Add the select and group by clauses
            $queryBuilder->select($alias)
                ->groupBy(implode(', ', array_map(fn($column) => "$alias.$column", $uniqueColumns)))
                ->having('COUNT(DISTINCT ' . $alias . ') > 1');

            // Get the query
            $query = $queryBuilder->getQuery();

            // Get the duplicate entities
            $duplicates = $query->getResult();

            // Remove duplicate entities
            foreach ($duplicates as $duplicate) {
                $entityManager->remove($duplicate);
                $deletedDuplicates++;
            }
        }

        // Flush the changes to the database
        $entityManager->flush();

        return new Response($deletedDuplicates . ' duplicates removed.');
    }

    #[Route('/database/delete', name: 'delete')]
    public function clearAction(EntityManagerInterface $entityManager): Response
    {
        $entityClasses = [Person::class, Lesson::class, Registration::class, Training::class]; // Add your entity classes

        foreach ($entityClasses as $entityClass) {
            $repository = $entityManager->getRepository($entityClass);
            $entities = $repository->findAll();

            foreach ($entities as $entity) {
                $entityManager->remove($entity);
            }

            $entityManager->flush();
        }

        return new Response('Data cleared successfully');
    }
}