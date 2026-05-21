<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictGuruFromAsrama
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasRole('GURU')) {
            $route = $request->route();
            $routeName = $route ? $route->getName() : '';
            
            // List of route name prefixes or exact names that are blocked for GURU
            $blockedRoutes = [
                // 1. Persetujuan & Pengadaan
                'manajemenasetdanasrama.persetujuan.',
                'manajemenasetdanasrama.pengadaan.',
                
                // 2. Aset (cannot create directly)
                'manajemenasetdanasrama.aset.create',
                'manajemenasetdanasrama.aset.store',
                'manajemenasetdanasrama.aset.duplicate',
                'manajemenasetdanasrama.aset.destroy',
                'manajemenasetdanasrama.aset.bulk-destroy',
                
                // 3. Kamar (cannot add, create, or modify)
                'manajemenasetdanasrama.kamar.create',
                'manajemenasetdanasrama.kamar.store',
                'manajemenasetdanasrama.kamar.edit',
                'manajemenasetdanasrama.kamar.update',
                'manajemenasetdanasrama.kamar.destroy',
                'manajemenasetdanasrama.kamar.empty',
                
                // 4. Penghuni (cannot assign or modify)
                'manajemenasetdanasrama.penghuni.create',
                'manajemenasetdanasrama.penghuni.store',
                'manajemenasetdanasrama.penghuni.edit',
                'manajemenasetdanasrama.penghuni.update',
                'manajemenasetdanasrama.penghuni.destroy',
                'manajemenasetdanasrama.penghuni.assign-multiple',
                'manajemenasetdanasrama.penghuni.store-multiple',
                
                // 5. Jadwal Piket (cannot create or modify, only selesai/print/index are allowed)
                'manajemenasetdanasrama.jadwal-piket.create',
                'manajemenasetdanasrama.jadwal-piket.store',
                'manajemenasetdanasrama.jadwal-piket.edit',
                'manajemenasetdanasrama.jadwal-piket.update',
                'manajemenasetdanasrama.jadwal-piket.destroy',
                'manajemenasetdanasrama.jadwal-piket.destroy-day',
                'manajemenasetdanasrama.jadwal-piket.auto-generate',
                'manajemenasetdanasrama.jadwal-piket.bulk-store',
                'manajemenasetdanasrama.jadwal-piket.reset',
                
                // 6. Trash
                'manajemenasetdanasrama.trash.',

                // 7. Pemeliharaan (Guru cannot record/modify maintenance)
                'manajemenasetdanasrama.pemeliharaan.',
                'manajemenasetdanasrama.kerusakan.proses-pemeliharaan',
            ];

            foreach ($blockedRoutes as $blocked) {
                if (str_starts_with($routeName, $blocked)) {
                    abort(403, 'Akses Ditolak: Guru tidak memiliki wewenang untuk tindakan ini di modul Aset & Asrama.');
                }
            }
        }

        return $next($request);
    }
}
