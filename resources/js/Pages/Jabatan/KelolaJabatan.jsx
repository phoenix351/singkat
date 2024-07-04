import Authenticated from "@/Layouts/AuthenticatedLayout";
import React, { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";
import Pagination from "@/Components/Pagination";
import TableJabatan from "@/Pages/Jabatan/TableJabatan";
import AddJabatanForm from "../Jabatan/AddJabatanForm";
import EditJabatanForm from "../Jabatan/EditJabatanForm";
import Alert from "@/Components/Alert";

const KelolaJabatan = ({ auth, jabatan, search }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentJabatan, setCurrentJabatan] = useState(null);

    const { flash, errors } = usePage().props;

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const closeEditModal = () => setIsEditModalOpen(false);

    const openEditModal = (jabatan) => {
        setCurrentJabatan(jabatan);
        setIsEditModalOpen(true);
    };
    const handleDelete = (id) => {
        console.log(id);
        router.delete(`/jabatan/${id}`).then(() => {
            // Refresh the page or handle post-delete actions here
        });
    };

    const handleSearch = (query) => {
        router.get("/jabatan", { search: query }, { replace: true });
    };
    return (
        <Authenticated title="Jabatan" user={auth.user}>
            {flash.success && <Alert tipe="success" pesan={flash.success} />}

            {errors && Object.keys(errors).length > 0 && (
                <Alert
                    tipe="error"
                    pesan="Terjadi kesalahan! Silahkan cek form."
                />
            )}

            <Head title="Kelola Jabatan" />

            <Breadcrumb pageName="Kelola Jabatan" />

            <div className="flex flex-row sm:justify-between mb-5">
                <SearchInput
                    initialQuery={search}
                    handleSearch={handleSearch}
                />

                <div>
                    <button
                        onClick={openModal}
                        className=" gap-2.5 rounded-md    inline-flex items-center justify-center bg-meta-3 py-2 px-5  text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4"
                    >
                        Tambah Jabatan
                    </button>
                </div>
            </div>

            <div className="flex flex-col gap-10">
                <TableJabatan
                    jabatan={jabatan}
                    onEdit={openEditModal}
                    onDelete={handleDelete}
                />

                <Pagination
                    links={jabatan.links}
                    currentPage={jabatan.current_page}
                    lastPage={jabatan.last_page}
                    from={jabatan.from}
                    to={jabatan.to}
                    total={jabatan.total}
                />
            </div>

            {/* Modal Form */}
            <AddJabatanForm visible={isModalOpen} onCancel={closeModal} />
            <EditJabatanForm
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                jabatan={currentJabatan}
            />
        </Authenticated>
    );
};

export default KelolaJabatan;
