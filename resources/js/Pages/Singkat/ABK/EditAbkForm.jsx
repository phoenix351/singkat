import React, { useEffect, useState } from "react";
import { Modal, Form, Select, InputNumber } from "antd";
import { router } from "@inertiajs/react";
import axios from "axios"; // Ensure axios is imported

const EditAbkForm = ({ visible, onCancel, abk, jabatan, unitKerja, role }) => {
    const [form] = Form.useForm();

    const [jabatanOptions, setJabatanOptions] = useState([]);
    const [selectedUnitKerja, setSelectedUnitKerja] = useState(null);

    useEffect(() => {
        if (abk) {
            form.setFieldsValue(abk);
            if (abk.unit_kerja_id) {
                handleUnitKerjaChange(abk.unit_kerja_id, abk.jabatan_id);
            }
        }
    }, [abk, form]);

    const checkEksisting = (_, value) => {
        const abk = form.getFieldValue("abk");

        if (value >= 0 && value <= abk) {
            return Promise.resolve();
        }
        return Promise.reject(
            new Error("Nilai Eksisting tidak boleh lebih dari nilai ABK!")
        );
    };

    const handleUnitKerjaChange = async (value, currentJabatanId = null) => {
        setSelectedUnitKerja(value);
        try {
            const response = await axios.get(route("index")+"/api/get-available-jabatan", {
                params: { unit_kerja_id: value },
            });

            let options = response.data.map((jabatan) => ({
                value: jabatan.id,
                label: jabatan.nama,
            }));

            // Include the current jabatan if it's not in the list
            if (currentJabatanId) {
                const currentJabatan = jabatan.find(
                    (j) => j.id === currentJabatanId
                );
                if (
                    currentJabatan &&
                    !options.some((j) => j.value === currentJabatanId)
                ) {
                    options.push({
                        value: currentJabatan.id,
                        label: currentJabatan.nama,
                    });
                }
            }

            setJabatanOptions(options);
        } catch (error) {
            console.error("Failed to fetch jabatan:", error);
        }
    };

    const handleSubmit = (values) => {
        router.put(route("singkat.admin.abk.update",{abk:abk.id}), values);
        onCancel();
        form.resetFields();
    };

    return (
        <Modal
            title="Edit ABK"
            open={visible}
            onCancel={onCancel}
            onOk={() => form.submit()}
            width={600}
            okText="Simpan"
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
                    label="Unit Kerja"
                    rules={[
                        {
                            required: true,
                            message: "Unit Kerja tidak boleh kosong.",
                        },
                    ]}
                >
                    <Select
                        {...(role === "admin" ? {} : { disabled: true })}
                        placeholder="Pilih Unit Kerja"
                        showSearch
                        optionFilterProp="label"
                        onChange={(value) => handleUnitKerjaChange(value)}
                        options={unitKerja.map((item) => ({
                            value: item.id,
                            label: item.nama,
                        }))}
                    />
                </Form.Item>
                <Form.Item
                    name="jabatan_id"
                    label="Nama Jabatan"
                    rules={[
                        {
                            required: true,
                            message: "Jabatan tidak boleh kosong.",
                        },
                    ]}
                >
                    <Select
                        {...(role === "admin" ? {} : { disabled: true })}
                        placeholder="Pilih Jabatan"
                        showSearch
                        optionFilterProp="label"
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
                    <InputNumber
                        {...(role === "admin" ? {} : { disabled: true })}
                        className="border w-full border-slate-400 rounded-md"
                    />
                </Form.Item>
                <Form.Item
                    rules={[
                        {
                            validator: checkEksisting,
                        },
                    ]}
                    name="eksisting"
                    label="Eksisting"
                >
                    <InputNumber className="border w-full border-slate-400 rounded-md" />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default EditAbkForm;
