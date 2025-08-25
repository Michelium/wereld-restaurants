<?php

namespace App\EventSubscriber;

use App\Entity\Restaurant;
use App\Enum\RestaurantFieldSource;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This subscriber listens to EasyAdmin events to automatically manage the fieldSources of Restaurant entities.
 * It marks fields as "admin" when a restaurant is created or updated via the EasyAdmin interface.
 *
 * This way, we can track which fields were set or modified by an admin user.
 */
readonly class EasyAdminRestaurantFieldSourceSubscriber implements EventSubscriberInterface {

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public static function getSubscribedEvents(): array {
        return [
            BeforeEntityPersistedEvent::class => 'onPersist',
            BeforeEntityUpdatedEvent::class => 'onUpdate',
        ];
    }

    /**
     * This method is called before a NEW entity is persisted in EasyAdmin.
     * It marks all filled fields as "admin" for a newly created restaurant.
     */
    public function onPersist(BeforeEntityPersistedEvent $event): void {
        $entity = $event->getEntityInstance();
        if (!$entity instanceof Restaurant) {
            return;
        }

        // New restaurant created by admin: mark all filled fields as "admin"
        $fields = ['name', 'street', 'houseNumber', 'postalCode', 'city', 'website', 'country', 'latitude', 'longitude'];
        $entity->setFieldSources($fields, new \DateTimeImmutable(), RestaurantFieldSource::ADMIN);
    }

    /**
     * This method is called before an entity is updated in EasyAdmin.
     * It checks which fields have changed and updates the fieldSources accordingly.
     */
    public function onUpdate(BeforeEntityUpdatedEvent $event): void {
        $entity = $event->getEntityInstance();
        if (!$entity instanceof Restaurant) return;

        $uow = $this->entityManager->getUnitOfWork();
        $meta = $this->entityManager->getClassMetadata(Restaurant::class);

        $uow->recomputeSingleEntityChangeSet($meta, $entity);

        $changeSet = $uow->getEntityChangeSet($entity);
        if (!$changeSet) return;

        $changedFields = array_keys($changeSet);

        $old = $entity->getFieldSources();
        $entity->setFieldSources($changedFields, new \DateTimeImmutable(), RestaurantFieldSource::ADMIN);
        $new = $entity->getFieldSources();

        if ($old !== $new) {
            $uow->propertyChanged($entity, 'fieldSources', $old, $new);
            $uow->scheduleForUpdate($entity);
            $uow->recomputeSingleEntityChangeSet($meta, $entity);
        }
    }

}
