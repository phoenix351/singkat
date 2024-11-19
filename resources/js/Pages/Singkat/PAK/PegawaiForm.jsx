import React, { useEffect, useState } from "react";
import { Modal, Form, Input, Select, InputNumber, DatePicker } from "antd";
import dayjs from "dayjs";
import axios from "axios";

const { RangePicker } = DatePicker;

const validateNip = (_, value) => {
    if (!value) {
        return Promise.reject(new Error("NIP tidak boleh kosong."));
    }
    if (/^\d{18}$/.test(value)) {
        return Promise.resolve();
    }
    return Promise.reject(new Error("NIP harus terdiri dari 18 angka."));
};

const validateNipBps = (_, value) => {
    if (!value) {
        return Promise.reject(new Error("NIP BPS tidak boleh kosong."));
    }

    if (/^\d{9}$/.test(value)) {
        return Promise.resolve();
    }
    return Promise.reject(
        new Error("NIP BPS harus tediri dari 9 digit angka.")
    );
};

const PegawaiForm = ({
    visible,
    onCancel,
    pegawai,
    jabatan,
    unitKerja,
    role,
    onFinish,
    type,
    title,
    form,
}) => {
    const calculateAkumulasi = async () => {
        const validated = await form.validateFields();
        console.log({ validated });

        const angka_kredit_dasar =
            form.getFieldValue("angka_kredit_dasar") || 0;
        const selectedJabatan = form.getFieldValue("jabatan_id");

        const { data } = await axios.get(`/api/jabatans/${selectedJabatan}`);

        const angkaKreditTahunan = data.angka_kredit || 0;
        const ijazahKeAngkaKredit = {
            "SD/sederajat": 0,
            "SLTP/sederajat": 0,
            "SLTA/sederajat": 0,
            DI: 0,
            DII: 0,
            DIII: 0,
            "S1/DIV/sederajat": 0,
            S2: 1,
            S3: 1,
        };

        const ijazah_terakhir = form.getFieldValue("ijazah_terakhir");
        let tambahan_ijazah = 0;
        if (
            ijazah_terakhir &&
            ijazahKeAngkaKredit.hasOwnProperty(ijazah_terakhir)
        ) {
            tambahan_ijazah =
                angkaKreditTahunan * ijazahKeAngkaKredit[ijazah_terakhir];
        }

        const fromMonth = new Date(form.getFieldValue("bulan")[0]).getMonth();
        const toMonth = new Date(form.getFieldValue("bulan")[1]).getMonth();
        const numberMonths = toMonth - fromMonth + 1;
        const predikat = form.getFieldValue("predikat_id");

        const tambahan_predikat =
            (angkaKreditTahunan *
                predikats[predikat - 1].nilai *
                numberMonths) /
            12;

        const akumulasi_ak =
            angka_kredit_dasar + tambahan_ijazah + tambahan_predikat;

        form.setFieldValue("akumulasi_ak", akumulasi_ak);
    };

    const [predikats, setPredikats] = useState([]);
    const fetchPredikats = async () => {
        try {
            const { data } = await axios.get("/api/predikats");
            // console.log({ data });
            setPredikats(data);
        } catch (error) {
            console.error("Error when get predikat data");
        }
    };

    useEffect(() => {
        fetchPredikats();
    }, []);

    useEffect(() => {
        if (pegawai) {
            console.log({pegawai});
            
            if (pegawai.bulan_mulai) {
                pegawai.bulan = [dayjs(pegawai.bulan_mulai), dayjs(pegawai.bulan_selesai)];
            } else{
                pegawai.bulan = [null,null]
            }
            form.setFieldsValue(pegawai);
            // form.setFieldValue(
            //     "akumulasi_ak",
            //     form.getFieldValue("angka_kredit_dasar") || 0
            // );
        }
    }, [pegawai]);
    const daftarPangkat = [
        {
            label: "Juru Muda/Ia",
            value: "Juru Muda/Ia",
        },
        { label: "Juru Muda Tingkat I/Ib", value: "Juru Muda Tingkat I/Ib" },

        { label: "Juru/Ic", value: "Juru/Ic" },

        { label: "Juru Tingkat I/Id", value: "Juru Tingkat I/Id" },

        { label: "Juru Pengatur Muda/IIa", value: "Juru Pengatur Muda/IIa" },

        {
            label: "Juru Pengatur Muda Tingkat I/IIb",
            value: "Juru Pengatur Muda Tingkat I/IIb",
        },

        { label: "Juru Pengatur/IIc", value: "Juru Pengatur/IIc" },

        {
            label: "Juru Pengatur Tingkat I/IId",
            value: "Juru Pengatur Tingkat I/IId",
        },
        { label: "Juru Penata Muda/IIIa", value: "Juru Penata Muda/IIIa" },

        {
            label: "Juru Penata Muda Tingkat I/IIIb",
            value: "Juru Penata Muda Tingkat I/IIIb",
        },

        { label: "Juru Penata/IIIc", value: "Juru Penata/IIIc" },

        {
            label: "Juru Penata Tingkat I/IIId",
            value: "Juru Penata Tingkat I/IIId",
        },

        { label: "Juru Pembina/IVa", value: "Juru Pembina/IVa" },

        {
            label: "Juru Pembina Tingkat I/IVb",
            value: "Juru Pembina Tingkat I/IVb",
        },

        {
            label: "Juru Pembina Tingkat Utama Muda/IVc",
            value: "Juru Pembina Tingkat Utama Muda/IVc",
        },

        {
            label: "Juru Pembina Tingkat Utama Madya/IVd",
            value: "Juru Pembina Tingkat Utama Madya/IVd",
        },

        {
            label: "Juru Pembina Tingkat Utama/IVe",
            value: "Juru Pembina Tingkat Utama/IVe",
        },
    ];

    return (
        <Modal
            title={`${title} ${form.getFieldValue(
                "nama"
            )}  (${form.getFieldValue("nip_bps")})`}
            open={visible}
            style={{ top: 20 }}
            onCancel={onCancel}
            onOk={() => form.submit()}
            width={600}
            okText="Simpan"
            cancelText="Batal"
        >
            <Form
                form={form}
                onFinish={onFinish}
                // onValuesChange={handleValuesChange}
                layout="vertical"
                wrapperCol={{ span: 24 }}
                autoComplete="off"
                size="large"
            >
                <Form.Item name="id" hidden>
                    <Input />
                </Form.Item>
                <Form.Item
                    name="nip_bps"
                    label="NIP BPS"
                    rules={[{ required: true, validator: validateNipBps }]}
                    hidden={type === "edit"}
                >
                    <Input
                        placeholder="Masukkan NIP lama contoh : 32002098"
                        className="border border-slate-400 rounded-md"
                        // {...(type === "edit" ? {} : { disabled: true })}
                    />
                </Form.Item>
                <Form.Item
                    name="nip"
                    label="NIP"
                    rules={[{ required: true, validator: validateNip }]}
                    hidden={type === "edit"}
                >
                    <Input
                        placeholder="Masukkan NIP baru contoh : 198810232001041002"
                        // {...(role === "admin" ? {} : { disabled: true })}
                        className="border border-slate-400 rounded-md"
                    />
                </Form.Item>
                <Form.Item
                    name="nama"
                    label="Nama Pegawai"
                    rules={[{ required: true }]}
                    hidden={type === "edit"}
                >
                    <Input
                        placeholder="Nama Lengkap Tanpa Singkatan"
                        // {...(role === "admin" ? {} : { disabled: true })}
                        className="border border-slate-400 rounded-md"
                    />
                </Form.Item>
                <Form.Item
                    name="unit_kerja"
                    label="Satuan Kerja"
                    rules={[{ required: true }]}
                    className="focus:border-none"
                >
                    <Select
                        placeholder="Pilih Satuan Kerja"
                        allowClear
                        showSearch
                        optionFilterProp="label"
                        options={unitKerja.map((unit) => ({
                            label: unit.nama,
                            value: unit.nama,
                        }))}
                    />
                </Form.Item>
                <Form.Item
                    name="jabatan_id"
                    label="Jabatan"
                    rules={[{ required: true }]}
                    className="focus:border-none"
                >
                    <Select
                        allowClear
                        showSearch
                        placeholder="Pilih Jabatan Pegawai"
                        optionFilterProp="label"
                        onChange={() => {
                            Modal.confirm({
                                title: "Konfirmasi",
                                content:
                                    "Mengubah jabatan pegawai akan mereset akumulasi angka kredit menjadi 0 (nol) !",
                                footer: (_, { OkBtn, CancelBtn }) => (
                                    <>
                                        <OkBtn />
                                    </>
                                ),
                            });
                            calculateAkumulasi();
                        }}
                        // {...(role === "admin" ? {} : { disabled: true })}
                        options={jabatan.map((item) => ({
                            label: item.nama,
                            value: String(item.id),
                        }))}
                    />
                </Form.Item>
                <Form.Item
                    name="pangkat_golongan_ruang"
                    label="Pangkat / Golongan Ruang"
                    rules={[{ required: true }]}
                >
                    <Select
                        allowClear
                        showSearch
                        placeholder="Penata Muda/IIIa"
                        className="border border-slate-400 rounded-md"
                        options={daftarPangkat}
                    />
                </Form.Item>
                <Form.Item
                    name="angka_kredit_konvensional"
                    label="Angka Kredit Konvensional"
                    hidden={type === "edit"}
                >
                    <InputNumber className="border w-[30%] border-slate-400 rounded-md" />
                </Form.Item>
                <Form.Item
                    name="angka_kredit_integrasi"
                    label="Angka Kredit Integrasi"
                    hidden={type === "edit"}
                >
                    <InputNumber className="border w-[30%] border-slate-400 rounded-md" />
                </Form.Item>

                <Form.Item
                    name="angka_kredit_dasar"
                    label="Angka Kredit Dasar / Terakhir"
                    className="focus:border-none"
                    onChange={() => calculateAkumulasi()}
                >
                    <InputNumber className="border border-slate-400 rounded-md" />
                </Form.Item>

                <Form.Item name="ijazah_terakhir" label="Ijazah Terakhir">
                    <Select
                        onSelect={() => calculateAkumulasi()}
                        placeholder="Pilih Ijazah yang Ditamatkan"
                        options={[
                            { label: "SD/sederajat", value: "SD/sederajat" },
                            {
                                label: "SLTP/sederajat",
                                value: "SLTP/sederajat",
                            },
                            {
                                label: "SLTA/sederajat",
                                value: "SLTA/sederajat",
                            },
                            { label: "DI", value: "DI" },
                            { label: "DII", value: "DII" },
                            { label: "DIII", value: "DIII" },
                            { label: "S1/DIV", value: "S1/DIV/sederajat" },
                            { label: "S2", value: "S2" },
                            { label: "S3", value: "S3" },
                        ]}
                    />
                </Form.Item>
                <Form.Item
                    name={"bulan"}
                    label="Bulan Penilaian"
                    rules={[{ required: true }]}
                >
                    <RangePicker
                        picker="month"
                        minDate={dayjs("2019-01-01")}
                        format={"MMMM YYYY"}
                        maxDate={dayjs()}
                        onChange={calculateAkumulasi}
                    />
                </Form.Item>
                <Form.Item
                    name="predikat_id"
                    label="Predikat Kinerja"
                    className="focus:border-none"
                >
                    <Select
                        placeholder="Pilih Predikat"
                        allowClear
                        showSearch
                        optionFilterProp="label"
                        onChange={calculateAkumulasi}
                        options={predikats.map((predikat) => ({
                            label: predikat.nama,
                            value: String(predikat.id),
                        }))}
                    />
                </Form.Item>
                <Form.Item name="akumulasi_ak" label="Akumulasi Angka Kredit">
                    <InputNumber
                        readOnly
                        className="border border-slate-400 rounded-md"
                    />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default PegawaiForm;
