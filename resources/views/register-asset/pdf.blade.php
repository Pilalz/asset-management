<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi Aset - {{ $register_asset->form_no }}</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px; 
            border: 1px black solid;
            padding: 10px;
        }
        .container { 
            width: 100%; 
            margin: 0 auto; 
        }
        h3 { 
            text-align: center; 
            text-transform: uppercase;
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
            margin: 50px 0 50px 0;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>{{ $register_asset->company->name }}</h3>
        <h3>FORM REGISTER ASET <i>(ASSET REGISTER FORM)</i></h3>

        <div class="header-info">
            <table>
                <tr>
                    <td><strong>1. Data Pengajuan Asset</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td>No. Formulir Pengajuan <i>(Submmision Form No.)</i></td>
                    <td>: {{ $register_asset->form_no }}</td>
                </tr>
                <tr>
                    <td>Department Pemilik <i>(Own Department)</i></td>
                    <td>: {{ $register_asset->department->name }}</td>
                </tr>
                <tr>
                    <td>Lokasi Unit <i>(Unit Location)</i></td>
                    <td>: {{ $register_asset->location->name }}</td>
                </tr>
                 <tr>
                    <td>Tipe Aset <i>(Asset Type)</i></td>
                    <td>: {{ $register_asset->asset_type === 'LVA' ? 'Low Value Asset' : 'Fixed Asset' }}</td>
                </tr>
            </table>
        </div>

        <p><strong>2. Daftar Asset</strong></p>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>PO No.</th>
                    <th>Invoice No.</th>
                    <th>Commision Date</th>
                    <th>Specification</th>
                    <th>Asset Class</th>
                    <th>Asset Sub-Class</th>
                    <th>Asset Details</th>
                    <th>Cost Code</th>
                </tr>
            </thead>
            <tbody>
                @foreach($register_asset->detailRegisters as $detail)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $detail->po_no ?? '-' }}</td>
                    <td>{{ $detail->invoice_no ?? '-' }}</td>
                    <td>{{ $detail->commission_date ?? '-' }}</td>
                    <td>{{ $detail->specification ?? '-' }}</td>
                    <td>{{ $detail->assetName->assetSubClass->assetClass->name ?? '-' }}</td>
                    <td>{{ $detail->assetName->assetSubClass->name ?? '-' }}</td>
                    <td>{{ $detail->assetName->name ?? '-' }}</td>
                    <td>{{ $detail->assetName->assetSubClass->assetClass->obj_id ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="header-info">
            <table>
                <tr>
                    <td><strong>3. Diasuransikan <i>(Insured)</i></strong></td>
                    <td>: {{ $register_asset->insured === '1' ? 'Ya (Yes)' : 'Tidak (No)' }}</td>
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
                @foreach($register_asset->approvals->sortBy('approval_order') as $approval)
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
