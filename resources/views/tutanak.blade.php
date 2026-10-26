<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Zimmet Tutanağı</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 40px; max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .content { font-size: 16px; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: left; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; width: 200px; }
        .print-btn {
            background: #333; color: white; padding: 10px 20px; text-decoration: none;
            position: fixed; top: 20px; right: 20px; border-radius: 5px; font-family: sans-serif;
        }
        @media print { .print-btn { display: none; } } /* Yazdırırken butonu gizle */
    </style>
</head>
<body>

    <a href="javascript:window.print()" class="print-btn">🖨️ Yazdır / PDF Kaydet</a>

    <div class="header">
        <h2>DEMİRBAŞ ZİMMET TUTANAĞI</h2>
        <p>OFİS ENVANTER YÖNETİM SİSTEMİ</p>
    </div>

    <div class="content">
        <p>
            Aşağıda dökümü yapılan şirket demirbaşları,
            <strong>{{ $calisan->first_name }} {{ $calisan->last_name }}</strong>
            adlı personele sağlam ve çalışır vaziyette teslim edilmiştir.
        </p>

        <p>Personel, zimmetindeki eşyaları iş amacı dışında kullanmamayı ve işten ayrılırken eksiksiz iade etmeyi taahhüt eder.</p>

        <table>
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th>Ürün Adı</th>
                    <th>Seri Numarası</th>
                    <th>Tip</th>
                    <th>Teslim Tarihi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calisan->assets as $asset)
                    <tr>
                        <td>{{ $asset->name }}</td>
                        <td>{{ $asset->serial_number }}</td>
                        <td>{{ ucfirst($asset->type) }}</td>
                        <td>{{ $asset->updated_at->format('d.m.Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <div class="signature-box">
            <p><strong>Teslim Eden</strong></p>
            <p>Birim Yetkilisi</p>
            <br><br><br>
            <p>___________________</p>
        </div>

        <div class="signature-box">
            <p><strong>Teslim Alan</strong></p>
            <p>{{ $calisan->first_name }} {{ $calisan->last_name }}</p>
            <br><br><br>
            <p>___________________</p>
        </div>
    </div>

</body>
</html>