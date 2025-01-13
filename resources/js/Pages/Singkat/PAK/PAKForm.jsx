import React, { useEffect, useState } from "react";
import {
    Modal,
    Form,
    Input,
    Select,
    InputNumber,
    DatePicker,
    message,
    Upload,
    Radio,
} from "antd";
import dayjs from "dayjs";
import customParseFormat from "dayjs/plugin/customParseFormat";
import axios from "axios";
import UploadPAK from "@/Components/UploadPAK";
const disabledStyle = {
    color: "#000",
};

const dateFormat = "YYYY-MM-DD";
const { RangePicker } = DatePicker;

const PAKForm = ({
    visible,
    onCancel,
    onFinish,
    form,
    title,
    okText,
    type,
}) => {
    const [predikats, setPredikats] = useState([]);
    const [pegawais, setPegawais] = useState([]);
    const [file, setFile] = useState(null);
    const [daftarJenisSk, setDaftarJenisSK] = useState([]);

    // define message
    const [messageApi, contextHolder] = message.useMessage();

    const fetchPredikats = async () => {
        try {
            const { data } = await axios.get(route("index") + "/api/predikats");
            setPredikats(data);
        } catch (error) {
            console.error("Error when get predikat data");
        }
    };
    const fetchPegawais = async () => {
        try {
            const { data } = await axios.get(route("index") + "/api/pegawais");
            setPegawais(data);
        } catch (error) {
            console.error("Error when get pegawai data");
        }
    };
    const fetchDaftarJenisSK = async () => {
        try {
            const { data } = await axios.get(route("index") + "/api/jenis-sk");
            setDaftarJenisSK(data);
        } catch (error) {
            console.error("Error when get jenis-sk data");
        }
    };

    useEffect(() => {
        fetchPredikats();
        fetchPegawais();
        fetchDaftarJenisSK();
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

    useEffect(() => {
        form.setFieldValue("file", file);
    }, [file]);

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
                    <Form.Item name="_token" hidden>
                        <Input />
                    </Form.Item>
                    <Form.Item
                        name="id"
                        label="ID"
                        className="focus:border-none"
                        hidden
                    >
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
                            // disabled={type === "edit"}
                            optionFilterProp="label"
                            options={pegawais.map((pegawai) => ({
                                label: pegawai.nama,
                                value: pegawai.id,
                            }))}
                        />
                    </Form.Item>
                    <Form.Item
                        name="nomor_sk"
                        label="Nomor SK"
                        className="focus:border-none"
                    >
                        <Input className="border border-slate-400 rounded-md" />
                    </Form.Item>
                    <Form.Item
                        name="jenis_sk"
                        label="Jenis SK"
                        className="focus:border-none"
                    >
                        <Select
                            options={daftarJenisSk.map(({ nama, id }) => ({
                                label: nama,
                                value: id,
                            }))}
                        />
                    </Form.Item>

                    <Form.Item name={"tmt_sk"} label="TMT SK">
                        <DatePicker
                            // picker="month"
                            // minDate={dayjs("2019-01-01", dateFormat)}
                            format={"DD MMMM YYYY"}
                            maxDate={dayjs()}
                            // onChange={() => setPeriode("Bulanan")}
                            // disabled={type === "edit"}
                        />
                    </Form.Item>
                    <Form.Item name={"bulan"} label="Bulan Penilaian">
                        <RangePicker
                            picker="month"
                            minDate={dayjs("2019-01-01", dateFormat)}
                            format={"MMMM YYYY"}
                            maxDate={dayjs()}
                            // disabled={type === "edit"}
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
                            options={predikats.map((predikat) => ({
                                label: predikat.nama,
                                value: predikat.id,
                            }))}
                        />
                    </Form.Item>
                    <Form.Item
                        name="angka_kredit"
                        label="Angka Kredit Perolehan"
                        className="focus:border-none"
                        tooltip="Desimal pakai titik"
                    >
                        <InputNumber />
                    </Form.Item>
                    <Form.Item
                        name="angka_kredit_akumulasi"
                        label="Angka Kredit Akumulasi"
                        className="focus:border-none"
                        tooltip="Desimal pakai titik"
                    >
                        <InputNumber />
                    </Form.Item>

                    <Form.Item name={"file"} label="Dokumen PAK" required>
                        <UploadPAK setFile={setFile} />
                    </Form.Item>
                </Form>
            </Modal>
        </>
    );
};

export default PAKForm;
