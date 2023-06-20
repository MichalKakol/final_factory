<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Security;
use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/memberslesson')]
class MembersLessonController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'member_lesson_index', methods: ['GET'])]
    public function index(LessonRepository $lessonRepository): Response
    {
        $lessons = $lessonRepository->findAll();

        return $this->render('member/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }
}
