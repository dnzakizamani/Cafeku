<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // Seed the admin user
        $this->call(AdminSeeder::class);

        // You can add more seeders here as needed
        // $this->call(ProductCategorySeeder::class);
        // $this->call(ProductSeeder::class);
        // $this->call(SubscriptionSeeder::class);
        // $this->call(SubscriptionPaymentSeeder::class);
    }
}
