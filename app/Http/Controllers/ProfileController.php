<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Company;
use App\Models\Asset;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
