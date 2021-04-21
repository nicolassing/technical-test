<?php

namespace App\Tests\DataTransformeer;

use App\DataTransformer\ShopDataTransformer;
use App\Dto\Shop as ShopDto;
use App\Entity\Manager;
use App\Entity\Shop;
use App\Repository\ORM\ManagerRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ShopDataTransformerTest extends TestCase
{
    protected ManagerRepository | MockObject $managerRepository;
    protected ShopDataTransformer $dataTransformer;

    protected function setUp(): void
    {
        $this->managerRepository = $this->createMock(ManagerRepository::class);
        $this->dataTransformer = new ShopDataTransformer($this->managerRepository);
    }

    public function testTransform(): void
    {
        $dto = new ShopDto();
        $dto->name = 'Sezane';
        $dto->latitude = 1;
        $dto->longitude = 2;
        $dto->manager = 123;

        $this->managerRepository->method('find')->with(123)->willReturn(new Manager());

        $shop = $this->dataTransformer->transform($dto);
        self::assertEquals('Sezane', $shop->getName());
        self::assertInstanceOf(Manager::class, $shop->getManager());
    }

    public function testReverseTransform(): void
    {
        $shop = new Shop();
        $shop->setName('Sezane');
        $shop->setLatitude(1);
        $shop->setLongitude(2);
        $manager = new Manager();
        $manager->setId(123);
        $shop->setManager($manager);

        $dto = $this->dataTransformer->reverseTransform($shop);
        self::assertEquals('Sezane', $dto->name);
        self::assertEquals(123, $dto->manager);
        self::assertEquals('1,2', $dto->location);
    }
}
