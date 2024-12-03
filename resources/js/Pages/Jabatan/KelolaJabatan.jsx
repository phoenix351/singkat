import Authenticated from "@/Layouts/AuthenticatedLayout";
import React, { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";
import Pagination from "@/Components/Pagination";
import TableJabatan from "@/Pages/Jabatan/TableJabatan";
import AddJabatanForm from "./AddJabatanForm";
import JabatanForm from "./JabatanForm";
import Alert from "@/Components/Alert";
import axios from "axios";
import { message } from "antd";
import { PlusOutlined } from "@ant-design/icons";

const KelolaJabatan = ({ auth, jabatan, search }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentJabatan, setCurrentJabatan] = useState(null);

    const { flash, errors } = usePage().props;

    const [messageApi, contextHolder] = message.useMessage();

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
        router.get(
            route("singkat")+"/admin/jabatan",
            { search: query },
            { replace: true }
        );
    };
    async function handleAdd(values){
        const url = route("singkat")+'/admin/jabatan'
        values = preparePAK(values);
        if (values.file === null) {
            messageApi.open({
                key: "add-form",
                type: "error",
                content: "Mohon lampirkan dokumen PAK",
            });
            addForm.setFields({
                file: {
                    errors: ["mohon tambahkan file"],
                },
            });
            return;
        }

        try {
            messageApi.open({
                key: "add-form",
                type: "loading",
                content: "menambahkan PAK...",
            });

            const response = await axios.post(route("singkat")+`/capaian`, values, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            messageApi.open({
                key: "add-form",
                type: "success",
                content: "PAK sudah ditambahkan",
            });
            // router.get("/singkat/kelola-ckp", {}, { preserveState: true });
        } catch (error) {
            console.log({ error });
            messageApi.open({
                key: "add-form",
                type: "error",
                content: error.response.data.message,
            });
        } finally {
            router.get(
                route("singkat")+"/kelola-ckp",
                { search: search },
                { preserveState: true }
            );
            setIsModalOpen(false);
        }
    }
    const handleSave = async (values) => {
        try {
            messageApi.open({
                type: "loading",
                key: "handle-save",
                content: "Menyimpan perubahan...",
            });

            const response = await axios.patch(
                route("singkat")+`/jabatan/${values.id}`,
                values,
                { headers: { "Content-Type": "application/json" } }
            );
            messageApi.open({
                type: "success",
                key: "handle-save",
                content: "Berhasil menyimpan perubahan",
            });
        } catch (error) {
            messageApi.open({
                type: "error",
                key: "handle-save",
                content: "Terjadi kesalahan",
            });
        } finally {
            setIsEditModalOpen(false);
            router.reload({ preserveState: true });
        }
    };
    return (
        <Authenticated title="Jabatan" user={auth.user}>
            {contextHolder}

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
                        <PlusOutlined />
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
            <JabatanForm visible={isModalOpen} onFinish={handleAdd} onCancel={closeModal} />
            <JabatanForm
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                jabatan={currentJabatan}
                onFinish={handleSave}
            />
        </Authenticated>
    );
};

export default KelolaJabatan;
