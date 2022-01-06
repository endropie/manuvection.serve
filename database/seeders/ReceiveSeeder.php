<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Faker\Generator as Faker;

class ReceiveSeeder extends Seeder
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
            app(\App\Http\Controllers\ReceiveController::class)->store($request);
        }
    }

    protected function fakerRequest()
    {
        $vendor = \App\Models\Vendor::whereHas('purchases')->get()->random(1)->first();

        $items = collect();
        for ($i=0; $i < rand(3, 20); $i++) {
            if ($purchaseItem = $vendor->purchase_items->random(1)->first())
            {
                $items->push([
                    "quantity" => 25 * rand(2, 10),
                    "notes" => rand(0,2) == 0 ? $this->faker->text : null,
                    "purchase_item_id" => $purchaseItem->id,
                ]);
            }

        }

        $date = $this->faker->dateTimeThisMonth()->format('Y-m-d');

        return new Request([
            "vendor_id" => $vendor->id,
            "date" => $date,
            "description" => rand(0,2) == 0 ? $this->faker->text : null,
            "items" => $items->toArray(),
        ]);
    }
}
