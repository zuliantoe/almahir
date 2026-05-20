<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SiswaAccessRestrictionMiddleware
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
        $user = auth()->user();
        if ($user && $user->hasRole('SISWA')) {
            $route = $request->route();
            $routeName = $route ? $route->getName() : '';
            $path = $request->path();

            // 1. Whitelist URL patterns for Student
            $allowedPatterns = [
                '^$', // Dashboard
                '^profile',
                '^notifications',
                '^logout',
                '^penilaiandanpresensi/presensi/siswa',
                '^penilaiandanpresensi/izinsakit/siswa',
                '^akademik/jadwal-pelajaran',
                '^akademik/kalender-akademik',
                '^penilaiandanpresensi/penilaianakademik',
                '^penilaiandanpresensi/penilaiantahfidz',
                '^manajemenasetdanasrama/jadwal-piket',
                '^manajemenasetdanasrama/kamar',
                '^manajemenasetdanasrama/penghuni',
                '^keuangan/uangsakus',
                '^keuangan$',
                '^siswa/asrama',
                '^siswa/dashboard',
            ];

            $isAllowedPath = false;
            if ($request->is('/') || $path === '/' || $path === '') {
                $isAllowedPath = true;
            } else {
                foreach ($allowedPatterns as $pattern) {
                    if (preg_match('#' . $pattern . '#i', $path)) {
                        $isAllowedPath = true;
                        break;
                    }
                }
            }

            if (!$isAllowedPath) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman admin ini.');
            }

            // 2. Restrict Write/Modify methods to only student-specific write routes
            $method = $request->method();
            $isWriteMethod = in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']);
            $isCreateOrEditRoute = str_ends_with($routeName, '.create') || str_ends_with($routeName, '.edit');

            if ($isWriteMethod || $isCreateOrEditRoute) {
                // Whitelisted routes where student can write
                $allowedWriteRoutes = [
                    'penilaiandanpresensi.presensi.siswa.store',
                    'penilaiandanpresensi.izinsakit.siswa.index',
                    'penilaiandanpresensi.izinsakit.siswa.create',
                    'penilaiandanpresensi.izinsakit.siswa.store',
                    'penilaiandanpresensi.izinsakit.siswa.edit',
                    'penilaiandanpresensi.izinsakit.siswa.update',
                    'penilaiandanpresensi.izinsakit.siswa.destroy',
                    'profile.update',
                    'logout',
                ];

                if (!in_array($routeName, $allowedWriteRoutes)) {
                    abort(403, 'Akses Ditolak: Siswa tidak diizinkan melakukan perubahan data ini.');
                }
            }
        }

        return $next($request);
    }
}
