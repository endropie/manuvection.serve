<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vendor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('???')),
            'name' => $this->faker->company(),
            'address' => $this->faker->address,
        ];
    }

    protected function withFaker()
    {
        return \Illuminate\Container\Container::getInstance()
            ->make(\Faker\Generator::class, ['locale' => 'id_ID']);
    }
}
