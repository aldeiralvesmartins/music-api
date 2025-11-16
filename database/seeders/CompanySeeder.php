<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['slug' => 'default-company'],
            [
                'id' => Str::random(24),
                'name' => 'Default Company',
                'domain' => 'default.meusistema.com',
                'type' => 'subdomain',
                'is_active' => true,
            ]
        );

        app()->instance('company_id', $company->id);
        app()->instance('company', $company);
    }
}
