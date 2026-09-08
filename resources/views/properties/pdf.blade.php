<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Properti - {{ $property->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 24px; background: #fff; }
        .header { border-bottom: 2px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 20px; }
        h1 { margin: 0; font-size: 28px; }
        .meta { font-size: 12px; color: #6b7280; margin-top: 6px; }
        .cards { display: flex; gap: 16px; margin: 20px 0; }
        .card { flex: 1; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; background: #f9fafb; }
        .label { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.08em; }
        .value { font-size: 20px; font-weight: bold; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; font-size: 12px; }
        th { background: #f3f4f6; }
        .status { display: inline-block; font-size: 11px; padding: 4px 8px; border-radius: 999px; border: 1px solid #d1d5db; }
        .normal { background: #ecfdf5; color: #047857; border-color: #a7f3d0; }
        .warn { background: #fef3c7; color: #b45309; border-color: #fcd34d; }
        .bad { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Properti</h1>
        <div class="meta">{{ $property->name }} · {{ $property->type }} · {{ $property->address ?? 'Alamat belum diisi' }}</div>
    </div>

    <div class="cards">
        <div class="card">
            <div class="label">Health Score</div>
            <div class="value">{{ $healthScore }}%</div>
        </div>
        <div class="card">
            <div class="label">Total Aset</div>
            <div class="value">{{ $property->assets->count() }}</div>
        </div>
        <div class="card">
            <div class="label">Biaya Perawatan</div>
            <div class="value">Rp {{ number_format($totalMaintenanceCost, 0, ',', '.') }}</div>
        </div>
    </div>

    <h2 style="margin-top: 18px; font-size: 18px;">Daftar Aset</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Aset</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Pembelian</th>
                <th>Garansi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($property->assets as $asset)
                <tr>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category?->name ?? 'Belum dikategorikan' }}</td>
                    <td>
                        <span class="status
                            @if($asset->condition === 'Normal') normal
                            @elseif($asset->condition === 'Perlu Servis') warn
                            @else bad @endif">
                            {{ $asset->condition ?? 'Normal' }}
                        </span>
                    </td>
                    <td>{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->translatedFormat('d M Y') : '—' }}</td>
                    <td>{{ $asset->warranty_expiration ? \Carbon\Carbon::parse($asset->warranty_expiration)->translatedFormat('d M Y') : '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2 style="margin-top: 22px; font-size: 18px;">Riwayat Perawatan</h2>
    <table>
        <thead>
            <tr>
                <th>Aset</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Biaya</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @php $allLogs = $property->assets->flatMap(fn($asset) => $asset->maintenanceLogs)->sortByDesc('service_date'); @endphp
            @foreach($allLogs as $log)
                <tr>
                    <td>{{ $log->asset?->name ?? 'Aset tidak diketahui' }}</td>
                    <td>{{ $log->type }}</td>
                    <td>{{ \Carbon\Carbon::parse($log->service_date)->translatedFormat('d M Y') }}</td>
                    <td>Rp {{ number_format($log->cost, 0, ',', '.') }}</td>
                    <td>{{ $log->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
