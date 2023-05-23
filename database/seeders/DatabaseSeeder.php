<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Guard;
use App\Models\Site;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(10)->create();
        Site::factory(15)->create();
        Zone::factory(20)->create();
        Guard::factory(50)->create();
        Equipment::factory(50)->create();
    }
}
