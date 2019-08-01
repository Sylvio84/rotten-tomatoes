<?php

namespace App\Service;

use Faker\Factory as Faker_Factory;

class FakerService
{
    /**
     * @type \Faker\Generator
     */
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker_Factory::create('fr_FR');
        $this->faker->addProvider(new \DavidBadura\FakerMarkdownGenerator\FakerProvider($this->faker));
    }

    public function getFaker(): \Faker\Generator
    {
        return $this->faker;
    }
}
