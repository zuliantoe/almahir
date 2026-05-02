<?php

namespace Modules\Akademik\Middleware;

use Closure;
use Illuminate\Http\Request;

class ReadOnlyRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Restrict write operations for GURU and SISWA
        $user = auth()->user();
        if ($user && ($user->hasRole('GURU') || $user->hasRole('SISWA'))) {
            $method = $request->method();
            $isWriteMethod = in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']);
            $isEditOrCreateRoute = str_ends_with($request->route() ? $request->route()->getName() : '', '.create') || 
                                   str_ends_with($request->route() ? $request->route()->getName() : '', '.edit');

            if ($isWriteMethod || $isEditOrCreateRoute) {
                abort(403, 'Anda tidak memiliki akses untuk mengubah data ini.');
            }
        }
        
        return $next($request);
    }
}
