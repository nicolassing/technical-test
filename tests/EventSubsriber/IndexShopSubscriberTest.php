<?php

namespace App\Tests\EventSubsriber;

use App\Entity\Shop;
use App\EventSubscriber\IndexShopSubscriber;
use App\Indexer\ShopIndexer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IndexShopSubscriberTest extends TestCase
{
    protected IndexShopSubscriber $subscriber;
    protected ShopIndexer | MockObject $shopIndexer;

    protected function setUp(): void
    {
        $this->shopIndexer = $this->createMock(ShopIndexer::class);
        $this->subscriber = new IndexShopSubscriber($this->shopIndexer);
    }

    public function testPostRemove(): void
    {
        $shop = new Shop();
        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')
            ->willReturn($shop);

        $this->shopIndexer->expects(self::once())
            ->method('remove')
            ->with($shop);

        $this->subscriber->postRemove($eventArgs);
    }

    public function testPostPersist(): void
    {
        $shop = new Shop();
        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')
            ->willReturn($shop);

        $this->shopIndexer->expects(self::once())
            ->method('index')
            ->with($shop);

        $this->subscriber->postPersist($eventArgs);
    }

    public function testPostUpdate(): void
    {
        $shop = new Shop();
        $eventArgs = $this->createMock(LifecycleEventArgs::class);
        $eventArgs->method('getObject')
            ->willReturn($shop);

        $this->shopIndexer->expects(self::once())
            ->method('index')
            ->with($shop);

        $this->subscriber->postUpdate($eventArgs);
    }
}
