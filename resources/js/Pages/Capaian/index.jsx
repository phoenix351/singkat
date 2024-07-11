import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import React, { useEffect, useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";

import Pagination from "@/Components/Pagination";

import AddCapaianForm from "@/Pages/Capaian/AddCapaianForm";
import ExportModal from "@/Pages/Capaian/ExportModal";
import Table from "./TableCapaian";
import CapaianForm from "./CapaianForm";
import dayjs from "dayjs";
import { Form, message } from "antd";
// import Alert from "@/Components/Alert";

const KelolaPak = ({ auth, capaian, search, jabatan, unitKerja }) => {
    const [editForm] = Form.useForm();
    const [messageApi, contextHolder] = message.useMessage();
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentCapaian, setCurrentCapaian] = useState(null);
    const [isExportModalOpen, setIsExportModalOpen] = useState(false);

    const { errors, flash } = usePage().props;

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const openExportModal = () => setIsExportModalOpen(true);
    const closeExportModal = () => setIsExportModalOpen(false);

    const openEditModal = (capaian) => {
        setCurrentCapaian(capaian);
        setIsEditModalOpen(true);
    };

    const closeEditModal = () => setIsEditModalOpen(false);

    const handleDelete = (id) => {
        router.delete(`/kelola-ckp/${id}`).then(() => {
            // Refresh the page or handle post-delete actions here
        });
    };

    const handleSearch = (query) => {
        const url = new URL(`http://localhost:8000/kelola-ckp`);


        url.searchParams.set('search', query);
        router.get(url, { replace: true });
    };
    const handleSave = async (values) => {
        try {
            messageApi.open({
                key: 'submit-form',
                type: 'loading',
                content: 'menyimpan capaian pegawai'
            })
            let tahun_bulan = new Date(values.tahun);
            let preparedTahun = `${tahun_bulan.getFullYear()}`;
            const data = { ...values };
            data.tahun = preparedTahun;
            // return 
            const response = await axios.patch(`/capaian/${values.id}`, data, { headers: { "Content-Type": "application/json" } })
            messageApi.open({
                key: 'submit-form',
                type: 'success',
                content: 'perubahan telah disimpan'
            })
            router.get('/kelola-ckp', {}, { preserveState: true });
            setIsEditModalOpen(false)

        } catch (error) {
            // console.log({ error });
            messageApi.open(
                {
                    key: 'submit-form',
                    type: 'error',
                    content: error.response.data.error
                }
            )
        } finally {
            router.get('/kelola-ckp', {}, { preserveState: true })
        }


    };
    useEffect(() => {
        // console.log({currentCapaian});
        if (!currentCapaian) return
        let capaian = { ...currentCapaian };
        console.log({ capaian });
        capaian.tahun = dayjs(new Date(`${currentCapaian.tahun}-01-01`));
        editForm.setFieldsValue(capaian);
        // editForm.setFieldValue('id', currentCapaian.id);
    }, [currentCapaian])

    return (
        <AuthenticatedLayout user={auth.user}>
            {contextHolder}
            {flash.success && <Alert tipe="success" pesan={flash.success} />}

            {errors && Object.keys(errors).length > 0 && (
                <Alert
                    tipe="error"
                    pesan="Terjadi kesalahan! Silahkan cek form."
                />
            )}

            <Head title={auth.user.role == "viewer" ? "CKP" : "Kelola CKP"} />

            <Breadcrumb
                pageName={auth.user.role == "viewer" ? "CKP" : "Kelola CKP"}
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
                                Tambah capaian
                            </button>
                        )}
                </div>
            </div>

            <div className="flex flex-col gap-10">
                <Table
                    capaian={capaian}
                    onEdit={openEditModal}
                    onDelete={handleDelete}
                    role={auth.user.role}
                />

                <Pagination
                    links={capaian.links}
                    currentPage={capaian.current_page}
                    lastPage={capaian.last_page}
                    total={capaian.total}
                    from={capaian.from}
                    to={capaian.to}
                />
            </div>

            {/* Modal Form */}
            <AddCapaianForm
                jabatan={jabatan}
                // capaian={capaian}
                unitKerja={unitKerja}
                visible={isModalOpen}
                onCancel={closeModal}
                role={auth.user.role}
            />
            {/* <EditCapaianForm
                jabatan={jabatan}
                // capaian
                unitKerja={unitKerja}
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                capaian={currentcapaian}
                role={auth.user.role}
            /> */}

            <CapaianForm
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                capaian={currentCapaian}
                onFinish={handleSave}
                form={editForm}
                title="Ubah CKP"
                okText="Simpan"
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
