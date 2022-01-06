<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->adminGenerate();
        $this->call(ItemSeeder::class);
        $this->call(VendorSeeder::class);
        $this->call(PurchaseSeeder::class);
        $this->call(ReceiveSeeder::class);
        $this->call(BillSeeder::class);
    }

    public function adminGenerate()
    {
        $password = env('DEFAULT_USER_PASSWORD') ?: 'password';

        $user = \App\Models\User::firstOrNew([
            'email' => 'admin@example.com'
        ]);

        $user->name = 'Adminstrator';
        $user->email = 'admin@example.com';
        $user->mobile = '081234567890';
        $user->password = app('hash')->make($password);
        $user->ability = ['*'];
        $user->save();

        auth()->login($user);
    }
}
