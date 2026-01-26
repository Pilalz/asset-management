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
use Illuminate\Support\Facades\Gate;
use App\Models\Asset;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str;

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
        Gate::authorize('is-dev');

        return view('onboarding.create');
    }

    public function storeCompany(Request $request)
    {
        Gate::authorize('is-dev');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'required|string|unique:companies,alias',
            'code' => 'required|string|unique:companies,code',
            'currency' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $validated['owner_id'] = $user->id;

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        $newCompany = Company::create($validated);

        $newCompany->users()->attach($user->id, ['role' => 'Owner']);

        $user->last_active_company_id = $newCompany->id;
        $user->save();
        Session::put('active_company_id', $newCompany->id);

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

        $ownedCompanies = Company::where('owner_id', $user->id)->get();

        try {
            DB::transaction(function() use ($user, $ownedCompanies) {
                foreach ($ownedCompanies as $company) {
                    $totalUsers = $company->users()->count();

                    if ($totalUsers > 1) {
                        throw new \Exception("Gagal! Anda masih menjadi Owner di perusahaan '{$company->name}' yang memiliki user aktif. Silahkan transfer kepemilikan terlebih dahulu.");
                    } else {
                        $company->delete();
                        Asset::where('company_id', $company->id)->delete(); 
                    }
                }

                $user->update([
                    'email' => 'deleted_' . $user->id . '_' . time() . '@no-reply.com', // Biar unique constraint ga error
                    'password' => bcrypt(Str::random(16)),
                    'phone' => null,
                    'last_active_company_id' => null,
                ]);

                $user->delete(); // Soft Delete User
            });
        
            Auth::logout();

            return Redirect::to('/login');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
