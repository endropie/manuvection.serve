<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Faker\Generator as Faker;

class BillSeeder extends Seeder
{
    protected Faker $faker;

    public function run()
    {
        $this->faker = app('Faker\Generator');
        if (app()->environment(['local', 'staging'])) $this->fakerGenerate();
    }

    protected function fakerGenerate()
    {
        foreach (\App\Models\Receive::all()->random(10) as $receive) {
            $request = new Request;

            app(\App\Http\Controllers\BillController::class)->storeBaseReceive($receive->id, $request);
        }
    }
}
