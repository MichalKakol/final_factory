<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InstructorController extends AbstractController
{
    #[Route('/instructor', name: 'app_instructor')]
    public function index(): Response
    {
        return $this->render('instructor/first.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}