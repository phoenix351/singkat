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

const PegawaiForm = ({
    visible,
    onCancel,
    pegawai,
    jabatan,
    unitKerja,
    role,
    onFinish,
    type,
    form
}) => {


    useEffect(() => {
        if (pegawai) {

            form.setFieldsValue(pegawai);
        }
    }, [pegawai]);



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
                        {...(role === "admin" ? {} : { disabled: true })}
                    />
                </Form.Item>
                <Form.Item
                    name="nip"
                    label="NIP"
                    rules={[{ required: true, validator: validateNip }]}
                >
                    <Input
                        placeholder="Masukkan NIP baru contoh : 198810232001041002"
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
                        placeholder="Nama Lengkap Tanpa Singkatan"
                        {...(role === "admin" ? {} : { disabled: true })}
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
                        {...(role === "admin" ? {} : { disabled: true })}
                        
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
                    <Input placeholder="Penata Muda/IIIa" className="border border-slate-400 rounded-md" />
                
                </Form.Item>
                <Form.Item
                    name="angka_kredit_konvensional"
                    label="Angka Kredit Konvensional"
                >
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
                        {label:'SD/sederajat',value:'SD/sederajat'},
                        {label:'SLTP/sederajat',value:'SLTP/sederajat'},
                        {label:'SLTA/sederajat',value:'SLTA/sederajat'},
                        {label:'DI',value:'DI'},
                        {label:'DII',value:'DII'},
                        {label:'DIII',value:'DIII'},
                        {label:'S1/DIV',value:'S1/DIV/sederajat'},
                        {label:'S2',value:'S2'},
                        {label:'S3',value:'S3'},
                    ]}/>
                </Form.Item>
                <Form.Item name="angka_kredit_akumulasi" label="Akumulasi Angka Kredit">
                    <Input
                        className="border border-slate-400 rounded-md"
                        disabled
                    />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default PegawaiForm;
