import React, { useEffect } from "react";
import { Modal, Form, Input, InputNumber } from "antd";
import axios from "axios";

const JenisSKForm = ({
    visible,
    onFinish,
    onCancel,
    title,
    form,
    okText,
    cancelText,
}) => {
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
            title={title}
            open={visible}
            onCancel={onCancel}
            onOk={() => form.submit()}
            width={600}
            okText={okText}
            cancelText={cancelText}
        >
            <Form
                form={form}
                onFinish={onFinish}
                layout="vertical"
                wrapperCol={{ span: 24 }}
                autoComplete="off"
                size="large"
                onKeyDown={(event) => {
                    if (event.code === "Enter") form.submit();
                }}
            >
                <Form.Item name="id" label="ID" hidden>
                    <Input className="border border-slate-400 rounded-md" />
                </Form.Item>
                <Form.Item name="_token" hidden>
                    <Input />
                </Form.Item>
                <Form.Item name="new_id" label="ID Baru">
                    <Input
                        className="border border-slate-400 rounded-md"
                        placeholder="Isikan ID hanya jika ingin diubah"
                        onChange={(event) => {
                            const replaced = event.target.value.replace(
                                /[^0-9]/g,
                                ""
                            );
                            form.setFieldValue("new_id", replaced);
                        }}
                    />
                </Form.Item>
                <Form.Item
                    name="nama"
                    label="Deskripsi"
                    rules={[
                        {
                            required: true,
                            message: "Deskripsi tidak boleh kosong.",
                        },
                    ]}
                >
                    <Input className="border border-slate-400 rounded-md" />
                </Form.Item>
            </Form>
        </Modal>
    );
};

export default JenisSKForm;
