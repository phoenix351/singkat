import React from "react";
import { usePage, Link } from "@inertiajs/react";
import { Row, Col, List, Space, Typography } from "antd";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { format } from "date-fns";
import { id } from "date-fns/locale";
import dayjs from "dayjs";
import "dayjs/locale/id";

dayjs.locale("id");
const { Text } = Typography;
const DetailPak = ({ auth, histories, pegawai }) => {
    // const { pegawai } = usePage().props;
    const getUsia = (tanggal_lahir) => {
        // Hitung selisih dalam milidetik
        const selisihMilidetik = new Date() - new Date(tanggal_lahir);

        // Konversi selisih ke milidetik menjadi hari
        const selisihHari = Math.floor(
            selisihMilidetik / (1000 * 60 * 60 * 24)
        );

        // Hitung tahun
        const tahun = Math.floor(selisihHari / 365.25); // Asumsikan rata-rata tahun 365.25 hari

        // Hitung sisa hari setelah dikurangi tahun
        const sisaHari = selisihHari % 365.25;

        // Hitung bulan (asumsikan rata-rata bulan 30.44 hari)
        const bulan = Math.floor(sisaHari / 30.44);

        return `${tahun} tahun ${bulan} bulan`;
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            {["admin", "operator", "super admin"].includes(auth.user.role) && (
                <div className="flex justify-start gap-4.5">
                    <Link
                        href="/kelola-pak"
                        className="flex justify-center rounded bg-primary py-2 px-6 font-medium text-gray hover:bg-opacity-90"
                    >
                        Kembali
                    </Link>
                </div>
            )}
            <div className="container mx-auto mt-8">
                <div className="col-span-5 xl:col-span-3 mb-10">
                    <div className="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div className="border-b border-stroke py-4 px-7 dark:border-strokedark">
                            <h3 className="font-lg font-bold text-black dark:text-white">
                                Detail Pegawai
                            </h3>
                        </div>
                        <div className="p-7 text-2lg">
                            <Row className="mb-5">
                                <Col className="" span={2}></Col>
                                <Col className="font-bold" span={2}>
                                    NIP BPS
                                </Col>
                                <Col span={8}>: {pegawai.nip_bps}</Col>
                                <Col span={4} className="font-bold">
                                    Jabatan
                                </Col>
                                <Col span={6}>: {pegawai.jabatan.nama}</Col>
                            </Row>
                            <Row className="mb-5">
                                <Col className="" span={2}></Col>
                                <Col className="font-bold" span={2}>
                                    NIP
                                </Col>
                                <Col span={8}>: {pegawai.nip}</Col>
                                <Col span={4} className="font-bold">
                                    Satuan Kerja
                                </Col>
                                <Col span={6}>: {pegawai.unit_kerja}</Col>
                            </Row>
                            <Row className="mb-5">
                                <Col className="" span={2}></Col>
                                <Col className="font-bold" span={2}>
                                    Nama
                                </Col>
                                <Col span={8}>: {pegawai.nama}</Col>
                                <Col span={4} className="font-bold">
                                    Pangkat / Golongan Ruang
                                </Col>
                                <Col span={6}>
                                    : {pegawai.pangkat_golongan_ruang}
                                </Col>
                            </Row>
                            <Row className="mb-5">
                                <Col className="" span={2}></Col>

                                <Col span={2} className="font-bold">
                                    Usia
                                </Col>
                                <Col span={6}>
                                    : {getUsia(pegawai.tanggal_lahir)}
                                </Col>
                            </Row>
                        </div>
                    </div>
                </div>
                <div className="col-span-5 xl:col-span-3">
                    <div className="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div className="border-b border-stroke py-4 px-7 dark:border-strokedark">
                            <h3 className="font-lg font-bold text-black dark:text-white">
                                Angka Kredit Pegawai
                            </h3>
                        </div>
                        <div className="p-7 text-2lg">
                            <Row className="mb-5">
                                <Col className="" span={2}></Col>
                                <Col className="font-bold" span={4}>
                                    Angka Kredit Konvesional
                                </Col>
                                <Col span={4}>
                                    : {pegawai.angka_kredit_konvesional}
                                </Col>
                                <Col className="font-bold" span={4}>
                                    Predikat
                                </Col>
                                <Col span={4}>: {pegawai.predikat}</Col>
                            </Row>
                            <Row className="mb-5">
                                <Col className="" span={2}></Col>
                                <Col className="font-bold" span={4}>
                                    Angka Kredit Integrasi
                                </Col>
                                <Col span={4}>
                                    : {pegawai.angka_kredit_integrasi}
                                </Col>
                                <Col span={4} className="font-bold">
                                    25% Tambahan Ijazah
                                </Col>
                                <Col span={6}>: {pegawai.tambahan_ijazah}</Col>
                            </Row>
                            <Row className="mb-5">
                                <Col className="" span={2}></Col>
                                <Col className="font-bold" span={4}>
                                    Akumulasi Angka Kredit
                                </Col>
                                <Col span={4}>
                                    : {Number(pegawai.akumulasi_ak).toFixed(3)}
                                </Col>
                                <Col span={4} className="font-bold">
                                    Ijazah Terakhir
                                </Col>
                                <Col span={6}>: {pegawai.ijazah_terakhir}</Col>
                            </Row>
                        </div>
                    </div>
                </div>

                <List
                    header={
                        <h3 className="font-lg font-bold text-black dark:text-white">
                            Histori Akumulasi Angka Kredit
                        </h3>
                    }
                    className="mt-5"
                    bordered
                    dataSource={histories}
                    renderItem={(item) => (
                        <List.Item>
                            {/* {format(
                                new Date(item.created_at),
                                "d MMMM yyyy HH:mm:ss",
                                {
                                    locale: id,
                                }
                            )}{" "} */}
                            <Space style={{display:"flex", justifyContent:'space-between'}}>
                                <Text strong>
                                    Periode :{" "}
                                    {dayjs(
                                        `${item.tahun}-${item.bulan_mulai}`,
                                        "YYYY-M"
                                    ).format("MMMM YYYY")}{" "}
                                    -{" "}
                                    {dayjs(
                                        `${item.tahun}-${item.bulan_akhir}`,
                                        "YYYY-M"
                                    ).format("MMMM YYYY")}
                                </Text>
                                <span>
                                    Akumulasi Angka Kredit :{" "}
                                    {item.akumulasi_ak.toFixed(3)}{" "}
                                </span>
                                <span className="text-success">
                                    (+ {item.angka_kredit}){" "}
                                </span>
                            </Space>
                        </List.Item>
                    )}
                />
            </div>
        </AuthenticatedLayout>
    );
};

export default DetailPak;
