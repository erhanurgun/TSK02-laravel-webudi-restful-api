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
            'name' => 'Demo User',
            'email' => 'demo@urgun.com.tr',
            'password' => bcrypt('Demo1234!'),
            'phone' => '+90 (555) 555 55 55',
        ]);
        \App\Models\User::factory(10)->create();
    }
}
