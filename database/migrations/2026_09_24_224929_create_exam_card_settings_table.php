<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_card_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo_kiri')->nullable();
            $table->string('logo_kanan')->nullable();
            $table->string('header_1')->nullable()->default('PEMERINTAH PROVINSI');
            $table->string('header_2')->nullable()->default('DINAS PENDIDIKAN');
            $table->string('header_3')->nullable()->default('NAMA SEKOLAH');
            $table->string('header_4')->nullable()->default('Alamat Lengkap');
            $table->string('judul_kartu')->nullable()->default('KARTU PESERTA UJIAN');
            
            // history pembuatan (titimangsa) & ttd
            $table->string('tempat_tanggal')->nullable()->default('Kota, ' . date('d F Y'));
            $table->string('jabatan_penandatangan')->nullable()->default('Kepala Sekolah');
            $table->string('nama_penandatangan')->nullable()->default('Nama Kepala Sekolah');
            $table->string('nip_penandatangan')->nullable()->default('NIP. -');
            $table->string('ttd_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_card_settings');
    }
};
