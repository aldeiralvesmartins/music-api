<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;

class InitialCompanyAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->firstOrCreate(
            ['name' => 'My Company'],
            [
                'description' => 'Default company created by seeder',
                'industry' => 'Technology',
                'is_active' => true,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'deirnogrc7@gmail.com'],
            [
                'name' => 'Admin',
                'type' => 'admin',
                'password' => '12345678',
                'is_admin' => true,
                'is_super_admin' => true,
                'email_verified_at' => now(),
                'company_id' => $company->id,
            ]
        );
    }
}
