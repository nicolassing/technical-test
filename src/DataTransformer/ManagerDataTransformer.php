<?php

declare(strict_types=1);

namespace App\DataTransformer;

use App\Dto\Manager as Dto;
use App\Entity\Manager;

class ManagerDataTransformer
{
    public function transform(Dto $data): Manager
    {
        $manager = new Manager();
        $manager->setId($data->id);
        $manager->setFirstname($data->firstname);
        $manager->setLastname($data->lastname);

        return $manager;
    }

    public function reversetransform(Manager $data): Dto
    {
        $dto = new Dto();
        $dto->id = $data->getId();
        $dto->firstname = $data->getFirstname();
        $dto->lastname = $data->getLastname();

        return $dto;
    }
}
