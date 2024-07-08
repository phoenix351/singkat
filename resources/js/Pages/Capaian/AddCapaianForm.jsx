import React, { useEffect, useState } from "react";
import { Modal, Form, Input, Select, InputNumber, DatePicker, message } from "antd";
import { router } from "@inertiajs/react";

import calculateAkumulasiAk from "@/Utils/calculateAkumulasiAk";
import axios from "axios";

const disabledStyle = {
    color: "#000",
};

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

const AddCapaianForm = ({ visible, onCancel }) => {
    const [form] = Form.useForm();
    const [predikats, setPredikats] = useState([]);
    const [pegawais, setPegawais] = useState([]);

    // define message
    const [messageApi, contextHolder] = message.useMessage();

    const handleValuesChange = (changedValues, allValues) => {
        const akumulasi_ak = calculateAkumulasiAk(allValues);

        form.setFieldsValue({ akumulasi_ak });
    };

    const handleSubmit = async (values) => {
        try {
            messageApi.open({
                key: 'submit-form',
                type: 'loading',
                message: 'menambahkan capaian pegawai'
            })
            let tahun_bulan = new Date(values.tahun_bulan);
            let preparedTahunBulan = `${tahun_bulan.getFullYear()}${(tahun_bulan.getMonth() + 1).toString().padStart(2, '0')}`;
            const data = { ...values };
            data.tahun_bulan = preparedTahunBulan;
            const response = await axios.post('/capaian', data, { headers: { "Content-Type": "application/json" } })
            messageApi.open({
                key: 'submit-form',
                type: 'success',
                message: 'telah ditambahkan 1 capaian pegawai'
            })

        } catch (error) {
            messageApi.open(
                {
                    key: 'submit-form',
                    type: 'error',
                    message: 'Terdapat error'
                }
            )
        } finally {
            router.get('/kelola-ckp', {}, { preserveState: true })
        }


    };

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
            console.log({ data });
            setPegawais(data);
        } catch (error) {
            console.error('Error when get predikat data');
        }

    }

    useEffect(() => {
        fetchPredikats();
        fetchPegawais()
    }, [])


    return (
        <>
            {contextHolder}
            <Modal
                title="Tambah CKP"
                open={visible}
                onCancel={onCancel}
                style={{ top: 20 }}
                onOk={() => form.submit()}
                width={600}
                okText="Tambah"
                cancelText="Batal"
            >
                <Form
                    form={form}
                    onValuesChange={handleValuesChange}
                    name="control-hooks"
                    onFinish={handleSubmit}
                    style={{ maxWidth: 600 }}
                    layout="vertical"
                    wrapperCol={{ span: 24 }}
                    autoComplete="off"
                    size="large"
                >

                    <Form.Item
                        name="pegawai_id"
                        label="Pegawai"
                        className="focus:border-none"
                    >
                        <Select
                            placeholder="Pilih Pegawai"
                            allowClear
                            showSearch
                            optionFilterProp="label"
                            options={pegawais.map(pegawai => ({ label: pegawai.nama, value: pegawai.id }))}
                        />
                    </Form.Item>
                    <Form.Item
                        name="predikat"
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
                    <Form.Item name={"tahun_bulan"} label="Periode Penilaian">
                        <DatePicker picker="month" />
                    </Form.Item>

                </Form>
            </Modal>
        </>
    );
};

export default AddCapaianForm;
