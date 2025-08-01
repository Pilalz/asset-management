<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

            // Lacak pengguna berdasarkan google_id
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // Jika pengguna sudah ada, login kan
                Auth::login($user);
            } else {
                // Jika tidak ada google_id, cari dengan email
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // Jika pengguna dengan email yang sama ditemukan,
                    // perbarui akun yang sudah ada dengan google_id dan login
                    $existingUser->google_id = $googleUser->id;
                    $existingUser->save();
                    Auth::login($existingUser);
                } else {
                    // Jika benar-benar pengguna baru, buat akun baru
                    $newUser = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        // Pastikan kolom 'password' ada, meskipun nilainya acak
                        'password' => Hash::make(rand(100000, 999999)),
                    ]);
                    Auth::login($newUser);
                }
            }

            // Tambahkan baris debugging ini untuk memastikan pengguna telah login
            // dd(Auth::check()); 

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            // Catat error ke file log
            \Log::error('Google Login Failed: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Login with Google failed.');
        }
    }
}