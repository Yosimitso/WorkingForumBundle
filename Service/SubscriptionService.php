<?php


namespace Yosimitso\WorkingForumBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Contracts\Translation\TranslatorInterface;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Templating;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;
use Yosimitso\WorkingForumBundle\Entity\Post;
use Yosimitso\WorkingForumBundle\Entity\Subforum;
use Yosimitso\WorkingForumBundle\Entity\Subscription;
use Yosimitso\WorkingForumBundle\Entity\Thread;
use Yosimitso\WorkingForumBundle\Entity\UserInterface;

/**
 * Class Subscription
 *
 * @package Yosimitso\WorkingForumBundle\Service
 */
class SubscriptionService
{
    private EntityManager $em;
    private Swift_Mailer $mailer;
    private TranslatorInterface $translator;
    private string $siteTitle;
    private ?string $senderAddress;
    private Environment $templating;

    public function __construct(
        EntityManager $em,
        Swift_Mailer $mailer,
        TranslatorInterface $translator,
        string $siteTitle,
        Environment $templating,
        ?string $senderAddress)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->siteTitle = $siteTitle;
        $this->senderAddress = $senderAddress;
        $this->templating = $templating;

        if (empty($this->senderAddress)) {
            trigger_error('The parameter "swiftmailer.sender_address" is empty, email delivering might failed');
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
                        $email = (new \Swift_Message())
                            ->setSubject($this->translator->trans('subscription.emailNotification.subject', $emailTranslation, 'YosimitsoWorkingForumBundle'))
                            ->setFrom($this->senderAddress)
                            ->setTo($notif->getUser()->getEmailAddress())
                            ->setBody(
                                $this->templating->render(
                                    '@YosimitsoWorkingForum/Email/notification_new_message_en.html.twig',
                                    $emailTranslation
                                ),
                                'text/html');

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
