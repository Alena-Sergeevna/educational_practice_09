<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Учебные учётные записи (пароль у всех одинаковый: password).
     */
    public function run(): void
    {
        $plain = 'password';

        $accounts = [
            ['name' => 'Администратор', 'email' => 'admin@company.test', 'role' => UserRole::Admin],
            ['name' => 'Вариант Проекты', 'email' => 'projects@company.test', 'role' => UserRole::Projects],
            ['name' => 'Вариант Org', 'email' => 'org@company.test', 'role' => UserRole::Org],
            ['name' => 'Вариант Assets', 'email' => 'assets@company.test', 'role' => UserRole::Assets],
            ['name' => 'Вариант Tickets', 'email' => 'tickets@company.test', 'role' => UserRole::Tickets],
            ['name' => 'Вариант Hiring', 'email' => 'hiring@company.test', 'role' => UserRole::Hiring],
        ];

        foreach ($accounts as $row) {
            User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => Hash::make($plain),
                    'role' => $row['role'],
                ]
            );
        }
    }
}
