<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Disposal Aset - {{ $disposal_asset->form_no }}</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px; 
            padding: 10px;
        }
        .container { 
            height: 100%;
            margin: 0 auto; 
        }
        .container2 { 
            height: 100%;
        }
        .header-logo > table td, th {
            border: 0px solid black;
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
            margin-top: 5px; 
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
            margin-bottom: 5px;
        }
        .header-info table { 
            width: 50%; 
            border: none; 
            margin: 10px 0 10px 0;
        }
        .header-info td { 
            border: none; 
            padding: 2px; 
        }
        .approval-table td { 
            text-align:center; 
            margin:15px 0 0 0;
        }
        .approval-table img { 
            max-height: 40px; 
            height: 40px;
        }

        .container2 table tr th{
            background: #dedede;
            border: 0;
            border-bottom: 1px solid black;
        }
        .container2 table tr td {
            border: 0;
        }
        .footer-sum {
            background: #dedede;
            border: 0;
            border-top: 1px solid black;
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

        <div class="header-info">
            <table>
                <tr>
                    <td><p><strong>1. Data Asset <i>(Asset Data)</i></strong></p></td>
                </tr>
                <tr>
                    <td>Nomor Asset <i>(Asset No.)</i></td>
                    <td>: Terlampir</td>
                </tr>
                <tr>
                    <td>Nama Asset <i>(Asset Name)</i></td>
                    <td>: Terlampir</td>
                </tr>
                <tr>
                    <td>No. Unit <i>(Unit No.)</i></td>
                    <td>: Terlampir</td>
                </tr>
                <tr>
                    <td>No. Mesin <i>(Machine No.)</i></td>
                    <td>: Terlampir</td>
                </tr>
                <tr>
                    <td>No. Engine <i>(Engine No.)</i></td>
                    <td>: Terlampir</td>
                </tr>
                <tr>
                    <td>Tahun Produksi <i>(Manufacturing Date)</i></td>
                    <td>: Terlampir</td>
                </tr>
                <tr>
                    <td>Tahun Pembelian <i>(Year Of Purchase)</i></td>
                    <td>: Terlampir</td>
                </tr>
                <tr>
                    <td>Lokasi Unit <i>(Unit Location)</i></td>
                    <td>: Terlampir</td>
                </tr>
                <tr>
                    <td>Alasan Pelepasan <i>(Reason of Disposal)</i></td>
                    <td>: {{ $disposal_asset->reason }}</td>
                </tr>
            </table>
        </div>

        <div class="header-info">
            <table>
                <tr>
                    <td>Nilai Buku <i>(Nett Book Value)</i></td>
                    <td>: {{ format_currency($disposal_asset->nbv) }}</td>
                </tr>
                <tr>
                    <td>Nilai Jual Estimasi <i>(Estimated Selling Price)</i></td>
                    <td>: {{ format_currency($disposal_asset->esp) }}</td>
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
                @foreach($disposal_asset->approvals->sortBy('approval_order') as $approval)
                <tr>
                    <td style="font-weight:bold;">{{ $approval->approval_action }}</td>
                    <td style="font-weight:bold;">{{ $approval->role }}</td>
                    <td>{{ $approval->user->name ?? '-' }}</td>
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

    <!-- PAGE 2 -->
    <div class="container2">
        <h2>
            {{ $disposal_asset->company->name }}<br>
            Assets Sold (Proposal)
        </h2>
        <table>
            <thead>
                <tr>
                    <th>Row Labels</th>
                    <th>Sum of Qty</th>
                    @if($activeCompany->currency === 'USD')
                        <th>Sum of NJAB (USD)</th>
                        <th>Sum of NJAB (IDR)</th>
                    @elseif($activeCompany->currency === 'IDR')
                        <th>Sum of NJAB (IDR)</th>
                        <th>Sum of NJAB (USD)</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($disposal_asset->detailDisposals as $detail)
                <tr>
                    <td>{{ $detail->asset?->description ?? "-" }}</td>
                    <td>{{ $detail->asset?->quantity ?? "-" }}</td>     
                    @if($activeCompany->currency === 'USD')
                        <td class="px-2 py-4">$ {{ number_format($detail->njab, 0, '.', ',') }}</td>
                        <td class="px-2 py-4">Rp {{ number_format(($detail->kurs * $detail->njab), 0, ',', '.') }}</td>
                    @elseif($activeCompany->currency === 'IDR')
                        <td class="px-2 py-4">Rp {{ number_format($detail->njab, 0, ',', '.') }}</td>
                        <td class="px-2 py-4">$ {{ number_format(($detail->njab / $detail->kurs), 0, '.', ',') }}</td>
                    @endif
                </tr>
                @endforeach
                <tr class="footer-sum">
                    <td><b>Grand Total</b></td>
                    <td><b>{{ $sumQuantity }}</b></td>
                    @if($activeCompany->currency === 'USD')
                        <td class="px-2 py-4"><b>$ {{ number_format($totalNjabUsd, 0, '.', ',') }}</b></td>
                        <td class="px-2 py-4"><b>Rp {{ number_format($totalNjabIdr, 0, ',', '.') }}</b></td>
                    @elseif($activeCompany->currency === 'IDR')
                        <td class="px-2 py-4"><b>Rp {{ number_format($totalNjabIdr, 0, ',', '.') }}</b></td>
                        <td class="px-2 py-4"><b>$ {{ number_format($totalNjabUsd, 0, '.', ',') }}</b></td>
                    @endif
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
