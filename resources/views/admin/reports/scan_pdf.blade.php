<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h1 { color: #333; text-align: center; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; color: #777; }
        .status-halal { color: #10b981; font-weight: bold; }
        .status-haram { color: #ef4444; font-weight: bold; }
        .status-syubhat { color: #f59e0b; font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Generated on: {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>Product Name</th>
                <th>Barcode</th>
                <th>Halal Status</th>
                <th>Scan Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scans as $index => $scan)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $scan->user->username ?? 'Unknown' }}</td>
                <td>{{ $scan->nama_produk }}</td>
                <td>{{ $scan->barcode }}</td>
                <td>
                    @if($scan->status_halal == 'halal')
                        <span class="status-halal">Halal</span>
                    @elseif($scan->status_halal == 'tidak halal' || $scan->status_halal == 'haram')
                        <span class="status-haram">Haram</span>
                    @else
                        <span class="status-syubhat">Syubhat</span>
                    @endif
                </td>
                <td>{{ $scan->tanggal_scan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Halalytics Admin System - Protected Content
    </div>
</body>
</html>
