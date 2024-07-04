import { useRef, useState } from "react";
import { Button, Modal, Input, Form, message } from "antd";
import { useForm } from "@inertiajs/react";

export default function DeleteUserForm({ className = "" }) {
    const [confirmingUserDeletion, setConfirmingUserDeletion] = useState(false);
    const passwordInput = useRef();

    const {
        data,
        setData,
        delete: destroy,
        processing,
        reset,
        errors,
    } = useForm({
        password: "",
    });

    const confirmUserDeletion = () => {
        setConfirmingUserDeletion(true);
    };

    const deleteUser = (e) => {
        e.preventDefault();

        destroy(route("profile.destroy"), {
            preserveScroll: true,
            onSuccess: () => {
                message.success("Account deleted successfully.");
                closeModal();
            },
            onError: () => {
                message.error("Error deleting account. Please try again.");
                passwordInput.current.focus();
            },
            onFinish: () => reset(),
        });
    };

    const closeModal = () => {
        setConfirmingUserDeletion(false);
        reset();
    };

    return (
        <section className={`space-y-6 ${className}`}>
            <header>
                <h2 className="text-lg font-medium text-gray-900">
                    Hapus Akun
                </h2>
                <p className="mt-1 text-sm text-gray-600">
                    Hapus akun Anda secara permanen.
                </p>
            </header>

            <Button danger onClick={confirmUserDeletion}>
                Hapus Akun
            </Button>

            <Modal
                title="Hapus Akun"
                visible={confirmingUserDeletion}
                onCancel={closeModal}
                footer={null}
            >
                <Form onFinish={deleteUser} layout="vertical">
                    <Form.Item>
                        <h2 className="text-lg font-medium text-gray-900">
                            Apa anda yakin ingin menghapus akun anda secara
                            permanen?
                        </h2>
                        <p className="mt-1 text-sm text-gray-600">
                            Setelah akun anda dihapus, semua data dan informasi
                            akan dihapus secara permanen.
                        </p>
                    </Form.Item>

                    <Form.Item
                        label="Password"
                        validateStatus={errors.password ? "error" : ""}
                        help={errors.password}
                    >
                        <Input.Password
                            id="password"
                            name="password"
                            ref={passwordInput}
                            value={data.password}
                            onChange={(e) =>
                                setData("password", e.target.value)
                            }
                            placeholder="Password"
                            autoFocus
                        />
                    </Form.Item>

                    <Form.Item>
                        <div className="flex justify-end">
                            <Button onClick={closeModal} disabled={processing}>
                                Cancel
                            </Button>
                            <Button
                                type="primary"
                                danger
                                htmlType="submit"
                                className="ml-3"
                                loading={processing}
                            >
                                Hapus Akun
                            </Button>
                        </div>
                    </Form.Item>
                </Form>
            </Modal>
        </section>
    );
}
