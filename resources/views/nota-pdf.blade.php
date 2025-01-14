<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>{{ $nama_cafe }}</h2>
    <p><strong>Tanggal Transaksi:</strong> {{ $tanggal_transaksi }}</p>
    <p><strong>Nama Kasir:</strong> {{ $nama_kasir }}</p>
    <p><strong>Nomor Meja:</strong> {{ $nomor_meja }}</p>

    <h3>Detail Pemesanan:</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Menu</th>
                <th class="center">Jumlah</th>
                <th class="center">Harga Satuan</th>
                <th class="center">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_pemesanan as $item)
                <tr>
                    <td>{{ $item['nama_menu'] }}</td>
                    <td class="center">{{ $item['jumlah'] }}</td>
                    <td class="center">Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                    <td class="center">Rp {{ number_format($item['total_harga'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total Harga: Rp {{ number_format($total_harga, 0, ',', '.') }}</h3>
</body>
</html>
