<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Scopes\UserCompanyScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $userToLogin = null;

            // 1. CARI USER TANPA DIBLOKIR OLEH GLOBAL SCOPE
            // Kita tetap gunakan withoutGlobalScope di sini untuk praktik terbaik.
            $user = User::withoutGlobalScope(UserCompanyScope::class)
                        ->where('email', $googleUser->getEmail())
                        ->first();

            if ($user) {
                // Jika user dengan email yang sama sudah ada, update data Google-nya jika kosong.
                $user->google_id = $user->google_id ?? $googleUser->getId();
                $user->avatar = $user->avatar ?? $googleUser->getAvatar();
                $user->save();
                $userToLogin = $user;
            } else {
                // Jika user benar-benar baru, buat akun baru.
                $userToLogin = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(uniqid()), // Password acak
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // 2. LAKUKAN PROSES LOGIN
            Auth::login($userToLogin);

            // 3. ATUR SESSION PERUSAHAAN (LANGKAH PALING PENTING)
            // Cek perusahaan terakhir yang aktif, atau fallback ke perusahaan pertama yang dimiliki user.
            $company = $userToLogin->lastActiveCompany ?? $userToLogin->companies()->first();

            if ($company) {
                Session::put('active_company_id', $company->id);

                // Update juga `last_active_company_id` di database untuk login berikutnya.
                if ($userToLogin->last_active_company_id !== $company->id) {
                    $userToLogin->last_active_company_id = $company->id;
                    $userToLogin->save();
                }

            } else {
                // KASUS KRITIS: User ada tapi tidak terhubung ke perusahaan manapun.
                Auth::logout();
                Session::flush();
                return redirect('/login')->with('error', 'Akun Anda belum terhubung dengan perusahaan manapun.');
            }

            // 4. ARAHKAN KE DASHBOARD
            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('Google Login Failed: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login dengan Google.');
        }
    }
}