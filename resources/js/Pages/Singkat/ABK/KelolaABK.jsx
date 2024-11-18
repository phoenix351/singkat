import Authenticated from "@/Layouts/AuthenticatedLayout";
import React, { useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import Breadcrumb from "@/Components/Breadcrumb";
import SearchInput from "@/Components/SearchInput";
import Pagination from "@/Components/Pagination";
import TableABK from "../ABK/TableABK";
import AddAbkForm from "./AddAbkForm";
import EditAbkForm from "./EditAbkForm";
import Alert from "@/Components/Alert";
import * as XLSX from "xlsx";

const KelolaABK = ({ auth, abk, search, jabatan, unitKerja }) => {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [currentABK, setCurrentABK] = useState(null);

    const { errors, flash } = usePage().props;

    const openModal = () => setIsModalOpen(true);
    const closeModal = () => setIsModalOpen(false);

    const closeEditModal = () => setIsEditModalOpen(false);

    const openEditModal = (abk) => {
        setCurrentABK(abk);
        setIsEditModalOpen(true);
    };
    const handleDelete = (id) => {
        router.delete(`/kelola-abk/${id}`).then(() => {
            // Refresh the page or handle post-delete actions here
        });
    };
    const handleDownload = async (values) => {
        const { data } = await axios.get(
            route("abk.fetch", { search: values })
        );

        // return
        const jabatanData = data.reduce((acc, item) => {
            const nomor_urut_kepka = item.nomor_urut_kepka;
            if (!acc[nomor_urut_kepka]) {
                acc[nomor_urut_kepka] = [];
            }
            acc[nomor_urut_kepka].push(item);
            return acc;
        }, {});

        // kelompokkan data berdasarkan jabatan yang sama
        // nomor, nama jabatan , abk, ...
        const satker = {};
        const preparedData = [];
        for (let indexJabatan of Object.keys(jabatanData)) {
            let currentJabatan = jabatanData[indexJabatan];
            currentJabatan.sort((a, b) => a.unit_kerja_id - b.unit_kerja_id);
            let currentValue = [
                currentJabatan[0].nomor_urut_kepka,
                currentJabatan[0].jabatan,
            ];
            currentJabatan.forEach((abk) => {
                if (!satker[abk.unit_kerja_id]) {
                    satker[abk.unit_kerja_id] = abk.unit_kerja;
                }
                currentValue.push(
                    ...[abk.abk, abk.eksisting, abk.kebutuhan_pegawai]
                );
            });
            preparedData.push(currentValue);
            // console.log(indexJabatan);
        }

        // console.log({ satker });
        // return;

        const workbook = XLSX.utils.book_new();
        const worksheet = {};
        // Define the header with multiple levels
        let header = [
            ["No", "Nama Jabatan"], // First header row
            ["No", "Nama Jabatan"], // Second header row
            ["No", "Nama Jabatan"], // Third
        ];
        let merges = [
            { s: { r: 0, c: 0 }, e: { r: 2, c: 0 } }, // Merge 'No' vertically (rowspan 3)
            { s: { r: 0, c: 1 }, e: { r: 2, c: 1 } }, // Merge 'Nama' vertically (rowspan 3)
        ];
        let index = 0;
        for (let unit_kerja of Object.values(satker)) {
            const firstRow = [unit_kerja,"",""];
            const firstMerge = {
                s: { r: 0, c: index + 2 },
                e: { r: 0, c: index + 4 },
            };
            const secondRow = ["KEPKA 182 TAHUN 2024","",""];
            const secondMerge = {
                s: { r: 1, c: index + 2 },
                e: { r: 1, c: index + 4 },
            };
            const thirdRow = ["ABK", "Eksisting", "Kebutuhan Pegawai"];

            header[0].push(...firstRow);
            header[1].push(...secondRow);
            header[2].push(...thirdRow);

            merges.push(firstMerge);
            merges.push(secondMerge);
            // merge unit kerja
            index = index + 3;
        }
        // console.log({ header,merges });
        // return

        // Merge the cells to create a colspan and rowspan effect
        worksheet["!merges"] = merges;
        // Add the header to the worksheet
        XLSX.utils.sheet_add_aoa(worksheet, header);
        XLSX.utils.sheet_add_aoa(worksheet, preparedData, { origin: -1 });

        // const worksheet = XLSX.utils.json_to_sheet(data);

        XLSX.utils.book_append_sheet(workbook, worksheet, "abk");

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
        link.download = "data_abk.xlsx"; // Use the filename from state
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        setOpenUnduhModal(false);
    };
    const handleSearch = (query) => {
        router.get("/singkat/kelola-abk", { search: query }, { replace: true });
    };

    return (
        <Authenticated title="User" user={auth.user}>
            {flash.success && <Alert tipe="success" pesan={flash.success} />}
            {errors.abk && <Alert tipe="error" pesan={errors.abk} />}
            {errors.jabatan && <Alert tipe="error" pesan={errors.jabatan} />}
            {errors.unit_kerja && (
                <Alert tipe="error" pesan={errors.unit_kerja} />
            )}
            {errors.eksisting && (
                <Alert tipe="error" pesan={errors.eksisting} />
            )}

            <Head title={auth.user.role == "viewer" ? "ABK" : "Kelola ABK"} />

            <Breadcrumb
                pageName={auth.user.role == "viewer" ? "ABK" : "Kelola ABK"}
            />

            <div className="flex flex-row sm:justify-between mb-5">
                <SearchInput
                    initialQuery={search}
                    handleSearch={handleSearch}
                />

                <div>
                    <button
                        onClick={() => handleDownload(search)}
                        // href="/export-pegawai?columns=id,nip_bps,nip,nama,jabatan,unit_kerja,pangkat_golongan_ruang,angka_kredit_konvensional,angka_kredit_integrasi,predikat_kinerja,tambahan_ijazah,akumulasi_ak,ijazah_terakhir,tb,usia_per_3_januari,created_at,updated_at"
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
                            Tambah ABK
                        </button>
                    )}
                </div>
            </div>

            <div className="flex flex-col gap-10">
                <TableABK
                    abk={abk}
                    onEdit={openEditModal}
                    onDelete={handleDelete}
                    role={auth.user.role}
                />

                <Pagination
                    links={abk.links}
                    currentPage={abk.current_page}
                    lastPage={abk.last_page}
                    from={abk.from}
                    to={abk.to}
                    total={abk.total}
                />
            </div>

            {/* Modal Form */}
            <AddAbkForm
                visible={isModalOpen}
                onCancel={closeModal}
                jabatan={jabatan}
                unitKerja={unitKerja}
            />
            <EditAbkForm
                visible={isEditModalOpen}
                onCancel={closeEditModal}
                abk={currentABK}
                jabatan={jabatan}
                unitKerja={unitKerja}
                role={auth.user.role}
            />
        </Authenticated>
    );
};

export default KelolaABK;
