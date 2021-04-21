<?php

namespace App\DataFixtures;

use App\Entity\Shop;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ShopFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'name' => 'Le Bataclan',
                'address_1' => '50 Boulevard Voltaire',
                'postal_code' => '75011',
                'city' => 'Paris',
                'lat' => 48.8630357,
                'lon' => 2.3706465,
                'manager' => 'james',
            ],
            [
                'name' => 'Le Trianon',
                'address_1' => '80 Boulevard de Rochechouart',
                'postal_code' => '75018',
                'city' => 'Paris',
                'lat' => 48.8830683,
                'lon' => 2.3429332,
                'manager' => 'james',
            ],
            [
                'name' => 'L\'Olympia',
                'address_1' => '28 Boulevard des Capucines',
                'postal_code' => '75009',
                'city' => 'Paris',
                'lat' => 48.8702443,
                'lon' => 2.3283595,
                'manager' => 'james',
            ],
            [
                'name' => 'Le Parc des Princes',
                'address_1' => '24 Rue du Commandant Guilbaud',
                'postal_code' => '75016',
                'city' => 'Paris',
                'lat' => 48.8413634,
                'lon' => 2.2530693,
                'manager' => 'lars',
            ],
            [
                'name' => 'Le Stade de France',
                'address_1' => 'Rue Henri Delaunay',
                'postal_code' => '93200',
                'city' => 'Saint-Denis',
                'lat' => 48.9244726,
                'lon' => 2.3601325,
                'manager' => 'lars',
            ],
        ];

        foreach ($data as $item) {
            $shop = new Shop();
            $shop->setName($item['name']);
            $shop->setAddress1($item['address_1']);
            $shop->setCity($item['city']);
            $shop->setPostalCode($item['postal_code']);
            $shop->setLatitude($item['lat']);
            $shop->setLongitude($item['lon']);
            $shop->setManager($this->getReference(sprintf('manager_%s', $item['manager'])));
            $manager->persist($shop);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ManagerFixtures::class,
        ];
    }
}
