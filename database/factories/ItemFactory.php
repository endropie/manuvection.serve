<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'name' => $this->faker->randomElement(['Outfit', 'Atasan', 'Celana', 'Jaket', 'T-shirt', 'Jeans', 'Switer', 'Stelan Jas', 'Blazer', 'Stelan Vest'])
                    ." ". $this->faker->randomElement(['', 'Wanita', 'Pria', 'Cowok', 'Cewek', 'Anak', 'Anak Cowok', 'Anak Cewek']),
        ];
    }

    protected function withFaker()
    {
        return \Illuminate\Container\Container::getInstance()
            ->make(\Faker\Generator::class, ['locale' => 'id_ID']);
    }
}
