import Authenticated from "@/Layouts/AuthenticatedLayout";
import React, { useEffect, useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";

import axios from "axios";
import { Button, Form, message, Popconfirm, Table } from "antd";
import JenisSKForm from "./JenisSKForm";
import { DeleteOutlined, EditOutlined, PlusOutlined } from "@ant-design/icons";

const KelolaJenisSK = ({ auth }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [daftarJenisSk, setDaftarJenisSk] = useState([]);

    const [messageApi, contextHolder] = message.useMessage();
    const [form] = Form.useForm();

    const openModal = () => {
        form.resetFields();
        setIsModalOpen(true);
    };
    const closeModal = () => setIsModalOpen(false);

    const closeEditModal = () => setIsEditModalOpen(false);

    const onEdit = (jenisSk) => {
        // setCurrentjenisSk(jenisSk);
        form.setFieldsValue(jenisSk);
        setIsEditModalOpen(true);
    };
    async function handleDelete(id) {
        console.log("asasdadssad");

        try {
            messageApi.open({
                type: "loading",
                key: "handle-delete",
                content: "Menghapus data...",
            });

            const response = await axios.delete(
                `/singkat/admin/jenis-sk/${id}`
            );
            messageApi.open({
                type: "success",
                key: "handle-delete",
                content: "Berhasil menghapus data",
            });
        } catch (error) {
            messageApi.open({
                type: "error",
                key: "handle-delete",
                content: "Terjadi kesalahan",
            });
        } finally {
            // setIsModalOpen(false);
            fetchJenisSk();
        }
    }
    async function fetchJenisSk() {
        try {
            const { data } = await axios.get("/api/jenis-sk");
            console.log({ data });

            setDaftarJenisSk(data);
        } catch (error) {
            console.error("failed to fetch data");
        }
    }
    const handleSearch = (query) => {
        router.get("/jenis-sk", { search: query }, { replace: true });
    };
    const handleAdd = async (values) => {
        try {
            messageApi.open({
                type: "loading",
                key: "handle-add",
                content: "Menambahkan data...",
            });

            const response = await axios.post(
                `/singkat/admin/jenis-sk`,
                values,
                { headers: { "Content-Type": "application/json" } }
            );
            messageApi.open({
                type: "success",
                key: "handle-add",
                content: "Berhasil menambahkan data",
            });
        } catch (error) {
            messageApi.open({
                type: "error",
                key: "handle-add",
                content: "Terjadi kesalahan",
            });
        } finally {
            setIsModalOpen(false);
            fetchJenisSk();
        }
    };
    const handleSave = async (values) => {
        try {
            messageApi.open({
                type: "loading",
                key: "handle-save",
                content: "Menyimpan perubahan...",
            });

            const response = await axios.put(
                `/singkat/admin/jenis-sk`,
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
            fetchJenisSk();
        }
    };
    useEffect(() => {
        fetchJenisSk();
    }, []);

    return (
        <Authenticated title="Jenis SK" user={auth.user}>
            {contextHolder}

            <Head title="Kelola Jenis SK" />

            <Breadcrumb pageName="Kelola Jenis SK" />

            <div className="flex flex-row sm:justify-between mb-5">
                {/* <SearchInput
                    initialQuery={search}
                    handleSearch={handleSearch}
                /> */}

                <div>
                    <button
                        onClick={openModal}
                        className=" gap-2.5 rounded-md    inline-flex items-center justify-center bg-meta-3 py-2 px-5  text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-5 mr-4"
                    >
                        <PlusOutlined /> Tambah Jenis SK
                    </button>
                </div>
            </div>

            <div className="flex flex-col gap-10">
                <Table
                    dataSource={daftarJenisSk}
                    columns={[
                        { title: "Nomor", dataIndex: "id" },
                        { title: "Jenis SK", dataIndex: "nama" },
                        {
                            title: "Aksi",
                            render: (_, record) => (
                                <Button onClick={() => onEdit(record)}>
                                    <EditOutlined /> Ubah
                                </Button>
                            ),
                        },
                        {
                            title: "Hapus",
                            render: (_, record) => (
                                <Button onClick={() => handleDelete(record.id)}>
                                    <DeleteOutlined /> Hapus
                                </Button>
                            ),
                        },
                    ]}
                />
            </div>

            {/* Modal Form */}
            <JenisSKForm
                visible={isModalOpen}
                onCancel={closeModal}
                onFinish={handleAdd}
                okText="Tambahkan"
                cancelText="Batal"
                form={form}
            />
            <JenisSKForm
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                onFinish={handleSave}
                form={form}
                okText="Simpan"
                cancelText="Batal"
            />
        </Authenticated>
    );
};

export default KelolaJenisSK;
