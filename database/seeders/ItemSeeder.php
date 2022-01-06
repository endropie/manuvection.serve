<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment(['local', 'staging'])) $this->fakerGenerate();
    }

    public function fakerGenerate()
    {
        \App\Models\Item::factory()->count(20)->create();
    }
}
