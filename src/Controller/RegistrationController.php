<?php

namespace App\Controller;

use App\Entity\Registration;

use App\Repository\LessonRepository;
use App\Entity\Lesson;
use Symfony\Component\Security\Core\Security;
use App\Form\RegistrationType;
use App\Repository\RegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/registration')]
class RegistrationController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_registration_index', methods: ['GET'])]
    public function index(RegistrationRepository $registrationRepository): Response
    {
        $loggedInPerson = $this->security->getUser();
        $registrations = $registrationRepository->findBy(['person' => $loggedInPerson]);

        return $this->render('registration/index.html.twig', [
            'registrations' => $registrations,
        ]);
    }

    #[Route('/new', name: 'app_registration_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RegistrationRepository $registrationRepository): Response
    {
        $registration = new Registration();
        $form = $this->createForm(RegistrationType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrationRepository->save($registration, true);

            return $this->redirectToRoute('app_registration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('registration/new.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_registration_show', methods: ['GET'])]
    public function show(Registration $registration): Response
    {
        return $this->render('registration/show.html.twig', [
            'registration' => $registration,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_registration_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Registration $registration, RegistrationRepository $registrationRepository): Response
    {
        $form = $this->createForm(RegistrationType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registrationRepository->save($registration, true);

            return $this->redirectToRoute('app_registration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('registration/edit.html.twig', [
            'registration' => $registration,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_registration_delete', methods: ['POST'])]
    public function delete(Request $request, Registration $registration, RegistrationRepository $registrationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$registration->getId(), $request->request->get('_token'))) {
            $registrationRepository->remove($registration, true);
        }

        return $this->redirectToRoute('app_registration_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/new/{lessonId}', name: 'app_registration_new_lesson', methods: ['GET', 'POST'])]
    public function newLesson(Request $request, RegistrationRepository $registrationRepository, LessonRepository $lessonRepository, int $lessonId): Response
    {
        $lesson = $lessonRepository->find($lessonId);
        $registration = new Registration();
        $form = $this->createForm(RegistrationType::class, $registration);
        $form->handleRequest($request);
        $form = $this->createForm(RegistrationType::class, null, [
            'lesson' => $lesson,
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set the lesson for the registration
            $registration->setLesson($lesson);

            // Save the registration to the database
            $registrationRepository->save($registration, true);

            return $this->redirectToRoute('app_registration_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('registration/new.html.twig', [
            'registration' => $registration,
            'form' => $form,
            'lesson' => $lesson,
        ]);
    }

}
