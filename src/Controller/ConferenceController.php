<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class ConferenceController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param ConferenceRepository $conferenceRepository
     * @param Environment $twig
     * @return Response
     * @throws
     */
    public function index(Environment $twig, ConferenceRepository $conferenceRepository)
    {
        return new Response($twig->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]));
    }

    /**
     * @Route("/conference/{id}", name="conference")
     * @param CommentRepository $commentRepository
     * @param Conference $conference
     * @param Environment $twig
     * @return Response
     * @throws
     */
    public function show(Environment $twig, Conference $conference, CommentRepository $commentRepository)
    {
        return new Response($twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $commentRepository->findBy(['conference' => $conference], ['createdAt' => 'DESC']),
        ]));
    }
}
