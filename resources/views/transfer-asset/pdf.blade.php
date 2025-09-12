<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi Aset - {{ $transfer_asset->form_no }}</title>
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
        .approval-table img, .td-img { 
            max-height: 40px; 
            height: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>{{ $transfer_asset->company->name }}</h3>
        <h3>FORM PERPINDAHAN ASSET</h3> 
        <h3><i>ASSET REGISTER FORM</i></h3>

        <div class="header-info">
            <table>
                <tr>
                    <td>Tanggal Pengajuan <i>(Submmision Date)</i></td>
                    <td>: {{ \Carbon\Carbon::parse($transfer_asset->submit_date)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>No. Formulir Pengajuan <i>(Submmision Form No.)</i></td>
                    <td>: {{ $transfer_asset->form_no }}</td>
                </tr>
                <tr>
                    <td>Department Pemilik <i>(Own Department)</i></td>
                    <td>: {{ $transfer_asset->department->name }}</td>
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
                    <th>ID PB Pareto</th>
                    <th>No. Unit <br> <i>(Unit No.)</i></th>
                    <th>No. Mesin <br> <i>(Machine No.)</i></th>
                    <th>Tahun Produksi <br> <i>(Manufacturing Date)</i></th>
                    <th>Tahun Pembelian <br> <i>(Year Of Purchase)</i></th>
                    <th>Lokasi Asal <br> <i>(Origin Location)</i></th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfer_asset->detailTransfers as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->asset?->asset_number ?? "-" }}</td>
                    <td>{{ $detail->asset?->assetName?->name ?? "-" }}</td>
                    <td>{{ $detail->asset?->pareto ?? "-" }}</td>
                    <td>{{ $detail->asset?->unit_no ?? "-" }}</td>
                    <td>{{ $detail->asset?->sn_engine ?? "-" }}</td>
                    <td>{{ $detail->asset?->production_date ?? "-" }}</td>
                    <td>{{ $detail->asset?->capitalized_date ? \Carbon\Carbon::parse($detail->asset->capitalized_date)->translatedFormat('d F Y') : "-" }}</td>
                    <td>{{ $detail->asset?->location?->name ?? "-" }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="header-info">
            <table>
                <tr>
                    <td>Lokasi Tujuan <i>(Destination Location)</i></td>
                    <td>: {{ $transfer_asset->destinationLocation->name }}</td>
                </tr>
                <tr>
                    <td>Alasan Pemindahan <i>(Reason of Movement)</i></td>
                    <td>: {{ $transfer_asset->reason }}</td>
                </tr>              
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
                @foreach($transfer_asset->approvals->sortBy('approval_order') as $approval)
                <tr>
                    <td style="font-weight:bold;">{{ $approval->approval_action }}</td>
                    <td style="font-weight:bold;">{{ $approval->role }}</td>
                    <td>{{ $approval->pic->name ?? '-' }}</td>
                    <td class="td-img">
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
