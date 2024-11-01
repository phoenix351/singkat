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
    const [file, setFile] = useState(null);
    const [tahun, setTahun] = useState(null);

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
    useEffect(() => {
        form.setFieldValue("periode", periode);
    }, [periode]);

    const handlePeriodeChange = (event) => {
        let currentPeriode = event.target.value;
        setPeriode(currentPeriode);
        const tahun = new Date(form.getFieldValue("tahun")).getFullYear();
        setTahun(tahun);
        if (currentPeriode === "Tahunan") {
            const dates = [
                dayjs(`${tahun}-01`, "YYYY-MM"),
                dayjs(`${tahun}-12`, "YYYY-MM"),
            ];
            form.setFieldValue("bulan", dates);
        }
        if (currentPeriode === "Semester 1") {
            const dates = [
                dayjs(`${tahun}-01`, "YYYY-MM"),
                dayjs(`${tahun}-06`, "YYYY-MM"),
            ];
            form.setFieldValue("bulan", dates);
        }
        if (currentPeriode === "Semester 2") {
            const dates = [
                dayjs(`${tahun}-07`, "YYYY-MM"),
                dayjs(`${tahun}-12`, "YYYY-MM"),
            ];
            form.setFieldValue("bulan", dates);
        }
        if (currentPeriode === "Bulanan") {
            const dates = [dayjs(`${tahun}-01`, "YYYY-MM"), null];
            form.setFieldValue("bulan", dates);
        }
    };
    useEffect(() => {
        form.setFieldValue("file", file);
        // console.log({file});
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

                    <Form.Item name={"tahun"} label="Tahun Penilaian">
                        <DatePicker
                            picker="year"
                            minDate={dayjs("2019-01-01", dateFormat)}
                            disabled={type === "edit"}
                            maxDate={dayjs()}
                        />
                    </Form.Item>
                    <Form.Item name={"periode"} label="Periode Penilaian">
                        <Radio.Group
                            placeholder="Pilih Periode"
                            allowClear
                            optionType="button"
                            buttonStyle="solid"
                            defaultValue={"Bulanan"}
                            onChange={handlePeriodeChange}
                            disabled={type === "edit"}
                            options={[
                                { label: "Bulanan", value: "Bulanan" },
                                { label: "Semester 1", value: "Semester 1" },
                                { label: "Semester 2", value: "Semester 2" },
                                { label: "Tahunan", value: "Tahunan" },
                            ]}
                        />
                    </Form.Item>
                    <Form.Item name={"bulan"} label="Bulan Penilaian">
                                              <RangePicker
                            picker="month"
                            minDate={
                                tahun
                                    ? dayjs(`${tahun}-01-01`, dateFormat)
                                    : dayjs("2019-01-01", dateFormat)
                            }
                            format={"MMMM YYYY"}
                            maxDate={tahun ? dayjs(`${tahun}-12-31`) : dayjs()}
                            onChange={() => setPeriode("Bulanan")}
                            disabled={type === "edit"}

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

                    <Form.Item name={"file"} label="Dokumen PAK" required>
                        <UploadPAK setFile={setFile} />
                    </Form.Item>
                </Form>
            </Modal>
        </>
    );
};

export default CapaianForm;
