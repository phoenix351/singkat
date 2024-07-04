import React, { useState } from "react";
import { Modal, Checkbox, Radio, Space, Row, Col, Switch } from "antd";

const ExportModal = ({ visible, onCancel, unitKerja }) => {
    const [selectedUnitKerja, setSelectedUnitKerja] = useState(
        unitKerja.map((data) => data.id)
    );
    const [showAllJabatan, setShowAllJabatan] = useState(false);

    const onUnitKerjaChange = (checkedValues) => {
        setSelectedUnitKerja(checkedValues);
    };

    const onShowAllJabatanChange = (e) => {
        setShowAllJabatan(e.target.value);
    };

    const handleSwitchChange = (checked) => {
        if (checked) {
            setSelectedUnitKerja(unitKerja.map((col) => col.id));
        } else {
            setSelectedUnitKerja([]);
        }
    };

    function sendArrayData() {
        const queryString = selectedUnitKerja
            .map((id) => `columns[]=${encodeURIComponent(id)}`)
            .join("&");
        const url = `/export-abk?${queryString}&showAllJabatan=${showAllJabatan}`;
        window.location.href = url;
    }

    return (
        <Modal
            title="Pilih Kolom untuk Ekspor"
            open={visible}
            onCancel={onCancel}
            footer={null}
            width={600}
        >
            <div className="flex flex-row gap-10 align-center my-4">
                <Switch
                    checked={selectedUnitKerja.length === unitKerja.length}
                    onChange={handleSwitchChange}
                    checkedChildren="Pilih Semua"
                    unCheckedChildren="Tidak Pilih Semua"
                />
            </div>
            <Checkbox.Group
                value={selectedUnitKerja}
                onChange={onUnitKerjaChange}
                style={{ width: "100%" }}
            >
                <Row>
                    {unitKerja.map((col) => (
                        <Col span={12} key={col.id}>
                            <Checkbox value={col.id}>{col.nama}</Checkbox>
                        </Col>
                    ))}
                </Row>
            </Checkbox.Group>

            <div className="mt-4">
                <Radio.Group
                    onChange={onShowAllJabatanChange}
                    value={showAllJabatan}
                >
                    <Space direction="vertical">
                        <Radio value={true}>Tampilkan Semua Jabatan</Radio>
                        <Radio value={false}>
                            Tampilkan Jabatan dengan Nilai ABK
                        </Radio>
                    </Space>
                </Radio.Group>
            </div>

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
