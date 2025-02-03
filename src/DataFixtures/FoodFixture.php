<?php

namespace App\DataFixtures;

use App\Entity\Food;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FoodFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $foodsData = [
            ['name' => 'Baguette', 'origin' => 'FR'],
            ['name' => 'Sushi', 'origin' => 'JP'],
            ['name' => 'Tacos', 'origin' => 'MX'],
            ['name' => 'Paella', 'origin' => 'ES'],
            ['name' => 'Couscous', 'origin' => 'MA'],
            ['name' => 'Pizza', 'origin' => 'IT'],
            ['name' => 'Kimchi', 'origin' => 'KR'],
            ['name' => 'Falafel', 'origin' => 'LB'],
            ['name' => 'Churrasco', 'origin' => 'BR'],
            ['name' => 'Poutine', 'origin' => 'CA'],
            ['name' => 'Goulash', 'origin' => 'HU'],
            ['name' => 'Sauerbraten', 'origin' => 'DE'],
            ['name' => 'Pho', 'origin' => 'VN'],
            ['name' => 'Moussaka', 'origin' => 'GR'],
            ['name' => 'Ratatouille', 'origin' => 'FR'],
            ['name' => 'Haggis', 'origin' => 'GB'],
            ['name' => 'Biryani', 'origin' => 'IN'],
            ['name' => 'Shawarma', 'origin' => 'TR'],
            ['name' => 'Bobotie', 'origin' => 'ZA'],
            ['name' => 'Arepas', 'origin' => 'VE'],
        ];

        foreach ($foodsData as $data) {
            $food = new Food();
            $food->setName($data['name']);
            $food->setOrigin($data['origin']);
            $manager->persist($food);
        }

        $manager->flush();
    }
}
