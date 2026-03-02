<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $position = [
            'manager',
            'staff',
            'admin',
        ];
        for ($i = 0; $i < 10; $i++) {
            Employee::create([
                'name' => 'Employee ' . $i,
                'email' => 'employee' . $i . '@example.com',
                'phone' => '1234567890' . $i,
                'birth_date' => rand(2022, 2025) . '-01-01',
                'position' => $position[array_rand($position)],
                'photo' => 'https://example.com/photo.jpg',
                'address' => 'Address ' . $i
            ]);
        }
    }
}
