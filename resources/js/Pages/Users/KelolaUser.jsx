import Authenticated from "@/Layouts/AuthenticatedLayout";
import React, { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";
import Pagination from "@/Components/Pagination";
import Table from "@/Pages/Users/TableUser";
import AddUserForm from "./AddUserForm";
import EditUserForm from "./EditUserForm";
import Alert from "@/Components/Alert";

const KelolaUser = ({ auth, users, search }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentUsers, setCurrentUsers] = useState(null);

    const { flash, errors } = usePage().props;

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const closeEditModal = () => setIsEditModalOpen(false);

    const openEditModal = (users) => {
        setCurrentUsers(users);
        setIsEditModalOpen(true);
    };
    const handleDelete = (id) => {
        router.delete(route("singkat.admin.users.destroy",{user:id})).then(() => {
            // Refresh the page or handle post-delete actions here
        });
    };

    const handleSearch = (query) => {
        router.get(route("singkat.admin.users"), { search: query }, { replace: true });
    };
    return (
        <Authenticated title="User" user={auth.user}>
            {flash.success && <Alert tipe="success" pesan={flash.success} />}

            {errors && Object.keys(errors).length > 0 && (
                <Alert
                    tipe="error"
                    pesan="Terjadi kesalahan! Silahkan cek form."
                />
            )}

            <Head title="Kelola Users" />

            <Breadcrumb pageName="Kelola User" />

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
                        Tambah User
                    </button>
                </div>
            </div>

            <div className="flex flex-col gap-10">
                <Table
                    users={users}
                    onEdit={openEditModal}
                    onDelete={handleDelete}
                    role={auth.user.role}
                />

                <Pagination
                    links={users.links}
                    currentPage={users.current_page}
                    lastPage={users.last_page}
                    from={users.from}
                    to={users.to}
                    total={users.total}
                />
            </div>

            {/* Modal Form */}
            <AddUserForm visible={isModalOpen} onCancel={closeModal} />
            <EditUserForm
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                user={currentUsers}
            />
        </Authenticated>
    );
};

export default KelolaUser;
