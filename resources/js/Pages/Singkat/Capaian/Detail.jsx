import React from "react";
import { Head, usePage, Link } from "@inertiajs/react";
import { Row, Col, List } from "antd";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { format } from "date-fns";
import { id } from "date-fns/locale";

const Detail = ({ auth, capaian, pegawai }) => {
    // const { pegawai } = usePage().props;
    // console.log({ pegawai, capaian });
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Detail CKP" />

            <div className="flex justify-start gap-4.5">
                <Link
                    href={route("singkat")+"/kelola-ckp"}
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
                                <Col span={6}>: {pegawai.tanggal_lahir}</Col>
                            </Row>
                        </div>
                    </div>
                </div>
                <div className="col-span-5 xl:col-span-3">
                    <div className="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                        <div className="border-b border-stroke py-4 px-7 dark:border-strokedark">
                            <h3 className="font-lg font-bold text-black dark:text-white">
                                Capaian Kinerja Pegawai
                            </h3>
                        </div>
                        <div className="p-7 text-2lg">
                            <Row className="mb-5">
                                <Col className="" span={2}></Col>
                                <Col className="font-bold" span={2}>
                                    Tahun
                                </Col>
                                <Col span={4}>{capaian.tahun}</Col>
                                <Col className="font-bold" span={2}>
                                    Periode{" "}
                                </Col>
                                <Col span={4}>
                                    {capaian.periode.charAt(0).toUpperCase() +
                                        capaian.periode.slice(1)}
                                </Col>
                            </Row>
                            <Row className="mb-5">
                                <Col className="" span={2}></Col>
                                <Col className="font-bold" span={2}>
                                    Predikat Kinerja
                                </Col>
                                <Col span={4}>{capaian.predikat.nama}</Col>
                                <Col className="font-bold" span={2}>
                                    Angka Kredit
                                </Col>
                                <Col span={4}>{capaian.angka_kredit}</Col>
                            </Row>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
};

export default Detail;
