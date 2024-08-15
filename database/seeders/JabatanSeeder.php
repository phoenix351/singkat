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
            ['id' => 1, 'nama' => "Kepala BPS Provinsi", 'angka_kredit' => 0],
            ['id' => 2, 'nama' => "Kabag/Kasubag Umum", 'angka_kredit' => 0],
            ['id' => 3, 'nama' => "Analis Kepegawaian Pertama", 'angka_kredit' => 12.5],
            ['id' => 4, 'nama' => "Analis Anggaran Ahli Madya", 'angka_kredit' => 37.5],
            ['id' => 5, 'nama' => "Analis Anggaran Ahli Muda", 'angka_kredit' => 25],
            ['id' => 6, 'nama' => "Analis Anggaran Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 7, 'nama' => "Pranata Humas Ahli Muda", 'angka_kredit' => 25],
            ['id' => 8, 'nama' => "Pranata Humas Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 9, 'nama' => "Analis Pengelola Keuangan APBN Ahli Madya", 'angka_kredit' => 37.5],
            ['id' => 10, 'nama' => "Analis Pengelola Keuangan APBN Ahli Muda", 'angka_kredit' => 25],
            ['id' => 11, 'nama' => "Analis Pengelola Keuangan APBN Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 12, 'nama' => "Pranata Keuangan APBN Penyelia", 'angka_kredit' => 25],
            ['id' => 13, 'nama' => "Pranata Keuangan APBN Mahir", 'angka_kredit' => 12.5],
            ['id' => 14, 'nama' => "Pranata Keuangan APBN Terampil", 'angka_kredit' => 5],
            ['id' => 15, 'nama' => "Analis SDM Aparatur Ahli Muda", 'angka_kredit' => 25],
            ['id' => 16, 'nama' => "Analis SDM Aparatur Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 17, 'nama' => "Pranata SDM Aparatur Penyelia", 'angka_kredit' => 25],
            ['id' => 18, 'nama' => "Pranata SDM Aparatur Mahir", 'angka_kredit' => 12.5],
            ['id' => 19, 'nama' => "Pranata SDM Aparatur Terampil", 'angka_kredit' => 5],
            ['id' => 20, 'nama' => "Penyuluh Hukum Ahli Muda", 'angka_kredit' => 25],
            ['id' => 21, 'nama' => "Penyuluh Hukum Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 22, 'nama' => "Arsiparis Ahli Madya", 'angka_kredit' => 37.5],
            ['id' => 23, 'nama' => "Arsiparis Ahli Muda", 'angka_kredit' => 25],
            ['id' => 24, 'nama' => "Arsiparis Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 25, 'nama' => "Arsiparis Terampil", 'angka_kredit' => 5],
            ['id' => 26, 'nama' => "Penata Laksana Barang Mahir", 'angka_kredit' => 12.5],
            ['id' => 27, 'nama' => "Penata Laksana Barang Terampil", 'angka_kredit' => 5],
            ['id' => 28, 'nama' => "Pengolah Data", 'angka_kredit' => 12.5],
            ['id' => 29, 'nama' => "Verifikator Keuangan", 'angka_kredit' => 12.5],
            ['id' => 30, 'nama' => "Pengelola Surat", 'angka_kredit' => 12.5],
            ['id' => 31, 'nama' => "Pranata Kearsipan", 'angka_kredit' => 12.5],
            ['id' => 32, 'nama' => "Pengelola Barang Milik Negara", 'angka_kredit' => 12.5],
            ['id' => 33, 'nama' => "Teknisi Pemeliharaan Sarana dan Prasarana", 'angka_kredit' => 12.5],
            ['id' => 34, 'nama' => "Pengemudi", 'angka_kredit' => 12.5],
            ['id' => 35, 'nama' => "Pelaksana", 'angka_kredit' => 12.5],
            ['id' => 36, 'nama' => "PPPK-Analis SDM Aparatur Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 37, 'nama' => "Statistisi Ahli Utama", 'angka_kredit' => 12.5],
            ['id' => 38, 'nama' => "Statistisi Ahli Madya", 'angka_kredit' => 37.5],
            ['id' => 39, 'nama' => "Statistisi Ahli Muda", 'angka_kredit' => 25],
            ['id' => 40, 'nama' => "Statistisi Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 41, 'nama' => "Statistisi Penyelia", 'angka_kredit' => 25],
            ['id' => 42, 'nama' => "Statistisi Mahir", 'angka_kredit' => 12.5],
            ['id' => 43, 'nama' => "Statistisi Terampil", 'angka_kredit' => 5],
            ['id' => 44, 'nama' => "Pranata Komputer Ahli Madya", 'angka_kredit' => 37.5],
            ['id' => 45, 'nama' => "Pranata Komputer Ahli Muda", 'angka_kredit' => 25],
            ['id' => 46, 'nama' => "Pranata Komputer Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 47, 'nama' => "Pranata Komputer Penyelia", 'angka_kredit' => 25],
            ['id' => 48, 'nama' => "Pranata Komputer Mahir", 'angka_kredit' => 12.5],
            ['id' => 49, 'nama' => "Pranata Komputer Terampil", 'angka_kredit' => 5],
            ['id' => 50, 'nama' => "Pustakawan Penyelia", 'angka_kredit' => 25],
            ['id' => 51, 'nama' => "Pustakawan Mahir", 'angka_kredit' => 12.5],
            ['id' => 52, 'nama' => "Pustakawan Terampil", 'angka_kredit' => 5],
            ['id' => 53, 'nama' => "Pengolah Data", 'angka_kredit' => 12.5],
            ['id' => 54, 'nama' => "Sekretaris", 'angka_kredit' => 12.5],
            ['id' => 55, 'nama' => "Pengelola Pengadaan Barang dan Jasa Ahli Pertama", 'angka_kredit' => 12.5],
            ['id' => 56, 'nama' => "Kepala BPS Kabupaten/Kota", 'angka_kredit' => 0],
        ];

        foreach ($jabatans as $jabatan) {
            \App\Models\Jabatan::create($jabatan);
        }
    }
}
