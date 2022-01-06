<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Faker\Generator as Faker;

class PurchaseSeeder extends Seeder
{
    protected Faker $faker;

    public function run()
    {
        $this->faker = app('Faker\Generator');
        if (app()->environment(['local', 'staging'])) $this->fakerGenerate();
    }

    protected function fakerGenerate()
    {
        for ($i=0; $i < 30; $i++) {
            $request = $this->fakerRequest();

            app(\App\Http\Controllers\PurchaseController::class)->store($request);
        }
    }

    protected function fakerRequest()
    {

        $vendor = \App\Models\Vendor::all()->random(1)->first();

        $items = collect();
        for ($i=0; $i < rand(3, 20); $i++) {
            $items->push([
                "name" => $this->faker->userName,
                "quantity" => 25 * rand(2, 20),
                "price" => 500 * rand(2, 20),
                "notes" => rand(0,2) == 0 ? $this->faker->text : null,
            ]);
        }

        $date = $this->faker->dateTimeThisMonth()->format('Y-m-d');

        return new Request([
            "vendor_id" => $vendor->id,
            "date" => $date,
            "due" => $this->faker->dateTimeInInterval($date, "+14 days")->format('Y-m-d'),
            "description" => $this->faker->text,
            "items" => $items->toArray(),
        ]);
    }
}
