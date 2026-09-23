<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Fasilitas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #000;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
        }
        .header h1 {
            font-size: 18px;
            color: #000;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 11px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th {
            background-color: #e0e0e0;
            color: #000;
            font-weight: 600;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 7px 6px;
            border-bottom: 1px solid #ccc;
            vertical-align: top;
            color: #000;
        }
        tr:nth-child(even) {
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
            border: 1px solid #000;
            color: #000;
            background-color: #fff;
        }
        .footer {
            margin-top: 24px;
            text-align: right;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rekap Okupansi & Kerusakan Fasilitas</h1>
        <p>Didownload pada: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Fasilitas</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th class="text-center">Kapasitas</th>
                <th class="text-center">Status</th>
                <th class="text-center">Total Reservasi</th>
                <th class="text-center">Approved</th>
                <th class="text-center">Total Laporan</th>
                <th class="text-center">Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['Nama Fasilitas'] }}</td>
                    <td>{{ $row['Tipe'] }}</td>
                    <td>{{ $row['Lokasi'] }}</td>
                    <td class="text-center">{{ $row['Kapasitas'] }}</td>
                    <td class="text-center">
                        <span class="badge">{{ ucfirst($row['Status']) }}</span>
                    </td>
                    <td class="text-center">{{ $row['Total Reservasi'] }}</td>
                    <td class="text-center">{{ $row['Reservasi Approved'] }}</td>
                    <td class="text-center">{{ $row['Total Laporan'] }}</td>
                    <td class="text-center">{{ $row['Laporan Selesai'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Halaman 1 &bull; Sistem Reservasi Fasilitas
    </div>
</body>
</html>
