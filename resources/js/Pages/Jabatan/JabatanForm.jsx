import React, { useEffect } from "react";
import { Modal, Form, Input, InputNumber } from "antd";
import { router } from "@inertiajs/react";

const EditJabatanForm = ({ visible, onFinish, onCancel, jabatan }) => {
    const [form] = Form.useForm();

    useEffect(() => {
        if (jabatan) {
            form.setFieldsValue(jabatan);
        }
    }, [jabatan, form]);

    return (
        <Modal
            title="Ubah Jabatan"
            open={visible}
            onCancel={onCancel}
            onOk={() => form.submit()}
            width={600}
        >
            <Form
                form={form}
                onFinish={onFinish}
                layout="vertical"
                wrapperCol={{ span: 24 }}
                autoComplete="off"
                size="large"
            >
                <Form.Item
                    name="nomor_urut_kepka"
                    label="Nomor Urut Berdasarkan Kepka"
                    
                >
                    <InputNumber className="border border-slate-400 rounded-md" />
                </Form.Item>
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
                <Form.Item name="id" label="Id Jabatan" hidden>
                    <Input className="border border-slate-400 rounded-md" />
                </Form.Item>
                <Form.Item
                    name="angka_kredit"
                    label="Angka Kredit Tahunan (100%)"
                    rules={[
                        {
                            required: true,
                            message: "Angka Kredit tidak boleh kosong.",
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
