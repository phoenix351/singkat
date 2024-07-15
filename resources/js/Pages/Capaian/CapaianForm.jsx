import React, { useEffect, useState } from "react";
import { Modal, Form, Input, Select, InputNumber, DatePicker, message } from "antd";
import { router } from "@inertiajs/react";

import axios from "axios";

const disabledStyle = {
    color: "#000",
};


const CapaianForm = ({ visible, onCancel, onFinish,form,title,okText,type }) => {
    
    const [predikats, setPredikats] = useState([]);
    const [pegawais, setPegawais] = useState([]);

    // define message
    const [messageApi, contextHolder] = message.useMessage();

   

    const fetchPredikats = async () => {
        try {
            const { data } = await axios.get('/api/predikats');
            console.log({ data });
            setPredikats(data);
        } catch (error) {
            console.error('Error when get predikat data');
        }

    }
    const fetchPegawais = async () => {
        try {
            const { data } = await axios.get('/api/pegawais');
            setPegawais(data);
        } catch (error) {
            console.error('Error when get predikat data');
        }

    }

    useEffect(() => {
        fetchPredikats();
        fetchPegawais();
        // form.setFieldsValue(capaian);
        console.log({type});
    }, [])


    return (
        <>
            {contextHolder}
            <Modal
                title={title}
                open={visible}
                onCancel={onCancel}
                style={{ top: 20 }}
                onOk={() => form.submit()}
                width={600}
                okText={okText}
                cancelText="Batal"
            >
                <Form
                    form={form}
                    // onValuesChange={handleValuesChange}
                    name="control-hooks"
                    onFinish={onFinish}
                    style={{ maxWidth: 600 }}
                    layout="vertical"
                    wrapperCol={{ span: 24 }}
                    autoComplete="off"
                    size="large"
                >
                    <Form.Item name="id" label="ID" className="focus:border-none" hidden>
                        <Input />
                    </Form.Item>

                    <Form.Item
                        name="pegawai_id"
                        label="Pegawai"
                        className="focus:border-none"
                        >
                        <Select
                            placeholder="Pilih Pegawai"
                            allowClear
                            showSearch
                            disabled={type==='edit'}
                            optionFilterProp="label"
                            options={pegawais.map(pegawai => ({ label: pegawai.nama, value: pegawai.id }))}
                        />
                    </Form.Item>
                    <Form.Item
                        name="predikat_id"
                        label="Predikat Kinerja"
                        className="focus:border-none"
                    >
                        <Select
                            placeholder="Pilih Predikat"
                            allowClear
                            showSearch
                            optionFilterProp="label"
                            options={predikats.map(predikat => ({ label: predikat.nama, value: predikat.id }))}
                        />
                    </Form.Item>
                    <Form.Item name={"tahun"} label="Tahun Penilaian">
                        <DatePicker picker="year" />
                    </Form.Item>
                    <Form.Item name={"periode"} label="Periode Penilaian">
                        <Select placeholder="Pilih Periode" allowClear showSearch options={[
                            { label: 'Tahunan', value: 'Tahunan' },
                            { label: 'Semester 1', value: 'Semester 1' },
                            { label: 'Semester 2', value: 'Semester 2' },
                        ]} />
                    </Form.Item>

                </Form>
            </Modal>
        </>
    );
};

export default CapaianForm;
