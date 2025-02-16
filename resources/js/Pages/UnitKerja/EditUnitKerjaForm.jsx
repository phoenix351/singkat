import React, { useEffect } from "react";
import { Modal, Form, Input } from "antd";
import { router } from "@inertiajs/react";
import axios from "axios";

const EditUnitKerjaForm = ({ visible, onCancel, unitKerja }) => {
    const [form] = Form.useForm();

    useEffect(() => {
        if (unitKerja) {
            form.setFieldsValue(unitKerja);
        }
    }, [unitKerja, form]);
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

    const handleSubmit = (values) => {
        router.put(
            route("singkat.admin.unit-kerja.update", {
                unitKerja: unitKerja.id,
            }),
            values
        );
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
                onKeyDown={(event) => {
                    if (event.code === "Enter") {
                        form.submit();
                    }
                }}
            >
                <Form.Item name="_token" hidden>
                    <Input />
                </Form.Item>
                <Form.Item
                    name="kode"
                    label="Kode Satuan Kerja"
                    rules={[
                        {
                            required: true,
                            message: "Nama Satuan Kerja tidak boleh kosong.",
                        },
                    ]}
                >
                    <Input className="border border-slate-400 rounded-md" />
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

export default EditUnitKerjaForm;
