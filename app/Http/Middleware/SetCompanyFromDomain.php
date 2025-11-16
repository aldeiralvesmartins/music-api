<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCompanyFromDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Bypass for company creation endpoint
        $routeName = optional($request->route())->getName();
        if ($routeName === 'companies.store' || ($request->is('api/companies') && $request->isMethod('post'))) {
            return $next($request);
        }

        $company = Company::query()
            ->where('domain', $host)
            ->where('is_active', true)
            ->first();

        if (! $company) {
            abort(404);
        }

        app()->instance('company_id', $company->id);
        app()->instance('company', $company);

        if ($request->user() && $request->user()->company_id && $request->user()->company_id !== $company->id) {
            abort(403, 'Empresa inválida para este domínio.');
        }

        return $next($request);
    }
}
