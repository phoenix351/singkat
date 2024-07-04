import React, { useEffect } from "react";
import { Modal, Form, Input, Select, InputNumber } from "antd";
import { router } from "@inertiajs/react";
import calculateAkumulasiAk from "../../Utils/calculateAkumulasiAk";

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

const EditPegawaiForm = ({
    visible,
    onCancel,
    pegawai,
    jabatan,
    unitKerja,
    role,
}) => {
    const [form] = Form.useForm();

    useEffect(() => {
        if (pegawai) {
            form.setFieldsValue(pegawai);
        }
    }, [pegawai, form]);

    const handleValuesChange = (changedValues, allValues) => {
        const akumulasi_ak = calculateAkumulasiAk(allValues);
        form.setFieldsValue({ akumulasi_ak });
    };

    const handleSubmit = (values) => {
        router.put(`/kelola-pak/${pegawai.id}`, values);
        onCancel();
        form.resetFields();
    };

    return (
        <Modal
            title="Edit Pegawai"
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
                onFinish={handleSubmit}
                onValuesChange={handleValuesChange}
                layout="vertical"
                wrapperCol={{ span: 24 }}
                autoComplete="off"
                size="large"
            >
                <Form.Item
                    name="nip_bps"
                    label="NIP BPS"
                    rules={[{ required: true, validator: validateNipBps }]}
                >
                    <Input
                        className="border border-slate-400 rounded-md"
                        {...(role === "admin" ? {} : { disabled: true })}
                    />
                </Form.Item>
                <Form.Item
                    name="nip"
                    label="NIP"
                    rules={[{ required: true, validator: validateNip }]}
                >
                    <Input
                        {...(role === "admin" ? {} : { disabled: true })}
                        className="border border-slate-400 rounded-md"
                    />
                </Form.Item>
                <Form.Item
                    name="nama"
                    label="Nama Pegawai"
                    rules={[{ required: true }]}
                >
                    <Input
                        {...(role === "admin" ? {} : { disabled: true })}
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
                        allowClear
                        showSearch
                        {...(role === "admin" ? {} : { disabled: true })}
                        optionFilterProp=""
                        options={jabatan.map((jabatan) => ({
                            label: jabatan.nama,
                            value: jabatan.nama,
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
                    <Input className="border border-slate-400 rounded-md" />
                </Form.Item>
                <Form.Item
                    name="angka_kredit_konvensional"
                    label="Angka Kredit Konvensional"
                >
                    {" "}
                    <InputNumber
                        {...(role === "admin" ? {} : { disabled: true })}
                        className="border w-[30%] border-slate-400 rounded-md"
                    />
                </Form.Item>
                <Form.Item
                    name="angka_kredit_integrasi"
                    label="Angka Kredit Integrasi"
                >
                    <InputNumber
                        {...(role === "admin" ? {} : { disabled: true })}
                        className="border w-[30%] border-slate-400 rounded-md"
                    />
                </Form.Item>
                <Form.Item
                    name="predikat_kinerja"
                    label="Predikat Kinerja"
                    className="focus:border-none"
                >
                    <Select
                        allowClear
                        optionFilterProp="children"
                        options={[
                            { label: "Sangat Baik", value: "Sangat Baik" },
                            { label: "Baik", value: "Baik" },
                            { label: "Cukup", value: "Cukup" },
                            { label: "Kurang", value: "Kurang" },
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
                    <Input className="border border-slate-400 rounded-md" />
                </Form.Item>
                <Form.Item name="akumulasi_ak" label="Akumulasi Angka Kredit">
                    <Input
                        className="border border-slate-400 rounded-md"
                        disabled
                    />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default EditPegawaiForm;
