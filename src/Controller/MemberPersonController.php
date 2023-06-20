<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/memberperson')]
class MemberPersonController extends AbstractController
{
    #[Route('/', name: 'member_person_index', methods: ['GET'])]
    public function index(PersonRepository $personRepository, Security $security): Response
    {
        $user = $security->getUser();

        return $this->render('member/indexx.html.twig', [
            'people' => $personRepository->findBy(['email' => $user->getEmail()]),
        ]);
    }

    #[Route('/{id}/edit', name: 'member_person_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Person $person, PersonRepository $personRepository, Security $security): Response
    {
        $user = $security->getUser();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($person->getEmail() !== $user->getEmail()) {
            throw $this->createAccessDeniedException('Access denied.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $personRepository->save($person, true);

            return $this->redirectToRoute('member_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('member/editt.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }
}

