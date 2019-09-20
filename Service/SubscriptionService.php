<?php


namespace Yosimitso\WorkingForumBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\TranslatorInterface;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Templating;
use Symfony\Component\Templating\EngineInterface;
use Yosimitso\WorkingForumBundle\Entity\Subscription;

/**
 * Class Subscription
 *
 * @package Yosimitso\WorkingForumBundle\Service
 */
class SubscriptionService
{
    /**
     * @var EntityManager
     */
    private $em;
    /**
     * @var Swift_Mailer
     */
    private $mailer;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var string
     */
    private $siteTitle;
    /**
     * @var string
     */
    private $senderAddress;
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * Subscription constructor.
     * @param EntityManager $em
     * @param Swift_Mailer $mailer
     * @param TranslatorInterface $translator
     * @param string $siteTitle
     * @param string $senderAddress
     * @param EngineInterface $templating
     */
    public function __construct(EntityManager $em, Swift_Mailer $mailer, TranslatorInterface $translator, string $siteTitle, string $senderAddress, EngineInterface $templating)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->siteTitle = $siteTitle;
        $this->senderAddress = $senderAddress;
        $this->templating = $templating;

    }

    /**
     * Notify subscribed users of a new post
     * @param $post
     * @return bool
     * @throws \Exception
     */
    public function notifySubscriptions($post)
    {
        if (is_null($post->getThread())) {
            return;
        }
        $notifs = $this->em->getRepository(Subscription::class)->findByThread($post->getThread()->getId());
        if (!count($notifs)) {
            return;
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
                } catch (phpmailerException $e) {
                    throw new \Exception($e->errorMessage());
                } catch (Exception $e) {
                    throw new \Exception($e->getMessage());
                }
            }

            return true;
        }
    }

    /**
     * Get translated variable for email content
     * @param $subforum
     * @param $thread
     * @param $post
     * @param $user
     * @return array
     */
    private function getEmailTranslation($subforum, $thread, $post, $user)
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