import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import React, { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Table from "./TablePak";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";

import Pagination from "@/Components/Pagination";

// import AddPegawaiForm from "@/Pages/PAK/AddPegawaiForm";
// import EditPegawaiForm from "@/Pages/PAK/EditPegawaiForm";
import ExportModal from "@/Pages/Singkat/PAK/ExportModal";
// import Alert from "@/Components/Alert";
import { Form, message } from "antd";
import axios from "axios";
import PegawaiForm from "./PegawaiForm";
import dayjs from "dayjs";

const KelolaPak = ({ auth, pegawai, search, jabatan, unitKerja }) => {
    const [editForm] = Form.useForm();
    const [createForm] = Form.useForm();

    const [messageApi, contextHolder] = message.useMessage();

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentPegawai, setCurrentPegawai] = useState(null);
    const [isExportModalOpen, setIsExportModalOpen] = useState(false);

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const openExportModal = () => setIsExportModalOpen(true);
    const closeExportModal = () => setIsExportModalOpen(false);

    const openEditModal = (pegawai) => {
        setCurrentPegawai(pegawai);
        setIsEditModalOpen(true);
    };

    const closeEditModal = () => setIsEditModalOpen(false);

    const handleDelete = async (id) => {
        try {
            messageApi.open({
                type: "loading",
                content: "Menghapus data pegawai",
                key: "handle-delete",
            });
            const response = axios.delete(`/kelola-pak/${id}`);

            messageApi.open({
                type: "success",
                content: "Berhasil menyimpan perubahan",
                key: "handle-delete",
            });
        } catch (error) {
            messageApi.open({
                type: "error",
                content: "Gagal menghapus, periksa kembali !",
                key: "handle-delete",
            });
        } finally {
            // setIsEditModalOpen(false);
            router.reload({
                preserveState: true,
                preserveScroll: true,
                method: "get",
            });
        }
    };

    const handleSearch = (query) => {
        router.get(route("kelola-pak"), { search: query }, { replace: true });
    };
    const handleSave = async (values) => {
        // console.log({values});
        // return;
        try {
            messageApi.open({
                type: "loading",
                content: "Menyimpan perubahan",
                key: "handle-save",
            });
            values["bulan_mulai"] = values["bulan"][0];
            values["bulan_selesai"] = values["bulan"][1];
            delete values["bulan"];
            const response = axios.put(`/kelola-pak/${values.id}`, values, {
                headers: { "Content-Type": "application/json" },
            });

            messageApi.open({
                type: "success",
                content: "Berhasil menyimpan perubahan",
                key: "handle-save",
            });
        } catch (error) {
            messageApi.open({
                type: "error",
                content: "Gagal menyimpan, hubungi pengembang web!",
                key: "handle-save",
            });
        } finally {
            setIsEditModalOpen(false);
            router.reload({
                preserveState: true,
                preserveScroll: true,
                method: "get",
            });
        }
    };
    const handleCreate = async (values) => {
        // console.log({ values });
        // return;
        try {
            messageApi.open({
                type: "loading",
                content: "Menambahkan pegawai",
                key: "handle-create",
            });
            values["bulan_mulai"] = values["bulan"][0];
            values["bulan_selesai"] = values["bulan"][1];
            delete values["bulan"];
            const response = await axios.post(`/kelola-pak`, values, {
                headers: { "Content-Type": "application/json" },
            });

            messageApi.open({
                type: "success",
                content: "Berhasil menambahkan pegawai",
                key: "handle-create",
            });
        } catch (error) {
            messageApi.open({
                type: "error",
                content: "Gagal menambahkan, periksa kembali isian !",
                key: "handle-create",
            });
        } finally {
            setIsModalOpen(false);
            router.reload({ preserveState: true, preserveScroll: true });
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            {contextHolder}

            <Head title={auth.user.role == "viewer" ? "PAK" : "Kelola PAK"} />

            <Breadcrumb
                pageName={auth.user.role == "viewer" ? "PAK" : "Kelola PAK"}
            />

            <div className="flex flex-row sm:justify-between mb-5">
                <SearchInput
                    initialQuery={search}
                    handleSearch={handleSearch}
                />

                <div>
                    <button
                        onClick={openExportModal}
                        type="button"
                        className="inline-flex items-center justify-center gap-2.5 rounded-md bg-primary py-2 px-5 text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4"
                    >
                        <span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                strokeWidth={1.5}
                                stroke="currentColor"
                                className="size-4"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"
                                />
                            </svg>
                        </span>
                        Unduh Data
                    </button>

                    {["operator"].includes(auth.user.role) && (
                        <button
                            onClick={() => {
                                createForm.resetFields();
                                createForm.setFieldsValue({
                                    id: 340060268,
                                    nip_bps: "340060268",
                                    nip: "199810132021041001",
                                    nama: "Ponimin, S.Tr.Stat.",
                                    jabatan_id: "39",
                                    unit_kerja: "BPS P",
                                    pangkat_golongan_ruang:
                                        "Penata Tk.I / IIId",
                                    angka_kredit_konvensional: "313.898",
                                    angka_kredit_integrasi: "113.898",
                                    predikat_kinerja: "Sangat Baik",
                                    tambahan_ijazah: null,
                                    akumulasi_ak: "151.398",
                                    ijazah_terakhir: "S1/DIV/Sederajat",
                                    tanggal_lahir: "1971-02-05",
                                    nama_jabatan: "Statistisi Ahli Muda",
                                    angka_kredit_akumulasi: 151.398,
                                    tambahan_ijazah: "Baik",
                                    bulan: [dayjs(), dayjs()],
                                    predikat_id: "1",
                                    angka_kredit_dasar: 40,
                                    akumulasi_ak: 47.125,
                                });
                                openModal();
                            }}
                            className=" gap-2.5 rounded-md    inline-flex items-center justify-center bg-meta-3 py-2 px-5  text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4"
                        >
                            Tambah Pegawai
                        </button>
                    )}
                </div>
            </div>

            <div className="flex flex-col gap-10">
                <Table
                    pegawai={pegawai}
                    onEdit={openEditModal}
                    onDelete={handleDelete}
                    role={auth.user.role}
                />

                <Pagination
                    links={pegawai.links}
                    currentPage={pegawai.current_page}
                    lastPage={pegawai.last_page}
                    total={pegawai.total}
                    from={pegawai.from}
                    to={pegawai.to}
                />
            </div>

            {/* Modal Form */}
            {/* <AddPegawaiForm
                jabatan={jabatan}
                unitKerja={unitKerja}
                visible={isModalOpen}
                onCancel={closeModal}
                role={auth.user.role}
            /> */}
            <PegawaiForm
                key="create-form"
                jabatan={jabatan}
                unitKerja={unitKerja}
                visible={isModalOpen}
                onCancel={closeModal}
                role={auth.user.role}
                onFinish={handleCreate}
                type="create"
                title="Tambah Pegawai"
                form={createForm}
            />
            {/* <EditPegawaiForm
                jabatan={jabatan}
                unitKerja={unitKerja}
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                pegawai={currentPegawai}
                role={auth.user.role}
            /> */}
            <PegawaiForm
                key="edit-form"
                jabatan={jabatan}
                unitKerja={unitKerja}
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                pegawai={currentPegawai}
                role={auth.user.role}
                onFinish={handleSave}
                type="edit"
                title="Ubah Data Pegawai"
                form={editForm}
            />

            {/* Export Modal */}
            <ExportModal
                visible={isExportModalOpen}
                onCancel={closeExportModal}
            />
        </AuthenticatedLayout>
    );
};

export default KelolaPak;
