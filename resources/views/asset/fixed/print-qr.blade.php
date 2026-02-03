<html>
<head>
    <style>
        @page { margin: 1cm; }
        body {

        }
        .label-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .label-card {
            width: 31%;
            border: 1px dashed #ccc;
            padding: 10px;
            height: 4cm;
            display: flex; 
            flex-direction: row; 
            justify-content: space-between;
            align-items: center;
        }
        .qr-img { width: 100px; height: 100px; }
    </style>
</head>
<body>
    <div class="label-grid">
        @foreach ($assets->chunk(3) as $chunk)
            @foreach ($chunk as $asset)
                <div class="label-card">
                    <div>
                        <div class="company-tag">
                            <img src="{{ Storage::url($activeCompany->logo) }}" class="" alt="Logo Company" />
                        </div>
                        <table style="text-align:left;">
                            <tr>
                                <th>Asset No.</th>
                                <td>{{ $asset->asset_number }}</td>
                            </tr>
                            <tr>
                                <th>Asset Name</th>
                                <td>{{ $asset->asset_name_name }}</td>
                            </tr>
                            <tr>
                                <th>Asset Model</th>
                                <td>{{ $asset->description }}</td>
                            </tr>
                            <tr>
                                <th>SN</th>
                                <td>{{ $asset->sn }}</td>
                            </tr>
                            <tr>
                                <th>Own Dept.</th>
                                <td>{{ $asset->department->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <img class="qr-img" src="data:image/svg+xml;base64,{{ $asset->qr_base64 }}">
                    </div>
                </div>
            @endforeach
            {{-- Isi cell kosong jika baris tidak penuh --}}
            @for ($i = 0; $i < (3 - count($chunk)); $i++)
                <div class="label-card" style="border:none"></div>
            @endfor
        @endforeach
    </div>
</body>
</html>