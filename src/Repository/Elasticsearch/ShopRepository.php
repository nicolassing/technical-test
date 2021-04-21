<?php

declare(strict_types=1);

namespace App\Repository\Elasticsearch;

use Doctrine\Common\Collections\ArrayCollection;
use Elastica\Query;
use JoliCode\Elastically\Client;
use JoliCode\Elastically\Result;

class ShopRepository
{
    public static int $defaultDistance = 5000;
    protected string $shopIndexName;
    protected Client $client;

    public function __construct(Client $client, string $shopIndexName)
    {
        $this->client = $client;
        $this->shopIndexName = $shopIndexName;
    }

    /**
     * @param array<string, mixed> $criteria
     */
    public function search(array $criteria = []): ArrayCollection
    {
        $query = new Query();
        $boolQuery = new Query\BoolQuery();

        $q = $criteria['q'] ?? null;

        if ($q) {
            $match = new Query\MultiMatch();
            $match->setQuery((string) $q);
            $match->setFields(['name^2', 'name.stemmed']);
            $match->setFuzziness('AUTO');
            $boolQuery->addShould($match);
        }

        if (isset($criteria['lat'], $criteria['lon'])) {
            $geoDistance = new Query\GeoDistance(
                'location',
                ['lat' => (float) $criteria['lat'], 'lon' => (float) $criteria['lon']],
                (string) ($criteria['distance'] ?? self::$defaultDistance)
            );
            $boolQuery->addFilter($geoDistance);

            $query->addSort(['_geo_distance' => [
                'location' => ['lat' => (float) $criteria['lat'], 'lon' => (float) $criteria['lon']],
                'order' => 'asc',
                'unit' => 'km',
                'mode' => 'min',
                'distance_type' => 'arc',
                'ignore_unmapped' => true,
            ]]);
        }

        $query->setQuery($boolQuery);

        $collection = new ArrayCollection();
        $results = $this->client->getIndex($this->shopIndexName)
            ->search($query)
            ->getResults();

        /** @var Result $result */
        foreach ($results as $result) {
            $collection->add($result->getModel());
        }

        return $collection;
    }
}
