const calculateAkumulasiAk = (values) => {
    const {
        angka_kredit_integrasi,
        jabatan,
        predikat_kinerja,
        tambahan_ijazah,
    } = values;

    if (!angka_kredit_integrasi || !jabatan || !predikat_kinerja) {
        return "";
    }

    const kreditIntegrasi = parseFloat(angka_kredit_integrasi);
    let tambahan = 0;

    if (jabatan.includes("Ahli Pertama")) {
        switch (predikat_kinerja) {
            case "Sangat Baik":
                tambahan = 18.75;
                break;
            case "Baik":
                tambahan = 12.5;
                break;
            case "Butuh Perbaikan":
                tambahan = 9.38;
                break;
            case "Kurang":
                tambahan = 6.25;
                break;
            case "Sangat Kurang":
                tambahan = 3.13;
                break;
            default:
                tambahan = 0;
                break;
        }
    } else if (jabatan.includes("Ahli Muda")) {
        switch (predikat_kinerja) {
            case "Sangat Baik":
                tambahan = 37.5;
                break;
            case "Baik":
                tambahan = 25;
                break;
            case "Butuh Perbaikan":
                tambahan = 28.13;
                break;
            case "Kurang":
                tambahan = 18.75;
                break;
            case "Sangat Kurang":
                tambahan = 6.25;
                break;
            default:
                tambahan = 0;
                break;
        }
    } else if (jabatan.includes("Ahli Madya")) {
        switch (predikat_kinerja) {
            case "Sangat Baik":
                tambahan = 56.25;
                break;
            case "Baik":
                tambahan = 37.5;
                break;
            case "Butuh Perbaikan":
                tambahan = 28.13;
                break;
            case "Kurang":
                tambahan = 18.75;
                break;
            case "Sangat Kurang":
                tambahan = 9.375;
                break;
            default:
                tambahan = 0;
                break;
        }
    } else if (jabatan.includes("Ahli Utama")) {
        switch (predikat_kinerja) {
            case "Sangat Baik":
                tambahan = 75;
                break;
            case "Baik":
                tambahan = 50;
                break;
            case "Butuh Perbaikan":
                tambahan = 37.5;
                break;
            case "Kurang":
                tambahan = 25;
                break;
            case "Sangat Kurang":
                tambahan = 12.5;
                break;
            default:
                tambahan = 0;
                break;
        }
    } else if (jabatan.includes("Pemula")) {
        switch (predikat_kinerja) {
            case "Sangat Baik":
                tambahan = 5.63;
                break;
            case "Baik":
                tambahan = 3.75;
                break;
            case "Butuh Perbaikan":
                tambahan = 2.81;
                break;
            case "Kurang":
                tambahan = 1.88;
                break;
            case "Sangat Kurang":
                tambahan = 0.94;
                break;
            default:
                tambahan = 0;
                break;
        }
    } else if (jabatan.includes("Terampil")) {
        switch (predikat_kinerja) {
            case "Sangat Baik":
                tambahan = 7.5;
                break;
            case "Baik":
                tambahan = 5;
                break;
            case "Butuh Perbaikan":
                tambahan = 3.75;
                break;
            case "Kurang":
                tambahan = 2.5;
                break;
            case "Sangat Kurang":
                tambahan = 1.25;
                break;
            default:
                tambahan = 0;
                break;
        }
    } else if (jabatan.includes("Mahir")) {
        switch (predikat_kinerja) {
            case "Sangat Baik":
                tambahan = 18.75;
                break;
            case "Baik":
                tambahan = 12.5;
                break;
            case "Butuh Perbaikan":
                tambahan = 9.38;
                break;
            case "Kurang":
                tambahan = 6.25;
                break;
            case "Sangat Kurang":
                tambahan = 3.13;
                break;
            default:
                tambahan = 0;
                break;
        }
    } else if (jabatan.includes("Penyelia")) {
        switch (predikat_kinerja) {
            case "Sangat Baik":
                tambahan = 37.5;
                break;
            case "Baik":
                tambahan = 25;
                break;
            case "Butuh Perbaikan":
                tambahan = 18.75;
                break;
            case "Kurang":
                tambahan = 12.5;
                break;
            case "Sangat Kurang":
                tambahan = 6.25;
                break;
            default:
                tambahan = 0;
                break;
        }
    }

    let tambahanIjazah = 0;

    if (jabatan.includes("Ahli Pertama")) {
        switch (tambahan_ijazah) {
            case "Sangat Baik":
                tambahanIjazah = 18.75 / 4;
                break;
            case "Baik":
                tambahanIjazah = 12.5 / 4;
                break;
            case "Butuh Perbaikan":
                tambahanIjazah = 9.38 / 4;
                break;
            case "Kurang":
                tambahanIjazah = 6.25 / 4;
                break;
            case "Sangat Kurang":
                tambahanIjazah = 3.13 / 4;
                break;
            default:
                tambahanIjazah = 0;
                break;
        }
    } else if (jabatan.includes("Ahli Muda")) {
        switch (tambahan_ijazah) {
            case "Sangat Baik":
                tambahanIjazah = 37.5 / 4;
                break;
            case "Baik":
                tambahanIjazah = 25 / 4;
                break;
            case "Butuh Perbaikan":
                tambahanIjazah = 28.13 / 4;
                break;
            case "Kurang":
                tambahanIjazah = 18.75 / 4;
                break;
            case "Sangat Kurang":
                tambahanIjazah = 6.25 / 4;
                break;
            default:
                tambahanIjazah = 0;
                break;
        }
    } else if (jabatan.includes("Ahli Madya")) {
        switch (tambahan_ijazah) {
            case "Sangat Baik":
                tambahanIjazah = 56.25 / 4;
                break;
            case "Baik":
                tambahanIjazah = 37.5 / 4;
                break;
            case "Butuh Perbaikan":
                tambahanIjazah = 28.13 / 4;
                break;
            case "Kurang":
                tambahanIjazah = 18.75 / 4;
                break;
            case "Sangat Kurang":
                tambahanIjazah = 9.375 / 4;
                break;
            default:
                tambahanIjazah = 0;
                break;
        }
    } else if (jabatan.includes("Ahli Utama")) {
        switch (tambahan_ijazah) {
            case "Sangat Baik":
                tambahanIjazah = 75 / 4;
                break;
            case "Baik":
                tambahanIjazah = 50 / 4;
                break;
            case "Butuh Perbaikan":
                tambahanIjazah = 37.5 / 4;
                break;
            case "Kurang":
                tambahanIjazah = 25 / 4;
                break;
            case "Sangat Kurang":
                tambahanIjazah = 12.5 / 4;
                break;
            default:
                tambahanIjazah = 0;
                break;
        }
    } else if (jabatan.includes("Pemula")) {
        switch (tambahan_ijazah) {
            case "Sangat Baik":
                tambahanIjazah = 5.63 / 4;
                break;
            case "Baik":
                tambahanIjazah = 3.75 / 4;
                break;
            case "Butuh Perbaikan":
                tambahanIjazah = 2.81 / 4;
                break;
            case "Kurang":
                tambahanIjazah = 1.88 / 4;
                break;
            case "Sangat Kurang":
                tambahanIjazah = 0.94 / 4;
                break;
            default:
                tambahanIjazah = 0;
                break;
        }
    } else if (jabatan.includes("Terampil")) {
        switch (tambahan_ijazah) {
            case "Sangat Baik":
                tambahanIjazah = 7.5 / 4;
                break;
            case "Baik":
                tambahanIjazah = 5 / 4;
                break;
            case "Butuh Perbaikan":
                tambahanIjazah = 3.75 / 4;
                break;
            case "Kurang":
                tambahanIjazah = 2.5 / 4;
                break;
            case "Sangat Kurang":
                tambahanIjazah = 1.25 / 4;
                break;
            default:
                tambahanIjazah = 0;
                break;
        }
    } else if (jabatan.includes("Mahir")) {
        switch (tambahan_ijazah) {
            case "Sangat Baik":
                tambahanIjazah = 18.75 / 4;
                break;
            case "Baik":
                tambahanIjazah = 12.5 / 4;
                break;
            case "Butuh Perbaikan":
                tambahanIjazah = 9.38 / 4;
                break;
            case "Kurang":
                tambahanIjazah = 6.25 / 4;
                break;
            case "Sangat Kurang":
                tambahanIjazah = 3.13 / 4;
                break;
            default:
                tambahanIjazah = 0;
                break;
        }
    } else if (jabatan.includes("Penyelia")) {
        switch (tambahan_ijazah) {
            case "Sangat Baik":
                tambahanIjazah = 37.5 / 4;
                break;
            case "Baik":
                tambahanIjazah = 25 / 4;
                break;
            case "Butuh Perbaikan":
                tambahanIjazah = 18.75 / 4;
                break;
            case "Kurang":
                tambahanIjazah = 12.5 / 4;
                break;
            case "Sangat Kurang":
                tambahanIjazah = 6.25 / 4;
                break;
            default:
                tambahanIjazah = 0;
                break;
        }
    }

    return (kreditIntegrasi + tambahan + tambahanIjazah).toFixed(3);
};

export default calculateAkumulasiAk;
