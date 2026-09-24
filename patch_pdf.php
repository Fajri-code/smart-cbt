<?php
$file = 'resources/views/exam-cards/pdf.blade.php';
$content = file_get_contents($file);

$search1 = "                <div class=\"header-logo\">
                    <div class=\"logo-circle\">LOGO</div>
                </div>
                <div class=\"header-text\">
                    SMP AL-MADINAH<br>
                    ISLAMIC CENTER KKMB
                </div>
            </div>
            
            <div class=\"title\">KARTU UJIAN</div>";
            
$replace1 = "                <div class=\"header-logo\">
                    @if(isset(\$setting) && \$setting->logo_kiri && file_exists(public_path('storage/'.\$setting->logo_kiri)))
                        <img src=\"{{ public_path('storage/'.\$setting->logo_kiri) }}\" style=\"max-height: 50px; max-width: 50px;\">
                    @else
                        <div class=\"logo-circle\">LOGO</div>
                    @endif
                </div>
                <div class=\"header-text\">
                    <div style=\"font-size:9pt; font-weight:bold;\">{{ \$setting->header_1 ?? 'PEMERINTAH PROVINSI' }}</div>
                    <div style=\"font-size:9pt; font-weight:bold;\">{{ \$setting->header_2 ?? 'DINAS PENDIDIKAN' }}</div>
                    <div style=\"font-size:12pt; font-weight:900;\">{{ \$setting->header_3 ?? 'NAMA SEKOLAH' }}</div>
                    <div style=\"font-size:7pt; font-weight:normal;\">{{ \$setting->header_4 ?? 'Alamat Lengkap' }}</div>
                </div>
            </div>
            
            <div class=\"title\">{{ \$setting->judul_kartu ?? 'KARTU PESERTA UJIAN' }}</div>";
$content = str_replace($search1, $replace1, $content);

$search2 = "                    </tr>
                </table>
            </div>";
$replace2 = "                    </tr>
                </table>
            </div>
            
            <div style=\"margin-top: 20px; text-align: right; font-size: 10pt; padding-right: 15px;\">
                <div style=\"display: inline-block; text-align: center; width: 200px;\">
                    <div>{{ \$setting->tempat_tanggal ?? 'Kota, Tanggal' }}</div>
                    <div style=\"font-weight: bold;\">{{ \$setting->jabatan_penandatangan ?? 'Kepala Sekolah' }}</div>
                    <div style=\"height: 50px; margin: 5px 0;\">
                        @if(isset(\$setting) && \$setting->ttd_image && file_exists(public_path('storage/'.\$setting->ttd_image)))
                            <img src=\"{{ public_path('storage/'.\$setting->ttd_image) }}\" style=\"max-height: 50px;\">
                        @endif
                    </div>
                    <div style=\"font-weight: bold; text-decoration: underline;\">{{ \$setting->nama_penandatangan ?? 'Nama Penandatangan' }}</div>
                    <div>{{ \$setting->nip_penandatangan ?? 'NIP. -' }}</div>
                </div>
            </div>";
$content = str_replace($search2, $replace2, $content);

file_put_contents($file, $content);
