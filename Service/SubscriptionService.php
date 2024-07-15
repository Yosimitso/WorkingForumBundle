<?php


namespace Yosimitso\WorkingForumBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Subscription;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;


class SubscriptionService
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly MailerInterface $mailer,
        protected readonly TranslatorInterface $translator,
        protected readonly string $siteTitle,
        protected readonly Environment $templating,
        protected readonly ?string $senderAddress
    ) {
        if (empty($this->senderAddress)) {
            trigger_error('The parameter "yosimitso_working_forum.mailer_sender_address" is empty, email delivering might failed');
        }

    }

    /**
     * Notify subscribed users of a new post
     * @throws \Exception
     */
    public function notifySubscriptions(Post $post) : bool
    {
        if (is_null($post->getThread())) {
            return false;
        }
        $notifs = $this->em->getRepository(Subscription::class)->findBy(['thread' => $post->getThread()->getId()]);
        if (!count($notifs)) {
            return false;
        }
        $emailTranslation = $this->getEmailTranslation($post->getThread()->getSubforum(), $post->getThread(), $post, $post->getUser());
        
        if (!is_null($notifs)) {
            foreach ($notifs as $notif) {
                try {
                    if (!empty($notif->getUser()->getEmailAddress())) {
                        $email = (new Email())
                            ->subject($this->translator->trans('subscription.emailNotification.subject', $emailTranslation, 'YosimitsoWorkingForumBundle'))
                            ->from($this->senderAddress)
                            ->to($notif->getUser()->getEmailAddress())
                            ->html(
                                $this->templating->render(
                                    '@YosimitsoWorkingForum/Email/notification_new_message_en.html.twig',
                                    $emailTranslation
                                ));

                        $this->mailer->send($email);
                    }
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }

            return true;
        }
    }

    /**
     * Get translated variables for email content
     */
    private function getEmailTranslation(Subforum $subforum, Thread $thread, Post $post, UserInterface $user) : array
    {
        return [
            'siteTitle' => $this->siteTitle,
            'subforumName' => $subforum->getName(),
            'threadLabel' => $thread->getLabel(),
            'threadAuthor' => $thread->getAuthor()->getUsername(),
            'user' => $user,
            'thread' => $post->getThread(),
            'post' => $post,
            'postUser' => $post->getUser()
        ];
    }
}
