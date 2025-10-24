<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Approval;
use App\Models\CompanyUser;
use App\Models\RegisterAsset;
use App\Models\TransferAsset;
use App\Models\DisposalAsset;

class NotificationComposer
{
    public function compose(View $view): void
    {
        $user = Auth::user();
        $pendingApprovals = collect(); // Gunakan collection kosong sebagai default

        if ($user && $user->last_active_company_id) {

            $activeCompanyId = session('active_company_id');

            $potentialApprovals = Approval::where('user_id', $user->id)
                                        ->where('status', 'pending')
                                        ->whereHas('approvable', function ($query) use ($activeCompanyId) {
                                            $query->where('company_id', $activeCompanyId);
                                        })
                                        ->with('approvable.approvals')
                                        ->get();

            $pendingApprovals = $potentialApprovals->filter(function ($approval) {
                if (!$approval->approvable || $approval->approvable->status !== 'Waiting') {
                    return false;
                }
                if ($approval->approvable->sequence == 0) {
                    return true;
                }
                if ($approval->approvable->sequence == 1) {
                    $nextApprover = $approval->approvable->approvals
                                        ->where('status', 'pending')
                                        ->sortBy('approval_order')
                                        ->first();
                    return $nextApprover && $nextApprover->id === $approval->id;
                }
                return false;
            })->map(function ($approval) {
                // --- Bagian Baru: Transformasi Data ---
                $formType = 'Unknown Form';
                $showUrl = '#';
                
                if ($approval->approvable instanceof RegisterAsset) {
                    $formType = 'Register Asset';
                    $showUrl = route('register-asset.show', $approval->approvable_id);
                } elseif ($approval->approvable instanceof TransferAsset) {
                    $formType = 'Transfer Asset';
                    $showUrl = route('transfer-asset.show', $approval->approvable_id);
                } elseif ($approval->approvable instanceof DisposalAsset) {
                    $formType = 'Disposal Asset';
                    $showUrl = route('disposal-asset.show', $approval->approvable_id);
                }
                // Tambahkan 'elseif' lain jika ada jenis formulir baru

                return (object) [
                    'form_type' => $formType,
                    'form_no'   => $approval->approvable->form_no,
                    'form_date'   => $approval->approvable->submit_date,
                    'show_url'  => $showUrl,
                ];
            });
        }

        // Kirim koleksi notifikasi ke view
        $view->with('pendingApprovals', $pendingApprovals);
    }
}