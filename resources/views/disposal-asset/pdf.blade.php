<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi Aset - {{ $disposal_asset->form_no }}</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px; 
            padding: 10px;
        }
        .container { 
            width: 100%; 
            margin: 0 auto; 
            padding-top: 15px
        }
        .header-logo > table td, th {
            border: 0px solid black;
            border-bottom: 2px solid black;
        }
        .rightText {
            text-align: right;
        }
        h3 { 
            text-align: center; 
            text-transform: uppercase;
            margin: 5px;
            padding: 0px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th { 
            border: 1px solid black; 
            padding: 8px; 
            text-align: center; 
        }
        td { 
            border: 1px solid black; 
            padding: 8px; 
            text-align: left; 
        }
        .header-info { 
            margin-bottom: 20px; 
        }
        .header-info table { 
            width: 50%; 
            border: none; 
            margin: 30px 0 30px 0;
        }
        .header-info td { 
            border: none; 
            padding: 2px; 
        }
        .approval-table td { 
            text-align:center; 
        }
        .approval-table img { 
            max-height: 40px; 
            height: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-logo">
            <table>
                <tr>
                    <td>
                        <div>
                            @if($disposal_asset->company->logo)
                                <img src="{{ public_path('storage/' . $disposal_asset->company->logo) }}" style="max-height: 60px;">
                            @endif
                        </div>
                    </td>
                    <td class="rightText">
                        <p>{{ $disposal_asset->company->address ?? "-" }}</p>
                        <p>Phone : {{ $disposal_asset->company->phone ?? "-" }}</p>
                        <p>Fax : {{ $disposal_asset->company->fax ?? "-" }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>{{ $disposal_asset->company->name }}</h3>
        <h3>FORM PELEPASAN ASSET</h3>
        <h3><i>ASSET DISPOSAL FORM</i></h3>

        <div class="header-info">
            <table>
                <tr>
                    <td>Tanggal Pengajuan <i>(Submmision Date)</i></td>
                    <td>: {{ \Carbon\Carbon::parse($disposal_asset->submit_date)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>No. Formulir Pengajuan <i>(Submmision Form No.)</i></td>
                    <td>: {{ $disposal_asset->form_no }}</td>
                </tr>
                <tr>
                    <td>Department Pemilik <i>(Department)</i></td>
                    <td>: {{ $disposal_asset->department->name }}</td>
                </tr>
            </table>
        </div>

        <p><strong>1. Data Asset <i>(Asset Data)</i></strong></p>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Asset <br> <i>(Asset No.)</i></th>
                    <th>Nama Asset <br> <i>(Asset Name)</i></th>
                    <th>No. Unit <br> <i>(Unit No.)</i></th>
                    <th>No. Mesin <br> <i>(Machine No.)</i></th>
                    <th>Tahun Produksi <br> <i>(Manufacturing Date)</i></th>
                    <th>Tahun Pembelian <br> <i>(Year Of Purchase)</i></th>
                    <th>Lokasi Unit <br> <i>(Unit Location)</i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($disposal_asset->detailDisposals as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->asset?->asset_number ?? "-" }}</td>
                    <td>{{ $detail->asset?->assetName?->name ?? "-" }}</td>                    
                    <td>{{ $detail->asset?->unit_no ?? "-" }}</td>
                    <td>{{ $detail->asset?->sn_engine ?? "-" }}</td>
                    <td>{{ $detail->asset?->production_year ? \Carbon\Carbon::parse($detail->asset->production_year)->translatedFormat('Y') : "-" }}</td>
                    <td>{{ $detail->asset?->capitalized_date ? \Carbon\Carbon::parse($detail->asset->capitalized_date)->translatedFormat('d F Y') : "-" }}</td>
                    <td>{{ $detail->asset?->location?->name ?? "-" }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="header-info">
            <table>
                <tr>
                    <td>Alasan Pelepasan <i>(Reason of Disposal)</i></td>
                    <td>: {{ $disposal_asset->reason }}</td>
                </tr>
                @if($activeCompany->currency === 'USD')
                    <tr>
                        <td>Nilai Buku <i>(Nett Book Value)</i></td>
                        <td>: $ {{ number_format($disposal_asset->nbv, 0, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td>Nilai Jual Estimasi <i>(Estimated Selling Price)</i></td>
                        <td>: $ {{ number_format($disposal_asset->esp, 0, '.', ',') }}</td>
                    </tr>
                @elseif($activeCompany->currency === 'IDR')
                    <tr>
                        <td>Nilai Buku <i>(Nett Book Value)</i></td>
                        <td>: Rp {{ number_format($disposal_asset->nbv, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Nilai Jual Estimasi <i>(Estimated Selling Price)</i></td>
                        <td>: Rp {{ number_format($disposal_asset->esp, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <table class="approval-table">
            <thead>
                <tr>
                    <th colspan="2">Persetujuan<br><i>Approval</i></th>
                    <th>Nama<br><i>Name</i></th>
                    <th>Tandatangan<br><i>Signature</i></th>
                    <th>Tanggal<br><i>Date</i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($disposal_asset->approvals->sortBy('approval_order') as $approval)
                <tr>
                    <td style="font-weight:bold;">{{ $approval->approval_action }}</td>
                    <td style="font-weight:bold;">{{ $approval->role }}</td>
                    <td>{{ $approval->pic->name ?? '-' }}</td>
                    <td>
                        @if($approval->status == 'approved' && $approval->user?->signature)
                            <img src="{{ $approval->user->signature }}" alt="Signature">
                        @else
                            {{ $approval->status === 'pending' ? '' : $approval->status }}
                        @endif
                    </td>
                    <td>{{ $approval->approval_date ? \Carbon\Carbon::parse($approval->approval_date)->format('d M Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</body>
</html>
