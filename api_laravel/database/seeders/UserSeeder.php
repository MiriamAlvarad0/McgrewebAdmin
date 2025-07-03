<?php


namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@hotmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Password1#'),
        ])->assignRole('Admin');


        User::create([
            'name' => 'Usuario',
            'email' => 'user@hotmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Password1#'),
        ])->assignRole('User');


        User::create([
            'name' => 'Invitado',
            'email' => 'guest@hotmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Password1#'),
        ])->assignRole('Guest');
    }
}
