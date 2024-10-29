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
} from "antd";
import dayjs from "dayjs";
import customParseFormat from "dayjs/plugin/customParseFormat";
import axios from "axios";
import UploadPAK from "@/Components/UploadPAK";
const disabledStyle = {
    color: "#000",
};

const dateFormat = "YYYY-MM-DD";
const {RangePicker} = DatePicker;

const CapaianForm = ({
    visible,
    onCancel,
    onFinish,
    form,
    title,
    okText,
    type,
    initPeriod,
}) => {
    const [predikats, setPredikats] = useState([]);
    const [periode, setPeriode] = useState("");
    const [pegawais, setPegawais] = useState([]);

    // define message
    const [messageApi, contextHolder] = message.useMessage();

    const fetchPredikats = async () => {
        try {
            const { data } = await axios.get("/api/predikats");
            // console.log({ data });
            setPredikats(data);
        } catch (error) {
            console.error("Error when get predikat data");
        }
    };
    const fetchPegawais = async () => {
        try {
            const { data } = await axios.get("/api/pegawais");
            setPegawais(data);
        } catch (error) {
            console.error("Error when get predikat data");
        }
    };

    useEffect(() => {
        fetchPredikats();
        fetchPegawais();
        // form.setFieldsValue(capaian);
    }, []);
    useEffect(() => {
        setPeriode(initPeriod);
    }, [initPeriod]);

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
                            disabled={type === "edit"}
                            optionFilterProp="label"
                            options={pegawais.map((pegawai) => ({
                                label: pegawai.nama,
                                value: pegawai.id,
                            }))}
                        />
                    </Form.Item>

                    <Form.Item name={"periode"} label="Periode Penilaian">
                        <Select
                            placeholder="Pilih Periode"
                            allowClear
                            showSearch
                            onChange={setPeriode}
                            disabled={type === "edit"}
                            options={[
                                { label: "Tahunan", value: "Tahunan" },
                                { label: "Bulanan", value: "Bulanan" },
                                { label: "Semester 1", value: "Semester 1" },
                                { label: "Semester 2", value: "Semester 2" },
                            ]}
                        />
                    </Form.Item>
                    {periode === "Bulanan" ? (
                        <Form.Item name={"bulan"} label="Bulan Penilaian">
                            {/* <DatePicker
                                picker="month"
                                disabled={type === "edit"}
                                /> */}
                            <RangePicker
                                picker="month"
                                minDate={dayjs("2019-01-01", dateFormat)}
                                format={"MMMM YYYY"}
                                maxDate={dayjs()}
                            />
                        </Form.Item>
                    ) : (
                        <Form.Item name={"tahun"} label="Tahun Penilaian">
                            <DatePicker
                                picker="year"
                                minDate={dayjs("2019-01-01", dateFormat)}
                                disabled={type === "edit"}
                                maxDate={dayjs()}
                            />
                        </Form.Item>
                    )}
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
                    {type==="edit"&&
                    <Form.Item name={"file"} label="Dokumen PAK">
                        <UploadPAK/>
                    </Form.Item>
                    }
                </Form>
            </Modal>
        </>
    );
};

export default CapaianForm;
