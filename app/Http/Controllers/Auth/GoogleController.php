<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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

            // 1. CARI USER
            $user = User::where('email', $googleUser->getEmail())->first();

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

            // 3. ATUR SESSION PERUSAHAAN
            $company = $userToLogin->lastActiveCompany ?? $userToLogin->companies()->first();

            if ($company) {
                Session::put('active_company_id', $company->id);

                if ($userToLogin->last_active_company_id !== $company->id) {
                    $userToLogin->last_active_company_id = $company->id;
                    $userToLogin->save();
                }

            } else {
                return redirect('/onboard');
            }

            // 4. ARAHKAN KE DASHBOARD
            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('Google Login Failed: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Terjadi kesalahan saat login dengan Google.');
        }
    }
}