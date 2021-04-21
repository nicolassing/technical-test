<?php

namespace App\DataFixtures;

use App\Entity\Manager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ManagerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $entity = new Manager();
        $entity->setFirstname('James');
        $entity->setLastname('Hetfield');
        $manager->persist($entity);
        $this->addReference('manager_james', $entity);

        $entity = new Manager();
        $entity->setFirstname('Lars');
        $entity->setLastname('Ulrich');
        $manager->persist($entity);
        $this->addReference('manager_lars', $entity);

        $manager->flush();
    }
}
