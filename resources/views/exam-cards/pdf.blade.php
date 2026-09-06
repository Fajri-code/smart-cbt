<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Ujian - {{ $kelas->nama_kelas }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        * { box-sizing: border-box; }
        body { color: #0f172a; font-family: DejaVu Sans, sans-serif; margin: 0; }
        .card { border: 2px solid #1e293b; border-radius: 12px; height: 110mm; margin-bottom: 7mm; padding: 6mm; page-break-inside: avoid; position: relative; overflow: hidden; }
        .card:nth-child(2n) { page-break-after: always; }
        .card:last-child { page-break-after: auto; }
        .header { border-bottom: 2px solid #1e293b; padding-bottom: 12px; display: table; width: 100%; }
        .header-logo { display: table-cell; width: 50px; vertical-align: middle; }
        .logo-circle { background-color: #15803d; border-radius: 50%; color: white; display: inline-block; font-size: 10px; height: 40px; line-height: 40px; text-align: center; width: 40px; }
        .header-text { display: table-cell; font-weight: 900; padding-left: 10px; vertical-align: middle; }
        .title { font-size: 18pt; font-weight: 900; margin: 15px 0 10px; text-align: center; }
        .exam-box { background-color: #eef2f6; border-radius: 8px; font-weight: bold; margin: 0 auto; padding: 10px; text-align: center; text-transform: uppercase; width: 80%; }
        .student-info { font-size: 11pt; font-weight: bold; margin: 20px auto 0; width: 90%; }
        table { border-collapse: collapse; width: 100%; }
        td { padding: 4px 0; vertical-align: top; }
        .td-label { width: 120px; }
        .td-colon { width: 15px; }
        .text-blue { color: #1d4ed8; }
        .footer { border-top: 1px solid #1e293b; bottom: 15px; font-size: 8pt; font-style: italic; font-weight: 500; position: absolute; text-align: center; width: calc(100% - 12mm); }
    </style>
</head>
<body>
    @foreach ($students as $student)
        <div class="card">
            <div class="header">
                <div class="header-logo">
                    <div class="logo-circle">LOGO</div>
                </div>
                <div class="header-text">
                    SMP AL-MADINAH<br>
                    ISLAMIC CENTER KKMB
                </div>
            </div>
            
            <div class="title">KARTU UJIAN</div>
            
            <div class="exam-box">
                {{ $namaUjian }}
            </div>
            
            <div class="student-info">
                <table>
                    <tr>
                        <td class="td-label">Nama</td>
                        <td class="td-colon">:</td>
                        <td style="text-transform: uppercase;">{{ $student->nama }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Kelas</td>
                        <td class="td-colon">:</td>
                        <td>{{ $kelas->nama_kelas }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Kode Ruangan</td>
                        <td class="td-colon">:</td>
                        <td>{{ $ruangan }}</td>
                    </tr>
                    <tr>
                        <td class="td-label">Tempat Duduk</td>
                        <td class="td-colon">:</td>
                        <td>{{ $student->nomor_bangku }}</td>
                    </tr>
                    @if($student->user && !empty($student->user->email))
                    <tr>
                        <td class="td-label text-blue">Username CBT</td>
                        <td class="td-colon text-blue">:</td>
                        <td class="text-blue">{{ $student->user->email }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            <div class="footer">
                Kartu ini harap dibawa saat pelaksanaan ujian.
            </div>
        </div>
    @endforeach
</body>
</html>
