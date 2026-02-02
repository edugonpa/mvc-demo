<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder crea usuarios de prueba con diferentes roles
     * para demostrar el sistema de autenticación y autorización.
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // IMPORTANTE: Usar Hash::make para encriptar
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Crear usuario regular
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'), // IMPORTANTE: Usar Hash::make para encriptar
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Crear más usuarios de prueba si es necesario
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
    }
}
