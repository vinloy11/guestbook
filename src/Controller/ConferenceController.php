<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class ConferenceController extends AbstractController
{
    private $twig;
    private $entityManager;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="homepage")
     * @param ConferenceRepository $conferenceRepository
     * @return Response
     * @throws
     */
    public function index(ConferenceRepository $conferenceRepository)
    {
        return new Response($this->twig->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]));
    }


    /**
     * @Route("/conference/{slug}", name="conference")
     * @param Conference $conference
     * @param CommentRepository $commentRepository
     * @return Response
     * @param Request $request
     * @param string $photoDir
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function show(Request $request, Conference $conference,
                         CommentRepository $commentRepository, string $photoDir)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);
            if ($photo = $form['photo']->getData()) {
                $filename = bin2hex(random_bytes(6)).'.'.$photo->guessExtension();
                try {
                    $photo->move($photoDir, $filename);
                } catch (FileException $e) {

                }

                $comment->setPhotoFilename($filename);
            }
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('conference', ['slug' => $conference->getSlug()]);
        }
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);
        return new Response($this->twig->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - $commentRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + $commentRepository::PAGINATOR_PER_PAGE),
            'comment_form' => $form->createView(),
        ]));
    }
}
