<?php

namespace App\EventListener;

use App\Entity\Trait\EntityLifecycleTrait;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
class EntityLifecycleTraitHandler {

    public function prePersist(PrePersistEventArgs $eventArgs): void {
        $this->setTraitValues($eventArgs);
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs): void {
        $this->setTraitValues($eventArgs);
    }

    private function setTraitValues(LifecycleEventArgs $args): void {
        $entity = $args->getObject();

        if (!in_array(EntityLifecycleTrait::class, array_keys(class_uses($entity)))) {
            return;
        }

        /** @var EntityLifecycleTrait $entity */
        if (empty($entity->getCreatedAt())) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        $entity->setUpdatedAt(new \DateTimeImmutable());
    }

}