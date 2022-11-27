<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'Erhan ÜRGÜN',
            'email' => 'urgun.js@gmail.com',
            'password' => bcrypt('071109014'),
            'phone' => '+90 (542) 257 06 76',
        ]);

        \App\Models\User::factory(2500)->create();
    }
}
