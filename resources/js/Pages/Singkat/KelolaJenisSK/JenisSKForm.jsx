import React, { useEffect } from "react";
import { Modal, Form, Input, InputNumber } from "antd";

const JenisSKForm = ({ visible, onFinish, onCancel, title, form,okText,cancelText }) => {
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
            >
                <Form.Item
                    name="id"
                    label="ID"
                    hidden
                >
                    <Input className="border border-slate-400 rounded-md" />
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
