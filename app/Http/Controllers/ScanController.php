<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use ZipArchive;
use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\PdfWriter;
use App\Scopes\CompanyScope;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan.index');
    }

    public function detail($code)
    {
        $asset = Asset::withoutGlobalScope(CompanyScope::class)
            ->with([
                'department' => function ($query) {
                    $query->withoutGlobalScope(CompanyScope::class);
                },
                'location' => function ($query) {
                    $query->withoutGlobalScope(CompanyScope::class);
                },
                'assetName' => function ($query) {
                    $query->withoutGlobalScope(CompanyScope::class);
                },
                'company'
            ])
            ->where('asset_code', $code)
            ->first();

        if (!$asset) {
            return redirect()->back()->with('error', 'Asset is not registered in the system!');
        }

        return view('scan.detail', compact('asset'));
    }

    public function scan(Request $request)
    {
        $rawText = $request->query('code');

        $id = Str::afterLast($rawText, '/');
        
        $asset = Asset::where('asset_code', $id)->first();

        if (!$asset) {
            return redirect()->back()->with('error', 'Asset is not registered in the system!');
        }

        // Redirect to asset detail page
        return redirect()->route('asset.edit', $asset->id);
    }

    public function download(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        $data = route('scan.detail', ['code' => $asset->asset_code]);
        $format = $request->query('format', 'svg');

        // Generate QR Code using Endroid
        $qrCode = EndroidQrCode::create($data)
            ->setSize(500)
            ->setMargin(10);

        if ($format === 'png') {
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $contentType = 'image/png';
            $extension = 'png';
        } elseif ($format === 'pdf') {
            $writer = new PdfWriter();
            $result = $writer->write($qrCode);
            $contentType = 'application/pdf';
            $extension = 'pdf';
        } else {
            $writer = new SvgWriter();
            $result = $writer->write($qrCode);
            $contentType = 'image/svg+xml';
            $extension = 'svg';
        }

        return response()->streamDownload(function () use ($result) {
            echo $result->getString();
        }, "QR-{$asset->asset_number}.{$extension}", [
            'Content-Type' => $contentType,
        ]);
    }

    public function bulkDownload(Request $request)
    {
        $ids = json_decode($request->input('ids'));
        $format = strtolower($request->input('format', 'svg'));

        // Validate format
        $allowedFormats = ['svg', 'png', 'pdf'];
        if (!in_array($format, $allowedFormats)) {
            $format = 'svg';
        }

        if (!$ids || count($ids) == 0) {
            return back()->with('error', 'Pilih aset terlebih dahulu.');
        }

        $assets = Asset::whereIn('id', $ids)->get();

        $zipFileName = "Bulk-QR-Assets-{$format}-" . time() . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($assets as $asset) {
                $data = route('scan.process', ['code' => $asset->asset_code]);

                try {
                    if ($format === 'svg') {
                        // Use Endroid QR Code for SVG
                        $qrCode = EndroidQrCode::create($data)
                            ->setSize(500)
                            ->setMargin(10);

                        $writer = new SvgWriter();
                        $result = $writer->write($qrCode);

                        $zip->addFromString("QR-{$asset->asset_code}.svg", $result->getString());

                    } elseif ($format === 'png') {
                        // Use Endroid QR Code with PNG Writer (uses GD backend)
                        $qrCode = EndroidQrCode::create($data)
                            ->setSize(500)
                            ->setMargin(10);

                        $writer = new PngWriter();
                        $result = $writer->write($qrCode);

                        $zip->addFromString("QR-{$asset->asset_code}.png", $result->getString());

                    } elseif ($format === 'pdf') {
                        // Use Endroid QR Code with PDF Writer (native PDF generation)
                        $qrCode = EndroidQrCode::create($data)
                            ->setSize(500)
                            ->setMargin(10);

                        $writer = new PdfWriter();
                        $result = $writer->write($qrCode);

                        $zip->addFromString("QR-{$asset->asset_code}.pdf", $result->getString());
                    }

                } catch (\Exception $e) {
                    // Fallback to SVG if anything fails
                    $qrCode = EndroidQrCode::create($data)
                        ->setSize(500)
                        ->setMargin(10);

                    $writer = new SvgWriter();
                    $result = $writer->write($qrCode);

                    $zip->addFromString("QR-{$asset->asset_code}.svg", $result->getString());
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
