<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use App\Repository\TrainingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(TrainingRepository $trainingRepository): Response
    {
        $trainings = $trainingRepository->findAll();

        return $this->render('home/contact.html.twig', [
            'controller_name' => 'HomeController',
            'trainings' => $trainings,
        ]);
    }
    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('home/login.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
    #[Route('/redirect', name: 'redirect')] # CHECK login.html.twig for part 2
    public function RedirectAction(Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin');
        }
        if ($security->isGranted('ROLE_MEMBER')) {
            return $this->redirectToRoute('app_member');
        }
        if ($security->isGranted('ROLE_INSTRUCTOR')) {
            return $this->redirectToRoute('app_instructor');
        }
        return $this->redirectToRoute('app_default');
    }
}
