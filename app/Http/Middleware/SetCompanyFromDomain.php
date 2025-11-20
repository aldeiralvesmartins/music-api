<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCompanyFromDomain
{

    // App/Http/Middleware/SetCompanyFromDomain.php
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->headers->get('X-Company-Domain') ?: $request->getHost();
        $routeName = optional($request->route())->getName();
        if ($routeName === 'companies.store' || ($request->is('api/companies') && $request->isMethod('post'))) {
            return $next($request);
        }

        $company = Company::query()
            ->where('domain', $host)
            ->where('is_active', true)
            ->first();

        if (!$company) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Empresa não encontrada para este domínio.',
                    'domain' => $host,
                    'route' => $routeName,
                ], 404);
            }

            abort(404, "Empresa não encontrada para este domínio: {$host}");
        }

        app()->instance('company_id', $company->id);
        app()->instance('company', $company);

        if ($request->user() && $request->user()->company_id && $request->user()->company_id !== $company->id) {
            abort(403, 'Empresa inválida para este domínio.');
        }

        return $next($request);
    }

}
