import React, { useEffect, useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import dayjs from "dayjs";
import { Button, Form, message } from "antd";
import axios from "axios";
import * as XLSX from "xlsx";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Pagination from "@/Components/Pagination";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";
import Table from "./Table";
import PAKForm from "./PAKForm";

const KelolaPak = ({ auth, capaian, search, jabatan, unitKerja }) => {
    const [editForm] = Form.useForm();
    const [addForm] = Form.useForm();
    const [messageApi, contextHolder] = message.useMessage();
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const { errors, flash } = usePage().props;
    function preparePAK(values) {
        let bulan_mulai = values.bulan[0];
        let bulan_selesai = values.bulan[1];
        // Add one day to bulan_selesai before formatting it
        const adjustedBulanSelesai = new Date(bulan_selesai);
        adjustedBulanSelesai.setDate(adjustedBulanSelesai.getDate() + 1);
        const adjustedBulanMulai = new Date(bulan_mulai);
        adjustedBulanMulai.setDate(adjustedBulanMulai.getDate() + 1);
        const adjustedTMT = new Date(values.tmt_sk);
        adjustedTMT.setDate(adjustedTMT.getDate() + 1);
        values["bulan_mulai"] = new Date(adjustedBulanMulai)
            .toISOString()
            .slice(0, 10);
        values["bulan_selesai"] = new Date(adjustedBulanSelesai)
            .toISOString()
            .slice(0, 10);
            
        values["tmt_sk"] = new Date(adjustedTMT)
            .toISOString()
            .slice(0, 10);

        delete values["bulan"];
        if (!values.file) {
            delete values["file"];
        }
        return values;
    }
   
    const openModal = () => {
        addForm.resetFields();
        setIsModalOpen(true);
    };
    const closeModal = () => setIsModalOpen(false);

    const openEditModal = (capaian) => {
        editForm.resetFields()
        const bulan_mulai = dayjs(capaian.bulan_mulai, "YYYY-MM-DD");
        const bulan_selesai = dayjs(capaian.bulan_selesai, "YYYY-MM-DD");
        capaian.bulan = [bulan_mulai, bulan_selesai];
        capaian.tmt_sk = dayjs(capaian.tmt_sk, "YYYY-MM-DD");
        if (capaian.predikat_id) {
            capaian.predikat_id = Number(capaian.predikat_id);
        }
        if (capaian.jenis_sk) {
            capaian.jenis_sk = Number(capaian.jenis_sk);
        }

        // capaian["id"] = String(capaian["id"]);
        capaian["pegawai_id"] = String(capaian["pegawai_id"]);
        // console.log({capaian});
        
        editForm.setFieldsValue(capaian);

        setIsEditModalOpen(true);
    };

    const closeEditModal = () => setIsEditModalOpen(false);

    const handleDelete = async (id) => {
        try {
            messageApi.open({
                key: "submit-form",
                type: "loading",
                content: "menghapus 1 PAK...",
            });
            // return
            const response = await axios.delete(
                route("singkat.admin.pak.destroy", {
                    pak: id,
                    _token: editForm.getFieldValue("_token"),
                })
            );
            messageApi.open({
                key: "submit-form",
                type: "success",
                content: "1 PAK berhasil terhapus",
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
            router.reload({ preserveState: true, preserveScroll: true });
        }
    };

    const handleSearch = (query) => {
        const url = new URL(route("singkat.admin.pak"));

        url.searchParams.set("search", query);
        router.get(url, { replace: true });
    };
    const handleSave = async (values) => {
        values = preparePAK(values);
        const contentType = values.hasOwnProperty("file")
            ? "multipart/form-data"
            : "application/json";
        try {
            messageApi.open({
                key: "submit-form",
                type: "loading",
                content: "menyimpan perubahan...",
            });
            const { data } = await axios.get(route("api.token.csrf"));

            const response = await axios.post(
                route("singkat.admin.pak.update", { pak: values.id }),
                {...values,_token:data},
                
                {
                    headers: { "Content-Type": contentType },
                }
            );
            messageApi.open({
                key: "submit-form",
                type: "success",
                content: "perubahan telah disimpan",
            });
            setIsEditModalOpen(false);
        } catch (error) {
            console.log(error);

            messageApi.open({
                key: "submit-form",
                type: "error",
                content: error.response.data.error,
            });
        } finally {
            router.reload({ preserveState: true, preserveScroll: true });
        }
    };

    const handleAdd = async (values) => {
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

            const response = await axios.post(
                route("singkat.admin.pak.store"),
                values,
                {
                    headers: { "Content-Type": "multipart/form-data" },
                }
            );
            messageApi.open({
                key: "add-form",
                type: "success",
                content: "PAK sudah ditambahkan",
            });
            // router.get("/singkat/kelola-ckp", {}, { preserveState: true });
        } catch (error) {
            messageApi.open({
                key: "add-form",
                type: "error",
                content: error.response.data.message,
            });
        } finally {
            router.reload({ preserveState: true, preserveScroll: true });
        }
    };
    const handleDownload = async (values) => {
        const { data } = await axios.get(
            route("singkat.admin.pak.fetch", { search: values })
        );

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

            <Head
                title={
                    auth.user.role == "viewer"
                        ? "Dokumen Penetapan Angka Kredit(PAK)"
                        : "Kelola Dokumen Penetapan Angka Kredit(PAK)"
                }
            />

            <Breadcrumb
                pageName={
                    auth.user.role == "viewer"
                        ? "Dokumen Penetapan Angka Kredit(PAK)"
                        : "Kelola Dokumen Penetapan Angka Kredit(PAK)"
                }
            />

            <div className="flex flex-row sm:justify-between mb-5">
                <SearchInput
                    initialQuery={search}
                    handleSearch={handleSearch}
                />

                <div>
                    <button
                        onClick={() => handleDownload(search)}
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
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="1em"
                                height="1em"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="currentColor"
                                    d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6z"
                                ></path>
                            </svg>
                            Tambah PAK
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

            <PAKForm
                jabatan={jabatan}
                // capaian={capaian}
                onFinish={handleAdd}
                form={addForm}
                unitKerja={unitKerja}
                visible={isModalOpen}
                onCancel={closeModal}
                role={auth.user.role}
                type="daftar"
                title={"Tambah Capaian Pegawai"}
            />

            <PAKForm
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                onFinish={handleSave}
                form={editForm}
                title="Ubah Capaian Pegawai"
                okText="Simpan"
                type="edit"
            />
            {/* Export Modal */}
        </AuthenticatedLayout>
    );
};

export default KelolaPak;
