<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Code {{ $asset->asset_tag }} | AssetHub</title>
    <style>
        @page { margin: 0; }
        body { font-family: Arial, sans-serif; color: #1f2937; margin: 0; padding: 32px; }
        .label { width: 7cm; max-width: 100%; margin: 0 auto; }
        .brand { color: #5b21b6; font-weight: 700; font-size: 18px; margin-bottom: 24px; text-align: center; }
        .sticker { box-sizing: border-box; width: 7cm; height: 2.4cm; border: 0.2mm solid #6b7280; display: flex; align-items: center; gap: 0.25cm; padding: 0.2cm; overflow: hidden; }
        .barcode { flex: 0 0 2cm; width: 2cm; height: 2cm; }
        .barcode svg { display: block; width: 2cm; height: 2cm; }
        .asset-info { min-width: 0; line-height: 1.2; }
        .asset-name { font-size: 11pt; font-weight: 700; overflow-wrap: anywhere; }
        .asset-serial { color: #4b5563; font-size: 8.5pt; margin-top: 0.12cm; overflow-wrap: anywhere; }
        .actions { margin-top: 28px; text-align: center; }
        button, a { border: 0; background: #5b21b6; color: #fff; padding: 10px 16px; text-decoration: none; cursor: pointer; border-radius: 6px; }
        @media print {
            body { padding: 0; }
            .label { width: 7cm; margin: 0; }
            .brand, .actions { display: none; }
        }
    </style>
</head>
<body>
    <main class="label">
        <div class="brand">AssetHub IT Asset</div>
        <div class="sticker">
            <div class="barcode">{!! $barcodeSvg !!}</div>
            <div class="asset-info">
                <div class="asset-name">{{ $asset->name }}</div>
                <div class="asset-serial">SN: {{ $asset->serial_number ?? 'Tidak tersedia' }}</div>
            </div>
        </div>
        <div class="actions"><button type="button" onclick="window.print()">Print QR Code</button> <button type="button" onclick="window.close()">Close</button></div>
    </main>
</body>
</html>
