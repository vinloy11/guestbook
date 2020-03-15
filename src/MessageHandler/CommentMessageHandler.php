<?php


namespace App\MessageHandler;


use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;


class CommentMessageHandler implements MessageHandlerInterface
{
    private $spamChecker;
    private $entityManager;
    private $commentRepository;

    /**
     * CommentMessageHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param CommentRepository $commentRepository
     */
    public function __construct(EntityManagerInterface $entityManager, CommentRepository $commentRepository)
    {
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param CommentMessage $message
     */
    public function __invoke(CommentMessage $message)
    {
        $comment = $this->commentRepository->find($message->getId());

        if (!$comment) {
            return;
        }
//        if (2 === $this->spamChecker->getSpamScore($comment,
//                $message->getContext())) {
//            $comment->setState('spam');
//        } else {
//            $comment->setState('published');
//        }
        $this->entityManager->flush();
    }

}