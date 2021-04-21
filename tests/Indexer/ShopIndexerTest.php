<?php

namespace App\Tests\Indexer;

use App\DataTransformer\ShopDataTransformer;
use App\Dto\Shop as ShopDto;
use App\Entity\Shop;
use App\Indexer\ShopIndexer;
use Elastica\Document;
use JoliCode\Elastically\Client;
use JoliCode\Elastically\Index;
use JoliCode\Elastically\Indexer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;

class ShopIndexerTest extends TestCase
{
    protected ShopIndexer $shopIndexer;
    protected Index | MockObject $index;
    protected Indexer | MockObject $indexer;
    protected NormalizerInterface | MockObject $serializer;
    protected ShopDataTransformer | MockObject $shopDataTransformer;

    protected function setUp(): void
    {
        $this->shopIndexer = $this->createMock(ShopIndexer::class);
        $this->index = $this->createMock(Index::class);
        $this->indexer = $this->createMock(Indexer::class);
        $this->serializer = $this->createMock(Serializer::class);
        $this->shopDataTransformer = $this->createMock(ShopDataTransformer::class);

        $client = $this->createMock(Client::class);
        $client->method('getIndex')->willReturn($this->index)->with('test_shops');
        $client->method('getIndexer')->willReturn($this->indexer);

        $this->shopIndexer = new ShopIndexer($client, $this->serializer, $this->shopDataTransformer, 'test_shops');
    }

    public function testIndex(): void
    {
        $shop = new Shop();
        $shop->setId(123);

        $shopDto = new ShopDto();
        $shopDto->id = 123;

        $this->shopDataTransformer->expects(self::once())
            ->method('reverseTransform')
            ->with($shop)
            ->willReturn($shopDto);

        $data = ['name' => 'Sezane'];
        $this->serializer->method('normalize')
            ->with($shopDto, null, ['groups' => ['shop:index', 'shop:read']])
            ->willReturn($data);

        $document = new Document('123', $data);
        $this->indexer->expects(self::once())
            ->method('scheduleIndex')
            ->with($this->index, $document);
        $this->indexer->expects(self::once())
            ->method('flush');

        $this->shopIndexer->index($shop);
    }

    public function testRemove(): void
    {
        $shop = new Shop();
        $shop->setId(123);
        $this->indexer->expects(self::once())
            ->method('scheduleDelete')
            ->with($this->index, '123');
        $this->indexer->expects(self::once())
            ->method('flush');

        $this->shopIndexer->remove($shop);
    }
}
