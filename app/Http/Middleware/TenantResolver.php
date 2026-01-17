<?php

namespace App\Http\Middleware;

use App\Models\TenantDomain;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TenantResolver
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        
        $tenantDomain = TenantDomain::where('domain', $host)
            ->whereNotNull('verified_at')
            ->with('tenant')
            ->first();
            
        if (!$tenantDomain) {
            return new JsonResponse(['message' => 'Domain not configured'], 404);
        }
        
        $tenant = $tenantDomain->tenant;
        
        app()->instance('tenant', $tenant);
        $request->attributes->set('tenant', $tenant);
        
        return $next($request);
    }
}