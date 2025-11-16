<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Normalize slug if needed
        $data['slug'] = Str::slug($data['slug']);

        // Create company
        $company = Company::create($data);

        return response()->json([
            'message' => 'Company created successfully',
            'data' => $company,
        ], 201);
    }
}
