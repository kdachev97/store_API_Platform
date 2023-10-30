<?php

namespace App\EntityListener;

use App\Entity\Alcohol;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Alcohol::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Alcohol::class)]
class AlcoholListener
{
    public function prePersist(Alcohol $alcohol)
    {
        $alcohol->setDateCreated(new \DateTime());
    }

    public function preUpdate(Alcohol $alcohol)
    {
        $alcohol->setDateEdited(new \DateTime());
    }
}
