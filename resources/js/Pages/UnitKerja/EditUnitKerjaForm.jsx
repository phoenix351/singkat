import React, { useEffect } from "react";
import { Modal, Form, Input } from "antd";
import { router } from "@inertiajs/react";

const EditUnitKerjaForm = ({ visible, onCancel, unitKerja }) => {
    const [form] = Form.useForm();

    useEffect(() => {
        if (unitKerja) {
            form.setFieldsValue(unitKerja);
        }
    }, [unitKerja, form]);

    const handleSubmit = (values) => {
        router.put(`/unit-kerja/${unitKerja.id}`, values);
        onCancel();
        form.resetFields();
    };

    return (
        <Modal
            title="Edit Satuan Kerja"
            open={visible}
            onCancel={onCancel}
            onOk={() => form.submit()}
            width={600}
        >
            <Form
                form={form}
                onFinish={handleSubmit}
                layout="vertical"
                wrapperCol={{ span: 24 }}
                autoComplete="off"
                size="large"
            >
                <Form.Item
                    name="nama"
                    label="Nama Satuan Kerja"
                    rules={[
                        {
                            required: true,
                            message: "Nama Satuan Kerja tidak boleh kosong.",
                        },
                    ]}
                >
                    <Input className="border border-slate-400 rounded-md" />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default EditUnitKerjaForm;
