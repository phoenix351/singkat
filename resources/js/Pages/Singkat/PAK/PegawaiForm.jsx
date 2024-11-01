import React, { useEffect } from "react";
import { Modal, Form, Input, Select, InputNumber, Button } from "antd";
import { router } from "@inertiajs/react";

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
    useEffect(() => {
        if (pegawai) {
            form.setFieldsValue(pegawai);
            console.log({ pegawai });
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
            title={title}
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
                >
                    <Input
                        placeholder="Nama Lengkap Tanpa Singkatan"
                        // {...(role === "admin" ? {} : { disabled: true })}
                        className="border border-slate-400 rounded-md"
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
                              title: 'Konfirmasi',
                              content: 'Mengubah jabatan pegawai akan mereset akumulasi angka kredit menjadi 0 (nol) !',
                              footer: (_, { OkBtn, CancelBtn }) => (
                                <>
                                  <OkBtn />
                                </>
                              ),
                            });
                          }}
                        // {...(role === "admin" ? {} : { disabled: true })}
                        options={jabatan.map((item) => ({
                            label: item.nama,
                            value: String(item.id),
                        }))}
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
                >
                    <InputNumber className="border w-[30%] border-slate-400 rounded-md" />
                </Form.Item>
                <Form.Item
                    name="angka_kredit_integrasi"
                    label="Angka Kredit Integrasi"
                >
                    <InputNumber className="border w-[30%] border-slate-400 rounded-md" />
                </Form.Item>

                <Form.Item
                    name="tambahan_ijazah"
                    label="25% Tambahan Ijazah"
                    className="focus:border-none"
                >
                    <Select
                        placeholder="Pilih Predikat Ijazah"
                        allowClear
                        optionFilterProp="children"
                        options={[
                            { value: "Sangat Baik", label: "Sangat Baik" },
                            { value: "Baik", label: "Baik" },
                            {
                                value: "Butuh Perbaikan",
                                label: "Butuh Perbaikan",
                            },
                            { value: "Kurang", label: "Kurang" },
                            { value: "Sangat Kurang", label: "Sangat Kurang" },
                        ]}
                    />
                </Form.Item>

                <Form.Item name="ijazah_terakhir" label="Ijazah Terakhir">
                    <Select
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
                <Form.Item name="akumulasi_ak" label="Akumulasi Angka Kredit">
                    <Input className="border border-slate-400 rounded-md" />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default PegawaiForm;
