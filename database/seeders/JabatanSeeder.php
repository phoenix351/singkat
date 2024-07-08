<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            ['nama'=>"Kepala BPS Provinsi", 'angka_kredit'=>0],
            ['nama'=>"Kabag/Kasubag Umum", 'angka_kredit'=>0],
            ['nama'=>"Analis Kepegawaian Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Analis Anggaran Ahli Madya", 'angka_kredit'=>37.5],
            ['nama'=>"Analis Anggaran Ahli Muda", 'angka_kredit'=>25],
            ['nama'=>"Analis Anggaran Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Pranata Humas Ahli Muda", 'angka_kredit'=>25],
            ['nama'=>"Pranata Humas Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Analis Pengelola Keuangan APBN Ahli Madya", 'angka_kredit'=>37.5],
            ['nama'=>"Analis Pengelola Keuangan APBN Ahli Muda", 'angka_kredit'=>25],
            ['nama'=>"Analis Pengelola Keuangan APBN Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Pranata Keuangan APBN Penyelia", 'angka_kredit'=>25],
            ['nama'=>"Pranata Keuangan APBN Mahir", 'angka_kredit'=>12.5],
            ['nama'=>"Pranata Keuangan APBN Terampil", 'angka_kredit'=>5],
            ['nama'=>"Analis SDM Aparatur Ahli Muda", 'angka_kredit'=>25],
            ['nama'=>"Analis SDM Aparatur Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Pranata SDM Aparatur Penyelia", 'angka_kredit'=>25],
            ['nama'=>"Pranata SDM Aparatur Mahir", 'angka_kredit'=>12.5],
            ['nama'=>"Pranata SDM Aparatur Terampil", 'angka_kredit'=>5],
            ['nama'=>"Penyuluh Hukum Ahli Muda", 'angka_kredit'=>25],
            ['nama'=>"Penyuluh Hukum Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Arsiparis Ahli Madya", 'angka_kredit'=>37.5],
            ['nama'=>"Arsiparis Ahli Muda", 'angka_kredit'=>25],
            ['nama'=>"Arsiparis Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Arsiparis Terampil", 'angka_kredit'=>5],
            ['nama'=>"Penata Laksana Barang Mahir", 'angka_kredit'=>12.5],
            ['nama'=>"Penata Laksana Barang Terampil", 'angka_kredit'=>5],
            ['nama'=>"Pengolah Data", 'angka_kredit'=>12.5],
            ['nama'=>"Verifikator Keuangan", 'angka_kredit'=>12.5],
            ['nama'=>"Pengelola Surat", 'angka_kredit'=>12.5],
            ['nama'=>"Pranata Kearsipan", 'angka_kredit'=>12.5],
            ['nama'=>"Pengelola Barang Milik Negara", 'angka_kredit'=>12.5],
            ['nama'=>"Teknisi Pemeliharaan Sarana dan Prasarana", 'angka_kredit'=>12.5],
            ['nama'=>"Pengemudi", 'angka_kredit'=>12.5],
            ['nama'=>"Pelaksana", 'angka_kredit'=>12.5],
            ['nama'=>"PPPK-Analis SDM Aparatur Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Statistisi Ahli Utama", 'angka_kredit'=>12.5],
            ['nama'=>"Statistisi Ahli Madya", 'angka_kredit'=>37.5],
            ['nama'=>"Statistisi Ahli Muda", 'angka_kredit'=>25],
            ['nama'=>"Statistisi Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Statistisi Penyelia", 'angka_kredit'=>25],
            ['nama'=>"Statistisi Mahir", 'angka_kredit'=>12.5],
            ['nama'=>"Statistisi Terampil", 'angka_kredit'=>5],
            ['nama'=>"Pranata Komputer Ahli Madya", 'angka_kredit'=>37.5],
            ['nama'=>"Pranata Komputer Ahli Muda", 'angka_kredit'=>25],
            ['nama'=>"Pranata Komputer Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Pranata Komputer Penyelia", 'angka_kredit'=>25],
            ['nama'=>"Pranata Komputer Mahir", 'angka_kredit'=>12.5],
            ['nama'=>"Pranata Komputer Terampil", 'angka_kredit'=>5],
            ['nama'=>"Pustakawan Penyelia", 'angka_kredit'=>25],
            ['nama'=>"Pustakawan Mahir", 'angka_kredit'=>12.5],
            ['nama'=>"Pustakawan Terampil", 'angka_kredit'=>5],
            ['nama'=>"Pengolah Data", 'angka_kredit'=>12.5],
            ['nama'=>"Sekretaris", 'angka_kredit'=>12.5],
            ['nama'=>"Pengelola Pengadaan Barang dan Jasa Ahli Pertama", 'angka_kredit'=>12.5],
            ['nama'=>"Kepala BPS Kabupaten/Kota", 'angka_kredit'=>0],
        ];
        
        foreach ($jabatans as $jabatan) {
            \App\Models\Jabatan::create($jabatan);
        }
    }
}
