<?php

declare(strict_types=1);

namespace App\Dto;

use App\Validator as AppAssert;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(
 *     required={"name", "latitude", "longitude", "address1", "postal_code", "city"},
 *     @OA\Property(property="longitude", nullable=false),
 *     @OA\Property(property="address1", nullable=false),
 *     @OA\Property(property="postal_code", nullable=false),
 *     @OA\Property(property="city", nullable=false)
 * )
 */
class Shop
{
    /**
     * @Groups({"shop:read"})
     */
    public ?int $id = null;

    /**
     * @Groups({"shop:read", "shop:write"})
     * @OA\Property(nullable=false, type="string")
     */
    #[Assert\NotBlank]
    public ?string $name = null;

    /**
     * @Groups({"shop:read", "shop:write"})
     * @OA\Property(nullable=false, type="number")
     */
    #[Assert\NotBlank]
    public ?float $latitude = null;

    /**
     * @Groups({"shop:read", "shop:write"})
     * @OA\Property(nullable=false, type="number")
     */
    #[Assert\NotBlank]
    public ?float $longitude = null;

    /**
     * Used by Elasticsearch.
     *
     * @see https://elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-distance-query.html
     *
     * @Groups({"shop:index"})
     */
    public ?string $location = null;

    /**
     * @Groups({"shop:read", "shop:write"})
     * @OA\Property(nullable=false, type="string")
     */
    #[Assert\NotBlank]
    public ?string $address1 = null;

    /**
     * @Groups({"shop:read", "shop:write"})
     * @OA\Property(nullable=false, type="string")
     */
    #[Assert\NotBlank]
    public ?string $postalCode = null;

    /**
     * @Groups({"shop:read", "shop:write"})
     * @OA\Property(nullable=false, type="string")
     */
    #[Assert\NotBlank]
    public ?string $city = null;

    /**
     * @Groups({"shop:read", "shop:write"})
     * @OA\Property(nullable=false, type="integer", description="Manager identifier")
     */
    #[Assert\NotBlank]
    #[AppAssert\ManagerExists]
    public ?int $manager = null;
}
