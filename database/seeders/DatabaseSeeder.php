<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $new_password = Hash::make('12345', [
            'rounds' => 12
        ]);

        User::create([
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
            'password' => $new_password,
        ]);
    }
}
