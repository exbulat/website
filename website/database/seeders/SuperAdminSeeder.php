<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Проверяем, существует ли уже суперадминистратор
        $existingSuperAdmin = User::where('is_super_admin', true)->first();
        
        if (!$existingSuperAdmin) {
            // Создаем суперадминистратора
            User::create([
                'name' => 'Суперадминистратор',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password123'),
                'is_admin' => true,
                'is_super_admin' => true,
            ]);
            
            $this->command->info('Суперадминистратор успешно создан!');
        } else {
            $this->command->info('Суперадминистратор уже существует.');
        }
    }
}
