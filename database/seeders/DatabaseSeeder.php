<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Advertiser, Site, Publisher};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Advertiser::factory(10)->create();
        Site::factory(10)->create();
        Publisher::factory(10)->create();
    }
}
