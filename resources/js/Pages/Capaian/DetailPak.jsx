import React from "react";
import { usePage, Link } from "@inertiajs/react";
import { Row, Col, List } from "antd";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { format } from "date-fns";
import { id } from "date-fns/locale";

const DetailPak = ({ auth, histories }) => {
    const { pegawai } = usePage().props;

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="flex justify-start gap-4.5">
                <Link
                    href="/kelola-pak"
                    className="flex justify-center rounded bg-primary py-2 px-6 font-medium text-gray hover:bg-opacity-90"
                >
                    Kembali
                </Link>
            </div>
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
                                <Col span={6}>: {pegawai.jabatan}</Col>
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
                                <Col className="font-bold" span={2}>
                                    TMT Pensiun
                                </Col>
                                <Col span={8}>: {pegawai.tmt_pensiun}</Col>
                                <Col span={4} className="font-bold">
                                    Usia
                                </Col>
                                <Col span={6}>: {pegawai.usia}</Col>
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
                                <Col span={4}>: {pegawai.akumulasi_ak}</Col>
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
                            {format(new Date(item.created_at), "d MMMM yyyy", {
                                locale: id,
                            })}{" "}
                            - Akumulasi Angka Kredit : {item.akumulasi_ak}
                        </List.Item>
                    )}
                />
            </div>
        </AuthenticatedLayout>
    );
};

export default DetailPak;
