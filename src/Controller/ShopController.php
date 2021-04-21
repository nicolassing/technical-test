<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransformer\ShopDataTransformer;
use App\Dto\Shop;
use App\Repository\Elasticsearch\ShopRepository;
use App\Validator\ShopRequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ShopController extends AbstractController
{
    /**
     * List shops.
     *
     * You can make a fulltext search or search by distance from a given point.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns shops",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Shop::class, groups={"shop:read"}))
     *     )
     * )
     * @OA\Response (
     *     response=400,
     *     description="Returns 400 when parameters are invalid"
     * )
     * @OA\Parameter(
     *     name="q",
     *     in="query",
     *     description="Fulltext search",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="lat",
     *     in="query",
     *     description="Latitude",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="lon",
     *     in="query",
     *     description="longitude",
     *     @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     name="distance",
     *     in="query",
     *     description="Max distance between a point and shops",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="shop")
     */
    #[Route('/api/shops', name: 'list_shop', methods: ['GET'])]
    public function list(Request $request, SerializerInterface $serializer, ShopRepository $shopRepository, ShopRequestValidator $validator): Response
    {
        $criteria = [
            'q' => $request->query->get('q'),
            'lat' => $request->query->has('lat') ? (float) $request->query->get('lat') : null,
            'lon' => $request->query->has('lon') ? (float) $request->query->get('lon') : null,
            'distance' => $request->query->has('distance') ? (int) $request->query->get('distance') : null,
        ];

        $violations = $validator->validate($criteria);

        if (\count($violations) > 0) {
            return JsonResponse::fromJsonString($serializer->serialize($violations, 'json'), 422);
        }

        $shops = $shopRepository->search($criteria);

        return JsonResponse::fromJsonString($serializer->serialize($shops, 'json', [
            AbstractNormalizer::GROUPS => 'shop:read',
        ]));
    }

    /**
     * Create a shop.
     *
     * @OA\Response (
     *     response=201,
     *     description="Returns the created shop",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Shop::class, groups={"shop:read"}))
     *     )
     * )
     * @OA\Response (
     *     response=400,
     *     description="Returns 400 when JSON is invalid or validation failed"
     * )
     * @OA\RequestBody(
     *     @OA\JsonContent(ref=@Model(type=Shop::class, groups={"shop:write"}))
     * )
     * @OA\Tag(name="shop")
     */
    #[Route('/api/shops', name: 'create_shop', methods: ['POST'], format: 'json')]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, ShopDataTransformer $shopDataTransformer): Response
    {
        try {
            $dto = $serializer->deserialize($request->getContent(), Shop::class, 'json', [
                AbstractNormalizer::GROUPS => ['shop:write'],
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ]);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'code' => 400,
                'message' => $exception->getMessage(),
            ], 400);
        }

        $violations = $validator->validate($dto);

        if (\count($violations) > 0) {
            return JsonResponse::fromJsonString($serializer->serialize($violations, 'json'), 400);
        }

        $entity = $shopDataTransformer->transform($dto);
        $em->persist($entity);
        $em->flush();
        $dto = $shopDataTransformer->reverseTransform($entity);

        return JsonResponse::fromJsonString($serializer->serialize($dto, 'json', [
            AbstractNormalizer::GROUPS => 'shop:read',
        ]), 201);
    }
}
