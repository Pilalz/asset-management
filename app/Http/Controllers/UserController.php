<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyUser;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    //Onboarding User
    public function onboard()
    {
        $user = Auth::user();
        $company_user = CompanyUser::where('user_id', $user->id)->get();
        $activeCompany = $user->last_active_company_id;
        
        return view('onboarding.onboard', compact('company_user', 'activeCompany'));
    }

    public function createCompany()
    {
        return view('onboarding.create');
    }

    public function storeCompany(Request $request)
    {
        // 1. Validasi input, owner_id tidak lagi diambil dari form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:companies,code', // Pastikan kode unik
        ]);

        $user = Auth::user();

        // 2. Buat perusahaan baru, atur owner_id secara otomatis
        $company = Company::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'owner_id' => $user->id,
        ]);

        // 3. (PALING PENTING) Hubungkan user ini ke perusahaan yang baru dibuat
        $user->companies()->attach($company->id, ['role' => 'owner']);

        // 4. Atur perusahaan baru sebagai yang aktif di session
        Session::put('active_company_id', $company->id);

        // Update juga last_active_company_id di database
        $user->last_active_company_id = $company->id;
        $user->save();

        // 5. Arahkan ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Selamat datang! Perusahaan Anda berhasil dibuat.');
    }

    //Profile
    public function editProfile(Request $request): View
    {
        return view('onboarding.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('onboarding.edit')->with('status', 'profile-updated');
    }

    public function updateSignature(Request $request)
    {
        $request->validate(['signature' => 'required|string']);
        $user = Auth::user();
        $user->update(['signature' => $request->signature]);
        return back()->with('success', 'Signature saved successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/login');
    }
}
