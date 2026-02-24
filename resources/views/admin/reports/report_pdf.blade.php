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
        .status-pending { color: #f59e0b; font-weight: bold; }
        .status-approved { color: #10b981; font-weight: bold; }
        .status-rejected { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>Generated on: {{ $date }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Reporter</th>
                <th>Product</th>
                <th>Report Content</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $report->user->username ?? 'Unknown' }}</td>
                <td>{{ $report->product->nama_product ?? 'N/A' }}</td>
                <td>{{ $report->laporan }}</td>
                <td>
                    <span class="status-{{ $report->status }}">
                        {{ strtoupper($report->status) }}
                    </span>
                </td>
                <td>{{ $report->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Halalytics Admin System - Protected Content
    </div>
</body>
</html>
