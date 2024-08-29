import React, { useState } from "react";
import { Modal, Checkbox, Row, Col, Switch } from "antd";

const columns = [
    { label: "NIP BPS", value: "nip_bps" },
    { label: "NIP", value: "nip" },
    { label: "Nama", value: "nama" },
    { label: "Jabatan", value: "jabatan" },
    { label: "Unit Kerja", value: "unit_kerja" },
    { label: "Pangkat/Golongan/Ruang", value: "pangkat_golongan_ruang" },
    { label: "Angka Kredit Konvensional", value: "angka_kredit_konvensional" },
    { label: "Angka Kredit Integrasi", value: "angka_kredit_integrasi" },
    { label: "Predikat Kinerja", value: "predikat_kinerja" },
    { label: "Tambahan Ijazah", value: "tambahan_ijazah" },
    { label: "Akumulasi AK", value: "akumulasi_ak" },
    { label: "Ijazah Terakhir", value: "ijazah_terakhir" },
    { label: "TMT Pensiun", value: "tmt_pensiun" },
    { label: "Usia", value: "usia" },
];

const ExportModal = ({ visible, onCancel }) => {
    const [selectedColumns, setSelectedColumns] = useState(
        columns.map((col) => col.value)
    );

    const onColumnChange = (checkedValues) => {
        setSelectedColumns(checkedValues);
    };

    const handleSwitchChange = (checked) => {
        if (checked) {
            setSelectedColumns(columns.map((col) => col.value));
        } else {
            setSelectedColumns([]);
        }
    };

    function sendArrayData() {
        const queryString = selectedColumns
            .map((id) => `columns[]=${encodeURIComponent(id)}`)
            .join("&");
        const url = `/export-pegawai?${queryString}`;
        window.location.href = url;
    }

    return (
        <Modal
            title="Pilih Kolom untuk Ekspor"
            open={visible}
            onCancel={onCancel}
            footer={null}
        >
            <div className="flex flex-row gap-10 justify-start align-center my-4">
                <Switch
                    checked={selectedColumns.length === columns.length}
                    onChange={handleSwitchChange}
                    checkedChildren="Pilih Semua"
                    unCheckedChildren="Jangan Pilih Semua"
                />
            </div>
            <Checkbox.Group
                value={selectedColumns}
                onChange={onColumnChange}
                style={{ width: "100%" }}
            >
                <Row>
                    {columns.map((col) => (
                        <Col span={12} key={col.value}>
                            <Checkbox value={col.value}>{col.label}</Checkbox>
                        </Col>
                    ))}
                </Row>
            </Checkbox.Group>

            {/* Tombol Button */}
            <div className="mt-4 flex justify-end">
                <button
                    onClick={onCancel}
                    className="px-4 py-2 bg-gray-300 border text-gray-800 rounded-md mr-2"
                >
                    Batal
                </button>
                <a
                    href="javascript:void(0);"
                    onClick={sendArrayData}
                    className="px-4 py-2 bg-primary text-white rounded-md"
                >
                    Ekspor
                </a>
            </div>
        </Modal>
    );
};

export default ExportModal;
