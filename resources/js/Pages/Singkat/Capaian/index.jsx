import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import React, { useEffect, useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";

import Pagination from "@/Components/Pagination";
import CapaianForm from "./CapaianForm";
import dayjs from "dayjs";
import { Form, message } from "antd";
import axios from "axios";
import * as XLSX from "xlsx";
// import Alert from "@/Components/Alert";

import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";
import Table from "./TableCapaian";

const KelolaPak = ({ auth, capaian, search, jabatan, unitKerja }) => {
    const [editForm] = Form.useForm();
    const [addForm] = Form.useForm();
    const [messageApi, contextHolder] = message.useMessage();
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentCapaian, setCurrentCapaian] = useState(null);

    const { errors, flash } = usePage().props;

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const openEditModal = (capaian) => {
        setCurrentCapaian(capaian);
        setIsEditModalOpen(true);
    };

    const closeEditModal = () => setIsEditModalOpen(false);

    const handleDelete = async (id) => {
        try {
            messageApi.open({
                key: "submit-form",
                type: "loading",
                content: "menghapus 1 capaian...",
            });
            // return
            const response = await axios.delete(`/capaian/${id}`);
            messageApi.open({
                key: "submit-form",
                type: "success",
                content: "1 capaian berhasil terhapus",
            });
            setIsEditModalOpen(false);
        } catch (error) {
            // console.log({ error });
            messageApi.open({
                key: "submit-form",
                type: "error",
                content: "terjadi kesalahan server",
            });
        } finally {
            router.get("/kelola-ckp", {}, { preserveState: true });
        }
    };

    const handleSearch = (query) => {
        const url = new URL(`http://localhost:8000/singkat/kelola-ckp`);

        url.searchParams.set("search", query);
        router.get(url, { replace: true });
    };
    const handleSave = async (values) => {
        try {
            messageApi.open({
                key: "submit-form",
                type: "loading",
                content: "menyimpan capaian pegawai",
            });
            let copyBulan = values.bulan;
            values["bulan"] = new Date(copyBulan).getMonth()+1;
            values["tahun"] = new Date(copyBulan).getFullYear();
            // return
            const response = await axios.patch(`/capaian/${values.id}`, values, {
                headers: { "Content-Type": "application/json" },
            });
            messageApi.open({
                key: "submit-form",
                type: "success",
                content: "perubahan telah disimpan",
            });
            router.get("/singkat/kelola-ckp", {}, { preserveState: true });
            setIsEditModalOpen(false);
        } catch (error) {
            // console.log({ error });
            messageApi.open({
                key: "submit-form",
                type: "error",
                content: error.response.data.error,
            });
        } finally {
            router.get("/singkat/kelola-ckp", {search:search}, { preserveState: true });
        }
    };
    const handleAdd = async (values) => {
        let copyBulan = values.bulan;
        values["bulan"] = new Date(copyBulan).getMonth()+1;
        values["tahun"] = new Date(copyBulan).getFullYear();
        // delete values.bulan
        // console.log({values});
        // return
        
        try {
            messageApi.open({
                key: "add-form",
                type: "loading",
                content: "menambahkan capaian pegawai",
            });
            // let tahun_bulan = new Date(values.tahun);
            // let preparedTahun = `${tahun_bulan.getFullYear()}`;
            // const data = { ...values };
            // data.tahun = preparedTahun;
            // return
            const response = await axios.post(`/capaian`, values, {
                headers: { "Content-Type": "application/json" },
            });
            messageApi.open({
                key: "add-form",
                type: "success",
                content: "perubahan telah disimpan",
            });
            router.get("/singkat/kelola-ckp", {}, { preserveState: true });
        } catch (error) {
            console.log({ error });
            messageApi.open({
                key: "add-form",
                type: "error",
                content: "disaster occured",
            });
        } finally {
            router.get("/singkat/kelola-ckp", {}, { preserveState: true });
            setIsModalOpen(false);
        }
    };
    const handleDownload = async (values) => {
        const {data} = await axios.get(route("kelola-ckp.fetch",{search:values}));
           
        
        // return
        


        const workbook = XLSX.utils.book_new();
        const worksheet = XLSX.utils.json_to_sheet(data);

        XLSX.utils.book_append_sheet(workbook, worksheet, "capaian");

        // XLSX.writeFile(workbook, "template_import.xlsx");
        const excelBuffer = XLSX.write(workbook, {
            bookType: "xlsx",
            type: "array",
        });

        const blob = new Blob([excelBuffer], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        });

        // Create a URL for the Blob
        const url = URL.createObjectURL(blob);

        const link = document.createElement("a");
        link.href = url;
        link.download = "data_capaian.xlsx"; // Use the filename from state
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        setOpenUnduhModal(false);
    };
    useEffect(() => {
        // console.log({currentCapaian});
        if (!currentCapaian) return;
        let capaian = { ...currentCapaian };
        // console.log({ capaian });
        console.log({capaian});
        
        capaian.tahun = dayjs(new Date(`${currentCapaian.tahun}-01-01`));
        if(capaian.bulan){
            capaian.bulan=dayjs(`${currentCapaian.tahun}-${currentCapaian.bulan}`,"YYYY-M"
            )
        }
        editForm.setFieldsValue(capaian);
        // editForm.setFieldValue('id', currentCapaian.id);
    }, [currentCapaian]);

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
                        onClick={()=>handleDownload(search)}
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
            {/* <AddCapaianForm
                jabatan={jabatan}
                // capaian={capaian}
                unitKerja={unitKerja}
                visible={isModalOpen}
                onCancel={closeModal}
                role={auth.user.role}
            />
           */}
            <CapaianForm
                jabatan={jabatan}
                // capaian={capaian}
                onFinish={handleAdd}
                form={addForm}
                unitKerja={unitKerja}
                visible={isModalOpen}
                onCancel={closeModal}
                role={auth.user.role}
                type="daftar"
            />

            <CapaianForm
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                capaian={currentCapaian}
                onFinish={handleSave}
                form={editForm}
                title="Ubah CKP"
                okText="Simpan"
                type="edit"
                initPeriod={currentCapaian? currentCapaian.periode:""}
            />
            {/* Export Modal */}
           
        </AuthenticatedLayout>
    );
};

export default KelolaPak;
