import React from "react";
import { Modal, Form, Input, Select, InputNumber } from "antd";
import { router } from "@inertiajs/react";

import calculateAkumulasiAk from "@/Utils/calculateAkumulasiAk";

const disabledStyle = {
    color: "#000",
};

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
    if (/^\d+$/.test(value)) {
        return Promise.resolve();
    }
    return Promise.reject(new Error("NIP BPS harus angka."));
};

const AddPegawaiForm = ({ visible, onCancel, jabatan, unitKerja, role }) => {
    const [form] = Form.useForm();

    const handleValuesChange = (changedValues, allValues) => {
        const akumulasi_ak = calculateAkumulasiAk(allValues);

        form.setFieldsValue({ akumulasi_ak });
    };

    const handleSubmit = (values) => {
        router.post("/kelola-pak", values);
        form.resetFields();
        onCancel();
    };

    return (
        <Modal
            title="Tambah Pegawai"
            open={visible}
            onCancel={onCancel}
            style={{ top: 20 }}
            onOk={() => form.submit()}
            width={600}
            okText="Tambah"
            cancelText="Batal"
        >
            <Form
                form={form}
                onValuesChange={handleValuesChange}
                name="control-hooks"
                onFinish={handleSubmit}
                style={{ maxWidth: 600 }}
                layout="vertical"
                wrapperCol={{ span: 24 }}
                autoComplete="off"
                size="large"
            >
                <Form.Item
                    name="nip_bps"
                    label="NIP BPS"
                    rules={[
                        {
                            required: true,
                            validator: validateNipBps,
                        },
                    ]}
                >
                    <Input
                        placeholder="Masukkan NIP BPS Pegawai"
                        className="border w-full border-slate-400 rounded-md"
                    />
                </Form.Item>
                <Form.Item
                    name="nip"
                    label="NIP"
                    rules={[
                        {
                            required: true,
                            validator: validateNip,
                        },
                    ]}
                >
                    <Input
                        placeholder="Masukkan NIP Pegawai"
                        className="border w-full border-slate-400 rounded-md"
                    />
                </Form.Item>
                <Form.Item
                    name="nama"
                    label="Nama Pegawai"
                    rules={[
                        { required: true, message: "Nama tidak boleh kosong." },
                    ]}
                >
                    <Input
                        placeholder="Kevin Pandoh"
                        className="border border-slate-400 rounded-md"
                    />
                </Form.Item>
                <Form.Item
                    name="jabatan"
                    label="Jabatan"
                    rules={[{ required: true }]}
                    className="focus:border-none"
                >
                    <Select
                        placeholder="Pilih Jabatan Pegawai"
                        showSearch
                        optionFilterProp="label"
                        options={jabatan.map((item) => ({
                            value: item.nama,
                            label: item.nama,
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
                        placeholder="Pilih Satuan Kerja Pegawai"
                        allowClear
                        showSearch
                        optionFilterProp="label"
                        options={unitKerja.map((item) => ({
                            value: item.nama,
                            label: item.nama,
                        }))}
                    />
                </Form.Item>
                <Form.Item
                    name="pangkat_golongan_ruang"
                    label="Pangkat / Golongan Ruang"
                    rules={[
                        {
                            required: true,
                            message: "Pangkat / Golongan tidak boleh kosong.",
                        },
                    ]}
                >
                    <Input
                        placeholder="Pembina Utama Muda / IVc"
                        className="border border-slate-400 rounded-md"
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
                    name="predikat_kinerja"
                    label="Predikat Kinerja"
                    className="focus:border-none"
                >
                    <Select
                        placeholder="Pilih Predikat"
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
                    <Input
                        placeholder=""
                        className="border border-slate-400 rounded-md"
                    />
                </Form.Item>

                <Form.Item name="akumulasi_ak" label="Akumulasi Angka Kredit">
                    <Input
                        style={disabledStyle}
                        placeholder=""
                        className="border border-slate-400 rounded-md"
                        disabled
                    />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default AddPegawaiForm;
