import Authenticated from "@/Layouts/AuthenticatedLayout";
import React, { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";
import Pagination from "@/Components/Pagination";
import TableUnitKerja from "@/Pages/UnitKerja/TableUnitKerja";
import AddUnitKerjaForm from "../UnitKerja/AddUnitKerjaForm";
import EditUnitKerjaForm from "../UnitKerja/EditUnitKerjaForm";
import Alert from "@/Components/Alert";

const KelolaUnitKerja = ({ auth, unitKerja, search }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentUnitKerja, setCurrentUnitKerja] = useState(null);

    const { flash, errors } = usePage().props;

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const closeEditModal = () => setIsEditModalOpen(false);

    const openEditModal = (unitKerja) => {
        setCurrentUnitKerja(unitKerja);
        setIsEditModalOpen(true);
    };
    const handleDelete = (id) => {
        router.delete(route("singkat.admin.unit-kerja.destroy",{unitKerja:id})).then(() => {
            // Refresh the page or handle post-delete actions here
        });
    };

    const handleSearch = (query) => {
        router.get(route("singkat.admin.unit-kerja"), { search: query }, { replace: true });
    };
    return (
        <Authenticated title="Unit Kerja" user={auth.user}>
            {flash.success && <Alert tipe="success" pesan={flash.success} />}

            {errors && Object.keys(errors).length > 0 && (
                <Alert
                    tipe="error"
                    pesan="Terjadi kesalahan! Silahkan cek form."
                />
            )}
            <Head title="Kelola Satuan Kerja" />

            <Breadcrumb pageName="Kelola Satuan Kerja" />

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
                        Tambah Satuan Kerja
                    </button>
                </div>
            </div>

            <div className="flex flex-col gap-10">
                <TableUnitKerja
                    unitKerja={unitKerja}
                    onEdit={openEditModal}
                    onDelete={handleDelete}
                />

                <Pagination
                    links={unitKerja.links}
                    currentPage={unitKerja.current_page}
                    lastPage={unitKerja.last_page}
                    from={unitKerja.from}
                    to={unitKerja.to}
                    total={unitKerja.total}
                />
            </div>

            {/* Modal Form */}
            <AddUnitKerjaForm visible={isModalOpen} onCancel={closeModal} />
            <EditUnitKerjaForm
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                unitKerja={currentUnitKerja}
            />
        </Authenticated>
    );
};

export default KelolaUnitKerja;
