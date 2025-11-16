<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();
        $slug = Str::slug($name);

        return [
            'id' => Str::random(24),
            'name' => $name,
            'slug' => $slug,
            'domain' => $slug . '.meusistema.com',
            'type' => 'subdomain',
            'is_active' => true,
        ];
    }
}
