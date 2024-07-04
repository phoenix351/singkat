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
            "Kepala BPS Provinsi",
            "Kabag/Kasubag Umum",
            "Analis Kepegawaian Pertama",
            "Analis Anggaran Ahli Madya",
            "Analis Anggaran Ahli Muda",
            "Analis Anggaran Ahli Pertama",
            "Pranata Humas Ahli Muda",
            "Pranata Humas Ahli Pertama",
            "Analis Pengelola Keuangan APBN Ahli Madya",
            "Analis Pengelola Keuangan APBN Ahli Muda",
            "Analis Pengelola Keuangan APBN Ahli Pertama",
            "Pranata Keuangan APBN Penyelia",
            "Pranata Keuangan APBN Mahir",
            "Pranata Keuangan APBN Terampil",
            "Analis SDM Aparatur Ahli Muda",
            "Analis SDM Aparatur Ahli Pertama",
            "Pranata SDM Aparatur Penyelia",
            "Pranata SDM Aparatur Mahir",
            "Pranata SDM Aparatur Terampil",
            "Penyuluh Hukum Ahli Muda",
            "Penyuluh Hukum Ahli Pertama",
            "Arsiparis Ahli Madya",
            "Arsiparis Ahli Muda",
            "Arsiparis Ahli Pertama",
            "Arsiparis Terampil",
            "Penata Laksana Barang Mahir",
            "Penata Laksana Barang Terampil",
            "Pengolah Data",
            "Verifikator Keuangan",
            "Pengelola Surat",
            "Pranata Kearsipan",
            "Pengelola Barang Milik Negara",
            "Teknisi Pemeliharaan Sarana dan Prasarana",
            "Pengemudi",
            "Pelaksana",
            "PPPK-Analis SDM Aparatur Ahli Pertama",
            "Statistisi Ahli Utama",
            "Statistisi Ahli Madya",
            "Statistisi Ahli Muda",
            "Statistisi Ahli Pertama",
            "Statistisi Penyelia",
            "Statistisi Mahir",
            "Statistisi Terampil",
            "Pranata Komputer Ahli Madya",
            "Pranata Komputer Ahli Muda",
            "Pranata Komputer Ahli Pertama",
            "Pranata Komputer Penyelia",
            "Pranata Komputer Mahir",
            "Pranata Komputer Terampil",
            "Pustakawan Penyelia",
            "Pustakawan Mahir",
            "Pustakawan Terampil",
            "Pengolah Data",
            "Sekretaris",
            "Pengelola Pengadaan Barang dan Jasa Ahli Pertama",
        ];

        foreach ($jabatans as $jabatan) {
            \App\Models\Jabatan::create([
                'nama' => $jabatan,
            ]);
        }
    }
}
