<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Halalytics Admin</title>
  <style>
    :root {
      --primary:#004D40;
      --background:#E0F2F1;
      --text:#0F172A;
    }
    body{margin:0;font-family:Inter,system-ui,sans-serif;background:var(--background);color:var(--text)}
    .top{background:var(--primary);color:var(--background);padding:16px 20px;font-weight:700}
    .wrap{padding:20px;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}
    .card{background:var(--background);border:1px solid var(--primary);border-radius:16px;padding:16px}
    .title{font-size:14px;opacity:.8}
    .val{font-size:28px;font-weight:800;color:var(--primary)}
  </style>
</head>
<body>
  <div class="top">Halalytics Admin Dashboard</div>
  <div class="wrap">
    <div class="card"><div class="title">Total Users</div><div class="val">{{ $stats['total_users'] ?? 0 }}</div></div>
    <div class="card"><div class="title">Total Scans</div><div class="val">{{ $stats['total_scans'] ?? 0 }}</div></div>
    <div class="card"><div class="title">Total Donations</div><div class="val">{{ $stats['total_donations'] ?? 0 }}</div></div>
    <div class="card"><div class="title">AI Requests Today</div><div class="val">{{ $stats['ai_requests_today'] ?? 0 }}</div></div>
  </div>
</body>
</html>
