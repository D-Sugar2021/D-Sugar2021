<?php

    namespace Database\Seeders;

    use Illuminate\Database\Console\Seeds\WithoutModelEvents;
    use Illuminate\Database\Seeder;
    use App\Models\User;
    use Illuminate\Support\Facades\Hash;

    class UserSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */
        public function run(): void
        {
            // Admin User
            User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Admin User',
                    'username' => 'admin', // Added username
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );

            // Student Users
            User::firstOrCreate(
                ['email' => 'student1@example.com'],
                [
                    'name' => 'Student One',
                    'username' => 'student1', // Added username
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]
            );

            User::firstOrCreate(
                ['email' => 'student2@example.com'],
                [
                    'name' => 'Student Two',
                    'username' => 'student2', // Added username
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
