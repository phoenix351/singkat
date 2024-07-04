import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import React, { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Table from "./TablePak";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";

import Pagination from "@/Components/Pagination";

import AddPegawaiForm from "@/Pages/PAK/AddPegawaiForm";
import EditPegawaiForm from "@/Pages/PAK/EditPegawaiForm";
import ExportModal from "@/Pages/PAK/ExportModal";
import Alert from "@/Components/Alert";

const KelolaPak = ({ auth, pegawai, search, jabatan, unitKerja }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentPegawai, setCurrentPegawai] = useState(null);
    const [isExportModalOpen, setIsExportModalOpen] = useState(false);

    const { errors, flash } = usePage().props;

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const openExportModal = () => setIsExportModalOpen(true);
    const closeExportModal = () => setIsExportModalOpen(false);

    const openEditModal = (pegawai) => {
        setCurrentPegawai(pegawai);
        setIsEditModalOpen(true);
    };

    const closeEditModal = () => setIsEditModalOpen(false);

    const handleDelete = (id) => {
        router.delete(`/kelola-pak/${id}`).then(() => {
            // Refresh the page or handle post-delete actions here
        });
    };

    const handleSearch = (query) => {
        router.get("/kelola-pak", { search: query }, { replace: true });
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            {flash.success && <Alert tipe="success" pesan={flash.success} />}

            {errors && Object.keys(errors).length > 0 && (
                <Alert
                    tipe="error"
                    pesan="Terjadi kesalahan! Silahkan cek form."
                />
            )}

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

                    {(auth.user.role === "admin" ||
                        auth.user.role === "super admin") && (
                        <button
                            onClick={openModal}
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
            <AddPegawaiForm
                jabatan={jabatan}
                unitKerja={unitKerja}
                visible={isModalOpen}
                onCancel={closeModal}
                role={auth.user.role}
            />
            <EditPegawaiForm
                jabatan={jabatan}
                unitKerja={unitKerja}
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                pegawai={currentPegawai}
                role={auth.user.role}
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
