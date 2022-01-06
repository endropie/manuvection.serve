<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Vendor::create(['code' => '000', 'name' => 'General']);

        if (app()->environment(['local', 'staging'])) $this->fakerGenerate();
    }

    public function fakerGenerate()
    {
        \App\Models\Vendor::factory()->count(20)->create();
    }
}
