<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $abilities = config('auth.gates', []);

        $once = $abilities[array_rand($abilities)];

        $password = env('DEFAULT_USER_PASSWORD') ?: 'password';

        return [
            'name' => $this->faker->userName,
            'email' => $this->faker->unique()->safeEmail,
            'mobile' => $this->faker->unique()->e164PhoneNumber,
            'password' => app('hash')->make($password),
            'ability' => [$once]
        ];
    }

    protected function withFaker()
    {
        return \Illuminate\Container\Container::getInstance()
            ->make(\Faker\Generator::class, ['locale' => 'id_ID']);
    }
}
