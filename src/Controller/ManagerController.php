<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransformer\ManagerDataTransformer;
use App\Dto\Manager;
use App\Repository\ORM\ManagerRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ManagerController extends AbstractController
{
    /**
     * Get manager.
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a manager",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Manager::class, groups={"manager:read"}))
     *     )
     * )
     * @OA\Response (
     *     response=404,
     *     description="Returns if manager with the given ID does not exist."
     * )
     * @OA\Response (
     *     response=400,
     *     description="Returns 400 when parameters are invalid."
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Manager ID"
     * )
     * @OA\Tag(name="manager")
     */
    #[Route('/api/managers/{id}', name: 'get_manager', methods: ['GET'])]
    public function read(int $id, SerializerInterface $serializer, ManagerRepository $managerRepository, ManagerDataTransformer $managerDataTransformer): Response
    {
        $manager = $managerRepository->find($id);

        if (null === $manager) {
            return new JsonResponse([
                'code' => 404,
                'message' => sprintf('Manager with ID "%s" does not exist', (string) $id),
            ], 404);
        }

        $dto = $managerDataTransformer->reverseTransform($manager);

        return JsonResponse::fromJsonString($serializer->serialize($dto, 'json', [
            AbstractNormalizer::GROUPS => 'manager:read',
        ]));
    }
}
