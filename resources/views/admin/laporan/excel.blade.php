<!DOCTYPE html>
<html>
<head>
    <title>Laporan Cuti</title>
    <style>
        /* CSS Sederhana agar rapi di Excel */
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h3 style="text-align: center;">
        REKAPITULASI DATA CUTI PEGAWAI<br>
        PENGADILAN AGAMA SENDAWAR
    </h3>
    <p style="text-align: center;">Periode: {{ date('d-m-Y', strtotime($awal)) }} s.d. {{ date('d-m-Y', strtotime($akhir)) }}</p>

    <table>
        <thead>
            <tr>
                <th width="50">NO</th>
                <th width="200">NAMA PEGAWAI</th>
                <th width="150">NIP</th>
                <th width="150">JABATAN</th>
                <th width="150">JENIS CUTI</th>
                <th width="100">TGL MULAI</th>
                <th width="100">TGL SELESAI</th>
                <th width="80">LAMA</th>
                <th width="250">KETERANGAN / ALASAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->user->name }}</td>
                <td style="mso-number-format:'\@'">{{ $item->user->nip }}</td> <td>{{ $item->user->jabatan }}</td>
                <td>{{ $item->jenis_cuti }}</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($item->tanggal_mulai)) }}</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($item->tanggal_selesai)) }}</td>
                <td class="text-center">{{ $item->lama }} Hari</td>
                <td>{{ $item->alasan }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">TIDAK ADA DATA CUTI PADA PERIODE INI</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>