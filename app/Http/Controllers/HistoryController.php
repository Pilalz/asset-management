<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use Carbon\Carbon;

class HistoryController extends Controller
{
    public function index()
    {
        return view('history.index');
    }

    // public function history(Asset $asset)
    // {
    //     // Ambil semua aktivitas untuk aset ini, urutkan dari yang terbaru
    //     $activities = $asset->activities()->latest()->get();

    //     return view('history.index', compact('asset', 'activities'));
    // }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = Activity::with(['causer'])
            ->where('log_name', $companyId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay(),
            ]);
        }

        return DataTables::of($query)
            ->editColumn('causer.name', function ($activity) {
                return $activity->causer->name ?? 'System';
            })
            ->editColumn('created_at', function ($activity) {
                return Carbon::parse($activity->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i');
            })
            ->editColumn('changes', function ($activity) {
                // Buat tampilan HTML untuk kolom 'changes'
                if ($activity->properties->isEmpty()) return '-';
                
                $changes = '';
                if ($activity->subject_type === 'App\Models\Insurance' && $activity->event === null){
                    if (explode(' ', $activity->description)[0] === "Created"){
                        $changes .= '<strong>Created:</strong> ' . json_encode($activity->properties->get('attributes'));
                    }
                    elseif (explode(' ', $activity->description)[0] === "Updated"){
                        $changes .= '<strong>Before:</strong> ' . json_encode($activity->properties->get('old')) . '<br>';
                        $changes .= '<strong>After:</strong> ' . json_encode($activity->properties->get('attributes'));
                    }
                }elseif ($activity->event === 'updated') {
                    $changes .= '<strong>Before:</strong> ' . json_encode($activity->properties->get('old')) . '<br>';
                    $changes .= '<strong>After:</strong> ' . json_encode($activity->properties->get('attributes'));
                } elseif ($activity->event === 'created') {
                    $changes .= '<strong>Created:</strong> ' . json_encode($activity->properties->get('attributes'));
                } elseif ($activity->event === 'deleted') {
                    $changes .= '<strong>Deleted:</strong> ' . json_encode($activity->properties->get('old'));
                }
                return $changes;
            })
            ->filterColumn('causer.name', function($query, $keyword) {
                $query->whereHas('causer', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('causer.name', function ($query, $order) {
                $query->join('users', 'activity_log.causer_id', '=', 'users.id')
                    ->orderBy('users.name', $order)
                    ->select('activity_log.*');
            })
            ->rawColumns(['changes'])
            ->toJson();
    }
}
