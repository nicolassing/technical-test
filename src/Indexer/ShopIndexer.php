<?php

declare(strict_types=1);

namespace App\Indexer;

use App\DataTransformer\ShopDataTransformer;
use App\Entity\Shop;
use Elastica\Document as ElasticaDocument;
use JoliCode\Elastically\Client;
use JoliCode\Elastically\Index;
use JoliCode\Elastically\Indexer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ShopIndexer
{
    protected Index $index;
    protected Indexer $indexer;
    protected NormalizerInterface $serializer;
    protected ShopDataTransformer $shopDataTransformer;

    public function __construct(Client $client, NormalizerInterface $serializer, ShopDataTransformer $shopDataTransformer, string $shopIndexName)
    {
        $this->index = $client->getIndex($shopIndexName);
        $this->indexer = $client->getIndexer();
        $this->serializer = $serializer;
        $this->shopDataTransformer = $shopDataTransformer;
    }

    public function index(Shop $shop, bool $andFlush = true): void
    {
        $dto = $this->shopDataTransformer->reverseTransform($shop);
        $data = $this->serializer->normalize($dto, null, [
            AbstractNormalizer::GROUPS => ['shop:index', 'shop:read'],
        ]);
        $this->indexer->scheduleIndex(
            $this->index,
            new ElasticaDocument((string) $dto->id, $data)
        );

        if (true === $andFlush) {
            $this->indexer->flush();
        }
    }

    public function remove(Shop $shop, bool $andFlush = true): void
    {
        $this->indexer->scheduleDelete($this->index, (string) $shop->getId());

        if (true === $andFlush) {
            $this->indexer->flush();
        }
    }
}
