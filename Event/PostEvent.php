<?php

namespace Yosimitso\WorkingForumBundle\Event;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Yosimitso\WorkingForumBundle\Entity\User;
use Yosimitso\WorkingForumBundle\Entity\Post;

class PostEvent
{
    private $floodLimit;
    private $translator;
    
    public function __construct($floodLimit, $translator)
    {
        $this->floodLimit = $floodLimit;
        $this->translator = $translator;
    }
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Post) {
            return;
        }

        $dateNow = new \DateTime('now');
        $floodLimit = new \DateTime('-'.$this->floodLimit.' seconds');

        if (!is_null($entity->getUser()->getLastReplyDate()) && $floodLimit <= $entity->getUser()->getLastReplyDate()) { // USER IS FLOODING
            throw new \Exception($this->translator->trans('forum.error_flood', ['%second%' => $this->floodLimit], 'YosimitsoWorkingForumBundle'));
        }

        $entity->getUser()->setLastReplyDate($dateNow);

        return;
    }
}