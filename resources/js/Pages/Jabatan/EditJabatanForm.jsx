import React, { useEffect } from "react";
import { Modal, Form, Input } from "antd";
import { router } from "@inertiajs/react";

const EditJabatanForm = ({ visible, onCancel, jabatan }) => {
    const [form] = Form.useForm();

    useEffect(() => {
        if (jabatan) {
            form.setFieldsValue(jabatan);
        }
    }, [jabatan, form]);

    const handleSubmit = (values) => {
        router.put(`/jabatan/${jabatan.id}`, values);
        onCancel();
        form.resetFields();
    };

    return (
        <Modal
            title="Edit Jabatan"
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
                    label="Nama Jabatan"
                    rules={[
                        {
                            required: true,
                            message: "Nama Jabatan tidak boleh kosong.",
                        },
                    ]}
                >
                    <Input className="border border-slate-400 rounded-md" />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default EditJabatanForm;
