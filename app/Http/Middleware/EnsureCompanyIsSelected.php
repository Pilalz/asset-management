<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek jika user sudah login DAN session 'active_company_id' TIDAK ADA.
        if (Auth::check() && !Session::has('active_company_id')) {

            // 2. Cek agar tidak terjadi redirect berulang-ulang (loop).
            // Kita izinkan akses ke halaman 'onboard', 'company', dan 'logout'.
            
            // --- BARIS INI YANG DIPERBARUI ---
            if (!$request->is(['onboard*']) && !$request->routeIs('logout')) {
                
                // 3. Jika semua kondisi terpenuhi, paksa redirect ke halaman onboarding.
                return redirect()->route('onboard.index');
            }
        }

        // 4. Jika user adalah tamu, atau sudah punya company aktif, izinkan request berlanjut.
        return $next($request);
    }
}