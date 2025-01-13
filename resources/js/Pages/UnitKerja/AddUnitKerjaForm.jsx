import React, { useEffect } from "react";
import { Modal, Form, Input } from "antd";
import { router } from "@inertiajs/react";
import axios from "axios";

const AddUnitKerjaForm = ({ visible, onCancel }) => {
    const [form] = Form.useForm();

    const handleSubmit = (values) => {
        router.post(route("singkat.admin.unit-kerja.store"), values);
        form.resetFields();
        onCancel();
    };
    useEffect(() => {
        async function getToken() {
            try {
                const { data } = await axios.get(route("api.token.csrf"));
                form.setFieldValue("_token", data);
            } catch (error) {
                console.log("error get token");
            }
        }
        getToken();
    }, []);

    return (
        <Modal
            title="Tambah Satuan Kerja"
            open={visible}
            onCancel={onCancel}
            onOk={() => form.submit()}
            width={600}
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
                <Form.Item name="_token" hidden>
                    <Input />
                </Form.Item>
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

export default AddUnitKerjaForm;
