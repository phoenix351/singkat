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

const EditCapaianForm = ({
    visible,
    onCancel,
    capaian,
    jabatan,
    unitKerja,
    role,
}) => {
    const [form] = Form.useForm();

    useEffect(() => {
        if (capaian) {
            form.setFieldsValue(capaian);
        }
    }, [capaian, form]);

    const handleValuesChange = (changedValues, allValues) => {
        const akumulasi_ak = calculateAkumulasiAk(allValues);
        form.setFieldsValue({ akumulasi_ak });
    };

    const handleSubmit = (values) => {
        router.put(`/kelola-pak/${capaian.id}`, values);
        onCancel();
        form.resetFields();
    };

    return (
        <Modal
            title="Edit capaian"
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
                
            </Form>
        </Modal>
    );
};

export default EditCapaianForm;
