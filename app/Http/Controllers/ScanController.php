<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan.index');
    }

    public function scan(Request $request) 
    {
        $rawText = $request->query('code');
        $finalCode = $rawText;

        if (filter_var($rawText, FILTER_VALIDATE_URL)) {
            $urlParts = parse_url($rawText);
            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $queryParams);
                $finalCode = $queryParams['code'] ?? $rawText;
            }
        }

        if (preg_match('/Asset No\.\s*:\s*([A-Z0-9]+)/i', $finalCode, $matches)) {
            $finalCode = $matches[1];
        }

        $finalCode = trim($finalCode);

        $asset = Asset::where('asset_code', $finalCode)->first();

        if (!$asset) {
            return redirect()->back()->with('error', 'Asset is not registered in the system!');
        }

        // Redirect to asset detail page
        return redirect()->route('asset.show', $asset->id);
    }

    public function download($id)
    {
        $asset = Asset::findOrFail($id);
        
        $data = route('scan.process', ['code' => $asset->asset_code]);

        return response()->streamDownload(function () use ($data) {
            echo QrCode::format('svg')
                ->size(500)
                ->margin(1)
                ->generate($data);
        }, "QR-{$asset->asset_code}.svg");
    }
}
