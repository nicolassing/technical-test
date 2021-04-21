<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Shop;
use App\Indexer\ShopIndexer;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class IndexShopSubscriber implements EventSubscriber
{
    protected ShopIndexer $shopIndexer;

    public function __construct(ShopIndexer $shopIndexer)
    {
        $this->shopIndexer = $shopIndexer;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postRemove,
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postRemove(LifecycleEventArgs $eventArgs): void
    {
        $object = $eventArgs->getObject();

        if ($object instanceof Shop) {
            $this->shopIndexer->remove($object);
        }
    }

    public function postPersist(LifecycleEventArgs $eventArgs): void
    {
        $object = $eventArgs->getObject();

        if ($object instanceof Shop) {
            $this->shopIndexer->index($object);
        }
    }

    public function postUpdate(LifecycleEventArgs $eventArgs): void
    {
        $object = $eventArgs->getObject();

        if ($object instanceof Shop) {
            $this->shopIndexer->index($object);
        }
    }
}
