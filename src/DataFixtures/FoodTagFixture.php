<?php

namespace App\DataFixtures;

use App\Entity\Food;
use App\Entity\FoodTags;
use App\Entity\Tags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FoodTagFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $foods = $manager->getRepository(Food::class)->findAll();
        $tags = $manager->getRepository(Tags::class)->findAll();

        if (empty($foods) || empty($tags)) {
            return;
        }

        foreach ($foods as $food) {
            // Choisir de manière aléatoire entre 1 à 3 Tags
            $numberOfTags = random_int(1, 3);
            $selectedTags = (array) array_rand($tags, $numberOfTags);

            foreach ($selectedTags as $tagIndex) {
                $foodTag = new FoodTags();
                $foodTag->setFood($food);
                $foodTag->setTag($tags[$tagIndex]);
                $manager->persist($foodTag);
            }
        }

        $manager->flush();
    }
}
