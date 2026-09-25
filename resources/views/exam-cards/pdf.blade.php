<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Ujian - {{ $kelas->nama_kelas }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 9mm 12mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
        }
        .card {
            border: 2px solid #1e293b;
            border-radius: 12px;
            height: 130mm;
            padding: 5mm 6mm;
            page-break-inside: avoid;
            position: relative;
            overflow: hidden;
            background-color: #ffffff;
        }
        .page-break {
            page-break-after: always;
            height: 0;
            line-height: 0;
            font-size: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #1e293b;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .header-logo {
            width: 58px;
            text-align: center;
            vertical-align: middle;
        }
        .header-logo-img {
            max-height: 52px;
            max-width: 52px;
        }
        .header-text {
            padding-left: 10px;
            vertical-align: middle;
            text-align: left;
        }
        .header-1 {
            font-size: 8pt;
            font-weight: bold;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .header-2 {
            font-size: 8pt;
            font-weight: bold;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .header-3 {
            font-size: 12pt;
            font-weight: 900;
            line-height: 1.2;
            text-transform: uppercase;
        }
        .header-4 {
            font-size: 6.5pt;
            color: #334155;
            font-weight: normal;
            line-height: 1.25;
            margin-top: 2px;
        }
        .title {
            font-size: 13.5pt;
            font-weight: 900;
            margin: 4px 0 6px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .exam-box {
            background-color: #f1f5f9;
            border-radius: 6px;
            font-weight: bold;
            font-size: 9.5pt;
            margin: 0 auto 8px;
            padding: 4px 12px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 70%;
            color: #0f172a;
        }
        .student-info-container {
            width: 82%;
            margin: 0 auto;
        }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            font-weight: bold;
            color: #0f172a;
        }
        .student-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .td-label {
            width: 110px;
        }
        .td-colon {
            width: 15px;
            text-align: center;
        }
        .td-value {
            text-align: left;
        }
        .text-blue {
            color: #1d4ed8;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .sig-box {
            width: 200px;
            text-align: center;
            vertical-align: top;
        }
        .sig-date {
            font-size: 8pt;
            color: #0f172a;
            line-height: 1.2;
        }
        .sig-title {
            font-size: 8pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.2;
        }
        .sig-img-container {
            height: 34px;
            line-height: 34px;
            margin: 2px 0;
            text-align: center;
        }
        .sig-name {
            font-size: 8pt;
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
            line-height: 1.2;
        }
        .sig-nip {
            font-size: 7.5pt;
            color: #1e293b;
            line-height: 1.2;
        }
        .bottom-note {
            border-top: 1px solid #1e293b;
            bottom: 4mm;
            color: #475569;
            font-size: 7pt;
            font-style: italic;
            font-weight: 500;
            left: 6mm;
            position: absolute;
            right: 6mm;
            text-align: center;
            padding-top: 3px;
        }
    </style>
</head>
<body>
    @foreach ($students as $index => $student)
        <div class="card" style="{{ $index % 2 == 1 ? 'margin-bottom: 0;' : 'margin-bottom: 6mm;' }}">
            <table class="header-table">
                <tr>
                    @if($logoBase64 || !empty($setting->logo_kiri))
                    <td class="header-logo">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" class="header-logo-img">
                        @else
                            <img src="{{ public_path('storage/' . ltrim(preg_replace('#^/?storage/#', '', $setting->logo_kiri), '/')) }}" class="header-logo-img">
                        @endif
                    </td>
                    @endif
                    <td class="header-text" style="{{ (!$logoBase64 && empty($setting->logo_kiri)) ? 'padding-left: 0; text-align: center;' : '' }}">
                        @if(!empty($setting->header_1))
                            <div class="header-1">{{ $setting->header_1 }}</div>
                        @endif
                        @if(!empty($setting->header_2))
                            <div class="header-2">{{ $setting->header_2 }}</div>
                        @endif
                        <div class="header-3">{{ $setting->header_3 ?? 'SMP ISLAMIC CENTER KKMB' }}</div>
                        @if(!empty($setting->header_4))
                            <div class="header-4">{{ $setting->header_4 }}</div>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="title">{{ $setting->judul_kartu ?? 'KARTU UJIAN' }}</div>

            <div class="exam-box">
                {{ $namaUjian }}
            </div>

            <div class="student-info-container">
                <table class="student-table">
                    <tr>
                        <td class="td-label">Nama</td>
                        <td class="td-colon">:</td>
                        <td class="td-value" style="text-transform: uppercase;">{{ $student->nama }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">NISN</td>
                        <td class="td-colon">:</td>
                        <td class="td-value">{{ $student->nisn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Kelas</td>
                        <td class="td-colon">:</td>
                        <td class="td-value">{{ $kelas->nama_kelas }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Kode Ruangan</td>
                        <td class="td-colon">:</td>
                        <td class="td-value">{{ $ruangan }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Tempat Duduk</td>
                        <td class="td-colon">:</td>
                        <td class="td-value">{{ $student->nomor_bangku }}</td>
                    </tr>
                    @if($student->user && !empty($student->user->email))
                    <tr>
                        <td class="td-label text-blue">Username CBT</td>
                        <td class="td-colon text-blue">:</td>
                        <td class="td-value text-blue">{{ $student->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="td-label text-blue">Password CBT</td>
                        <td class="td-colon text-blue">:</td>
                        <td class="td-value text-blue">12345678</td>
                    </tr>
                    @endif
                </table>
            </div>

            <table class="footer-table">
                <tr>
                    <td style="width: 52%; vertical-align: bottom;">
                        <!-- Left blank area -->
                    </td>
                    <td class="sig-box">
                        @if(!empty($setting->tempat_tanggal))
                            <div class="sig-date">{{ $setting->tempat_tanggal }}</div>
                        @endif
                        @if(!empty($setting->jabatan_penandatangan))
                            <div class="sig-title">{{ $setting->jabatan_penandatangan }}</div>
                        @endif
                        <div class="sig-img-container">
                            @if($ttdBase64)
                                <img src="{{ $ttdBase64 }}" style="max-height: 34px; max-width: 120px;">
                            @elseif(!empty($setting->ttd_image))
                                <img src="{{ public_path('storage/' . ltrim(preg_replace('#^/?storage/#', '', $setting->ttd_image), '/')) }}" style="max-height: 34px; max-width: 120px;">
                            @endif
                        </div>
                        @if(!empty($setting->nama_penandatangan))
                            <div class="sig-name">{{ $setting->nama_penandatangan }}</div>
                        @endif
                        @if(!empty($setting->nip_penandatangan))
                            <div class="sig-nip">{{ $setting->nip_penandatangan }}</div>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="bottom-note">
                Kartu ini harap dibawa saat pelaksanaan ujian.
            </div>
        </div>

        @if ($index % 2 == 1 && !$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>

