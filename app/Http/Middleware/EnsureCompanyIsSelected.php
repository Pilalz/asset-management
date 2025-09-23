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
        // 1. Pastikan pengguna sudah login
        if (Auth::check()) {
            $user = Auth::user();
            $hasActiveCompany = !is_null($user->last_active_company_id);

            // 2. JIKA PENGGUNA PUNYA PERUSAHAAN AKTIF
            if ($hasActiveCompany) {
                // Tapi dia mencoba mengakses halaman onboard...
                if ($request->routeIs('onboard.*')) {
                    // ...maka paksa arahkan ke dashboard.
                    return redirect()->route('dashboard');
                }
            } 
            // 3. JIKA PENGGUNA TIDAK PUNYA PERUSAHAAN AKTIF
            else {
                // Dan dia TIDAK sedang berada di halaman onboard...
                if (!$request->routeIs('onboard.*', 'profile.destroy', 'profile.updateSignature', 'password.update', 'profile.update', 'logout', 'company.switch')) {
                     // ...maka paksa arahkan ke halaman onboard.
                    return redirect()->route('onboard.index');
                }
            }
        }

        // 4. Jika semua kondisi tidak terpenuhi, izinkan request berlanjut.
        return $next($request);
    }
}