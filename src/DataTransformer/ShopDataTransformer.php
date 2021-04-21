<?php

declare(strict_types=1);

namespace App\DataTransformer;

use App\Dto\Shop as Dto;
use App\Entity\Shop;
use App\Repository\ORM\ManagerRepository;

class ShopDataTransformer
{
    protected ManagerRepository $managerRepository;

    public function __construct(ManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function transform(Dto $data): Shop
    {
        $shop = new Shop();
        $shop->setId($data->id);
        $shop->setName($data->name);
        $shop->setAddress1($data->address1);
        $shop->setPostalCode($data->postalCode);
        $shop->setCity($data->city);
        $shop->setLatitude($data->latitude);
        $shop->setLongitude($data->longitude);

        if ($data->manager) {
            $manager = $this->managerRepository->find($data->manager);
            $shop->setManager($manager);
        }

        return $shop;
    }

    public function reverseTransform(Shop $data): Dto
    {
        $dto = new Dto();
        $dto->id = $data->getId();
        $dto->name = $data->getName();
        $dto->address1 = $data->getAddress1();
        $dto->postalCode = $data->getPostalCode();
        $dto->city = $data->getCity();
        $dto->longitude = $data->getLongitude();
        $dto->latitude = $data->getLatitude();

        if ($dto->longitude && $dto->latitude) {
            $dto->location = sprintf('%s,%s', $dto->latitude, $dto->longitude);
        }

        if ($data->getManager()) {
            $dto->manager = $data->getManager()->getId();
        }

        return $dto;
    }
}
