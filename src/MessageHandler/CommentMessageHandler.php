<?php


namespace App\MessageHandler;


use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;


class CommentMessageHandler implements MessageHandlerInterface
{
    private $entityManager;
    private $commentRepository;
    private $bus;
    private $mailer;
    private $adminEmail;
    private $workflow;
    private $logger;

    /**
     * CommentMessageHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param CommentRepository $commentRepository
     * @param MessageBusInterface $bus
     * @param MailerInterface $mailer
     * @param $adminEmail
     * @param WorkflowInterface $commentStateMachine
     * @param LoggerInterface|null $logger
     */
    public function __construct(EntityManagerInterface $entityManager,
                                CommentRepository $commentRepository,
                                MessageBusInterface $bus, WorkflowInterface $commentStateMachine,
                                MailerInterface $mailer, string $adminEmail,
                                LoggerInterface $logger = null)
    {
        $this->entityManager = $entityManager;
        $this->commentRepository = $commentRepository;
        $this->bus = $bus;
        $this->workflow = $commentStateMachine;
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
        $this->logger = $logger;
    }

    /**
     * @param CommentMessage $message
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
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
        $this->mailer->send((new NotificationEmail())
            ->subject('New comment posted')
            ->htmlTemplate('emails/comment_notification.html.twig')
            ->from($this->adminEmail)
            ->to($this->adminEmail)
            ->context(['comment' => $comment])
        );
        $this->entityManager->flush();
    }

}