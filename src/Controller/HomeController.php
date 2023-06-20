<?php

namespace App\Controller;

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use App\Repository\TrainingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(TrainingRepository $trainingRepository): Response
    {
        $trainings = $trainingRepository->findAll();

        return $this->render('home/contact.html.twig', [
            'trainings' => $trainings,
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request, PersonRepository $personRepository): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encodedPassword = $this->passwordEncoder->encodePassword($person, $person->getPassword());
            $person->setPassword($encodedPassword);

            $personRepository->save($person, true);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/redirect', name: 'redirect')]
    public function redirectAction(Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
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
