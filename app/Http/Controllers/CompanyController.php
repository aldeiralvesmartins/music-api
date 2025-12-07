<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        $companies = Company::query()->orderByDesc('created_at')->get();
        return response()->json($companies);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        // Ensure non-nullable DB columns are filled
        $company = Company::create([
            'name' => $data['name'],
            'industry' => $data['industry'] ?? '',
            'description' => $data['description'] ?? '',
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Company created successfully',
            'data' => $company,
        ], 201);
    }

    public function storeWithUser(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company.name' => 'required|string|max:255',
            'company.industry' => 'nullable|string|max:255',
            'company.description' => 'nullable|string',
            'company.is_active' => 'sometimes|boolean',

            'user.name' => 'required|string|max:255',
            'user.email' => 'required|email|max:255|unique:users,email',
            'user.password' => 'required|string|min:6',
        ]);

        // Create company
        $company = Company::create([
            'name' => $data['company']['name'],
            'industry' => $data['company']['industry'] ?? '',
            'description' => $data['company']['description'] ?? '',
            'is_active' => $data['company']['is_active'] ?? true,
        ]);

        // Create user linked to company
        $user = User::create([
            'name' => $data['user']['name'],
            'email' => $data['user']['email'],
            'password' => $data['user']['password'], // hashed by cast
            'company_id' => $company->id,
            'is_admin' => true,
        ]);

        return response()->json([
            'message' => 'Company and user created successfully',
            'company' => $company,
            'user' => $user,
        ], 201);
    }
}
