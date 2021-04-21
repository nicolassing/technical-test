<?php

declare(strict_types=1);

namespace App\Command;

use App\DataTransformer\ShopDataTransformer;
use App\Repository\ORM\ShopRepository;
use Elastica\Document as ElasticaDocument;
use JoliCode\Elastically\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ElasticPopulateCommand extends Command
{
    protected static $defaultName = 'app:elastic:populate';
    protected static string $defaultDescription = 'Add a short description for your command';
    protected Client $client;
    protected ShopRepository $shopRepository;
    protected NormalizerInterface $serializer;
    protected ShopDataTransformer $shopDataTransformer;
    protected string $shopIndexName;

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription);
    }

    public function __construct(
        Client $client,
        ShopRepository $shopRepository,
        NormalizerInterface $serializer,
        ShopDataTransformer $shopDataTransformer,
        string $shopIndexName
    ) {
        $this->client = $client;
        $this->shopRepository = $shopRepository;
        $this->serializer = $serializer;
        $this->shopDataTransformer = $shopDataTransformer;
        $this->shopIndexName = $shopIndexName;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Starting populate elasticsearch index');

        $shops = $this->shopRepository->findAll();

        $indexBuilder = $this->client->getIndexBuilder();
        $indexer = $this->client->getIndexer();
        $index = $indexBuilder->createIndex($this->shopIndexName);

        foreach ($shops as $shop) {
            $dto = $this->shopDataTransformer->reverseTransform($shop);
            $data = $this->serializer->normalize($dto, null, [
                AbstractNormalizer::GROUPS => ['shop:read', 'shop:index'],
            ]);
            $indexer->scheduleIndex(
                $index,
                new ElasticaDocument((string) $dto->id, $data)
            );
        }

        $indexer->flush();
        $indexBuilder->markAsLive($index, $this->shopIndexName);
        $indexBuilder->purgeOldIndices($this->shopIndexName);

        $io->success('Elasticsearch index succcessfully populated');

        return Command::SUCCESS;
    }
}
