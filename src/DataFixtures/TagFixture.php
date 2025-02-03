<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tagNames = [
            'Vegan', 'Vegetarian', 'Gluten-Free', 'Spicy', 'Sweet', 'Savory',
            'Organic', 'Dairy-Free', 'Nut-Free', 'Keto', 'Paleo', 'Halal',
            'Kosher', 'Fast-Food', 'Homemade', 'Street-Food', 'Healthy',
            'High-Protein', 'Low-Carb', 'Fermented'
        ];

        foreach ($tagNames as $name) {
            $tag = new Tags();
            $tag->setName($name);
            $manager->persist($tag);
        }

        $manager->flush();
    }
}
