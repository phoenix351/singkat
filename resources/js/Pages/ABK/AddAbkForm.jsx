import React, { useState } from "react";
import { Modal, Form, Input, Button, Select, InputNumber, Alert } from "antd";
import { router, usePage } from "@inertiajs/react";

const AddAbkForm = ({ visible, onCancel, jabatan, unitKerja }) => {
    const [form] = Form.useForm();

    const [jabatanOptions, setJabatanOptions] = useState([]);
    const [selectedUnitKerja, setSelectedUnitKerja] = useState(null);

    const handleUnitKerjaChange = async (value) => {
        setSelectedUnitKerja(value);
        try {
            const response = await axios.get("/api/get-available-jabatan", {
                params: { unit_kerja_id: value },
            });
            setJabatanOptions(
                response.data.map((jabatan) => ({
                    value: jabatan.id,
                    label: jabatan.nama,
                }))
            );
        } catch (error) {
            console.error("Failed to fetch jabatan:", error);
        }
    };

    // const checkPrice = (_, value) => {
    //     if (value.number > 0) {
    //         return Promise.resolve();
    //     }
    //     return Promise.reject(new Error("Price must be greater than zero!"));
    // };

    const checkEksisting = (_, value) => {
        const abk = form.getFieldValue("abk");

        if (value >= 0 && value <= abk) {
            return Promise.resolve();
        }
        return Promise.reject(
            new Error("Nilai Eksisting tidak boleh lebih dari nilai ABK!")
        );
    };

    const handleSubmit = (values) => {
        router.post("/kelola-abk", values);
        form.resetFields();
        onCancel();
    };

    return (
        <Modal
            title="Tambah ABK"
            open={visible}
            onCancel={onCancel}
            onOk={() => form.submit()}
            width={600}
            okText="Tambah"
            cancelText="Batal"
        >
            <Form
                form={form}
                name="control-hooks"
                onFinish={handleSubmit}
                style={{ maxWidth: 600 }}
                layout="vertical"
                wrapperCol={{ span: 24 }}
                autoComplete="off"
                size="large"
            >
                <Form.Item
                    name="unit_kerja_id"
                    label="Satuan Kerja"
                    rules={[{ required: true }]}
                >
                    <Select
                        placeholder="Pilih Satuan Kerja"
                        allowClear
                        showSearch
                        optionFilterProp="label"
                        options={unitKerja.map((item) => ({
                            value: item.id,
                            label: item.nama,
                        }))}
                        onChange={handleUnitKerjaChange}
                    />
                </Form.Item>

                <Form.Item
                    name="jabatan_id"
                    label="Jabatan"
                    rules={[{ required: true }]}
                    initialValue={null}
                >
                    <Select
                        placeholder="Pilih Jabatan"
                        showSearch
                        optionFilterProp="label"
                        disabled={!selectedUnitKerja}
                        options={jabatanOptions}
                    />
                </Form.Item>

                <Form.Item
                    name="abk"
                    label="ABK"
                    rules={[
                        {
                            required: true,
                            message: "ABK tidak boleh kosong.",
                        },
                    ]}
                >
                    <InputNumber className="border w-full border-slate-400 rounded-md" />
                </Form.Item>
                <Form.Item
                    name="eksisting"
                    label="Eksisting"
                    rules={[
                        {
                            validator: checkEksisting,
                        },
                    ]}
                >
                    <InputNumber className="border w-full border-slate-400 rounded-md" />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default AddAbkForm;
