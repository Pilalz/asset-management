<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SyncUserCompanyState
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan pengguna sudah login sebelum menjalankan logika ini
        if (Auth::check()) {
            $user = Auth::user()->fresh(); // Selalu gunakan data terbaru
            $sessionCompanyId = session('active_company_id');
            $databaseCompanyId = $user->last_active_company_id;

            // Skenario 1: User baru ditambahkan (session kosong, DB ada)
            if (is_null($sessionCompanyId) && !is_null($databaseCompanyId)) {
                session(['active_company_id' => $databaseCompanyId]);
                return redirect($request->fullUrl());
            }

            // Skenario 2: User dihapus dari company aktif (session ada, DB kosong)
            if (!is_null($sessionCompanyId) && is_null($databaseCompanyId)) {
                session()->forget('active_company_id');
                return redirect()->route('onboard.index'); 
            }

            // --- KONDISI BARU (PALING PENTING) ---
            // Skenario 3: Company aktif user berubah (session beda dengan DB)
            if (!is_null($sessionCompanyId) && !is_null($databaseCompanyId) && $sessionCompanyId != $databaseCompanyId) {
                // Update session agar sesuai dengan database
                session(['active_company_id' => $databaseCompanyId]);
                // Refresh halaman agar perubahan terlihat
                return redirect($request->fullUrl());
            }
        }

        return $next($request);
    }
}
