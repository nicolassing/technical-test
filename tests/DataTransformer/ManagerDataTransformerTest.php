<?php

namespace App\Tests\DataTransformeer;

use App\DataTransformer\ManagerDataTransformer;
use App\Dto\Manager as ManagerDto;
use App\Entity\Manager;
use PHPUnit\Framework\TestCase;

class ManagerDataTransformerTest extends TestCase
{
    protected ManagerDataTransformer $dataTransformer;

    protected function setUp(): void
    {
        $this->dataTransformer = new ManagerDataTransformer();
    }

    public function testTransform(): void
    {
        $dto = new ManagerDto();
        $dto->id = 123;
        $dto->firstname = 'John';
        $dto->lastname = 'Doe';

        $manager = $this->dataTransformer->transform($dto);
        self::assertEquals('John', $manager->getFirstname());
        self::assertEquals('Doe', $manager->getLastname());
        self::assertEquals(123, $manager->getId());
    }

    public function testReverseTransform(): void
    {
        $manager = new Manager();
        $manager->setId(123);
        $manager->setFirstname('John');
        $manager->setLastname('Doe');

        $dto = $this->dataTransformer->reverseTransform($manager);
        self::assertEquals('John', $dto->firstname);
        self::assertEquals('Doe', $dto->lastname);
        self::assertEquals(123, $dto->id);
    }
}
