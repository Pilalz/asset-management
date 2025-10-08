<table>
    <thead>
        <tr>
            <th rowspan="2" style="font-weight: bold; text-align: center;">No</th>
            <th rowspan="2" style="font-weight: bold; text-align: center;">Asset Name</th>
            <th rowspan="2" style="font-weight: bold; text-align: center;">Asset Number</th>
            
            @foreach($months as $monthName)
                <th colspan="3" style="font-weight: bold; text-align: center;">{{ $monthName }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach($months as $monthName)
                <th style="font-weight: bold; text-align: center;">Monthly Depre</th>
                <th style="font-weight: bold; text-align: center;">Accum Depre</th>
                <th style="font-weight: bold; text-align: center;">Book Value</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse ($pivotedData as $assetId => $data)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $data['master_data']->assetName->name }}</td>
                <td>{{ $data['master_data']->asset_number }}</td>

                @foreach ($months as $monthKey => $monthName)
                    @if (isset($data['schedule'][$monthKey]))
                        <td style="text-align: right;">{{ format_currency($data['schedule'][$monthKey]->monthly_depre) }}</td>
                        <td style="text-align: right;">{{ format_currency($data['schedule'][$monthKey]->accumulated_depre) }}</td>
                        <td style="text-align: right;">{{ format_currency($data['schedule'][$monthKey]->book_value) }}</td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ 3 + (count($months) * 3) }}">Tidak ada data untuk ditampilkan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
