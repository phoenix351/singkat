import { useEffect, useMemo, useState } from "react";
import { Head, router, usePage } from "@inertiajs/react";
import axios from "axios";
import { Modal, Select } from "antd";
import {
    ArrowRightOutlined,
    ExclamationCircleOutlined,
    LoadingOutlined,
    LoginOutlined,
    UserSwitchOutlined,
} from "@ant-design/icons";

export default function Login({ status, isLocal = false }) {
    const { flash } = usePage().props;
    const currentYear = new Date().getFullYear();

    const [isSsoLoading, setIsSsoLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");

    // State Login Lokal (Development)
    const [isLocalEnv, setIsLocalEnv] = useState(Boolean(isLocal));
    const [isLocalModalOpen, setIsLocalModalOpen] = useState(false);
    const [isLoadingUsers, setIsLoadingUsers] = useState(false);
    const [isLocalSubmitting, setIsLocalSubmitting] = useState(false);
    const [localUsersList, setLocalUsersList] = useState([]);
    const [selectedLocalUserId, setSelectedLocalUserId] = useState(null);
    const [localLoginError, setLocalLoginError] = useState("");

    useEffect(() => {
        if (typeof window !== "undefined") {
            const host = window.location.hostname;
            const hostIsLocal =
                ["localhost", "127.0.0.1"].includes(host) ||
                host.endsWith(".test") ||
                host.endsWith(".local");
            setIsLocalEnv(Boolean(isLocal) || hostIsLocal);

            const params = new URLSearchParams(window.location.search);
            const ssoError = params.get("sso_error");
            if (ssoError) {
                const errorMap = {
                    token_exchange_failed:
                        "Gagal melakukan otentikasi dengan server SSO.",
                    no_access_token: "Access token SSO tidak valid.",
                    userinfo_failed:
                        "Gagal mengambil informasi data pegawai dari SSO.",
                    user_not_registered:
                        "Akun SSO Anda belum terdaftar di aplikasi.",
                };
                setErrorMessage(
                    errorMap[ssoError] || "Terjadi kesalahan pada SSO Login."
                );
            }
        }
    }, [isLocal]);

    useEffect(() => {
        if (flash?.error) {
            setErrorMessage(flash.error);
        }
    }, [flash?.error]);

    const localUserOptions = useMemo(
        () =>
            localUsersList.map((u) => ({
                label: u.label,
                value: u.id,
                searchText: `${u.name} ${u.username} ${u.nip_lama} ${u.nip} ${u.email} ${u.jabatan} ${u.organisasi}`.toLowerCase(),
            })),
        [localUsersList]
    );

    const selectedUserDetail = useMemo(() => {
        if (!selectedLocalUserId) return null;
        return (
            localUsersList.find((u) => u.id === selectedLocalUserId) || null
        );
    }, [localUsersList, selectedLocalUserId]);

    const handleSsoLogin = () => {
        setIsSsoLoading(true);
        setErrorMessage("");
        window.location.href = route("sso-login");
    };

    const openLocalModal = async () => {
        setIsLocalModalOpen(true);
        setLocalLoginError("");

        if (localUsersList.length === 0) {
            setIsLoadingUsers(true);
            try {
                const { data } = await axios.get(route("auth.local-users"));
                const users = Array.isArray(data) ? data : [];
                setLocalUsersList(users);
                if (users.length > 0) {
                    setSelectedLocalUserId(users[0].id);
                }
            } catch (err) {
                setLocalLoginError(
                    err?.response?.data?.message ||
                        "Gagal memuat daftar user lokal."
                );
            } finally {
                setIsLoadingUsers(false);
            }
        }
    };

    const doLocalLogin = () => {
        if (!selectedLocalUserId) return;
        setIsLocalSubmitting(true);
        setLocalLoginError("");

        router.post(
            route("auth.local-login"),
            { user_id: selectedLocalUserId },
            {
                onError: (errors) => {
                    setLocalLoginError(
                        errors?.user_id ||
                            Object.values(errors || {})[0] ||
                            "Gagal login lokal."
                    );
                },
                onFinish: () => {
                    setIsLocalSubmitting(false);
                },
            }
        );
    };

    return (
        <div
            className="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-4 py-10 bg-slate-50 dark:bg-slate-950"
            style={{
                backgroundImage:
                    "radial-gradient(rgba(148, 163, 184, 0.25) 1px, transparent 1px)",
                backgroundSize: "24px 24px",
            }}
        >
            <Head title="Masuk" />

            {/* Subtle decorative background accents (BPS tricolor ambient glow) */}
            <div className="pointer-events-none absolute -top-32 -left-32 h-96 w-96 rounded-full bg-[#0093DD]/10 blur-3xl" />
            <div className="pointer-events-none absolute top-1/2 -right-32 h-96 w-96 -translate-y-1/2 rounded-full bg-[#68B92E]/10 blur-3xl" />
            <div className="pointer-events-none absolute -bottom-32 left-1/3 h-80 w-80 rounded-full bg-[#EB891B]/10 blur-3xl" />

            <div className="relative z-10 w-full max-w-md">
                {/* Main Card */}
                <div className="overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 shadow-xl shadow-slate-200/60 dark:shadow-black/40 backdrop-blur-md transition-colors">
                    {/* BPS Tricolor Accent Bar */}
                    <div className="flex h-1.5 w-full">
                        <div className="flex-1 bg-[#0093DD]" />
                        <div className="flex-1 bg-[#68B92E]" />
                        <div className="flex-1 bg-[#EB891B]" />
                    </div>

                    <div className="px-7 pt-8 pb-7 sm:px-9 sm:pt-9 sm:pb-8">
                        {/* Brand Header */}
                        <div className="flex flex-col items-center text-center">
                            <img
                                src={`${route("index")}/images/logo/Satker-Hitam.png`}
                                alt="Logo BPS Sulawesi Utara"
                                className="h-auto max-w-[240px] object-contain"
                            />
                            <p className="mt-3 max-w-xs text-xs leading-relaxed font-medium text-slate-500 dark:text-slate-400 sm:text-sm">
                                Portal Aplikasi Terpadu BPS Provinsi Sulawesi
                                Utara
                            </p>
                        </div>

                        <div className="my-6 h-px w-full bg-gradient-to-r from-transparent via-slate-200 dark:via-slate-700 to-transparent" />

                        {/* Status Message */}
                        {status && (
                            <div className="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-medium text-emerald-700">
                                {status}
                            </div>
                        )}

                        {/* Error Alert */}
                        {errorMessage && (
                            <div className="mb-5 flex items-start gap-2.5 rounded-xl border border-red-200 bg-red-50/90 px-4 py-3 text-xs text-red-700 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-300">
                                <ExclamationCircleOutlined className="mt-0.5 text-sm shrink-0 text-red-500" />
                                <div>
                                    <div className="font-semibold">
                                        Login Gagal
                                    </div>
                                    <div className="mt-0.5 leading-relaxed">
                                        {errorMessage}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Login Prompt & Actions */}
                        <div className="space-y-4">
                            <div className="text-center">
                                <h2 className="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                    Masuk ke Aplikasi
                                </h2>
                                <p className="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    Silakan autentikasi menggunakan akun SSO
                                    Pegawai BPS Anda.
                                </p>
                            </div>

                            {/* Primary SSO BPS Button */}
                            <button
                                type="button"
                                disabled={isSsoLoading}
                                onClick={handleSsoLogin}
                                className="group relative flex w-full items-center justify-center gap-3 rounded-xl bg-slate-900 px-5 py-3.5 text-sm font-semibold text-white shadow-md shadow-slate-900/15 transition-all duration-200 hover:bg-slate-800 hover:shadow-lg hover:shadow-slate-900/20 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white p-1 shadow-sm">
                                    {isSsoLoading ? (
                                        <LoadingOutlined className="text-sm text-slate-800" />
                                    ) : (
                                        <img
                                            src={`${route("index")}/images/logo/logo-bps.png`}
                                            alt="Logo BPS"
                                            className="h-full w-full object-contain"
                                        />
                                    )}
                                </span>

                                <span>
                                    {isSsoLoading
                                        ? "Menghubungkan ke SSO..."
                                        : "SSO Login BPS"}
                                </span>

                                {!isSsoLoading && (
                                    <ArrowRightOutlined className="text-xs text-slate-400 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:text-white" />
                                )}
                            </button>

                            {/* Local Development Login */}
                            {isLocalEnv && (
                                <>
                                    <div className="relative flex items-center py-1">
                                        <div className="grow border-t border-slate-200 dark:border-slate-700" />
                                        <span className="mx-3 shrink-0 text-[11px] font-medium tracking-wider text-slate-400 dark:text-slate-500 uppercase">
                                            Development
                                        </span>
                                        <div className="grow border-t border-slate-200 dark:border-slate-700" />
                                    </div>

                                    <button
                                        type="button"
                                        onClick={openLocalModal}
                                        className="flex w-full items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-semibold text-amber-700 transition-all duration-200 hover:bg-amber-100 hover:border-amber-300 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 active:scale-[0.99]"
                                    >
                                        <UserSwitchOutlined className="text-base" />
                                        <span>Login Lokal</span>
                                    </button>
                                </>
                            )}
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <p className="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">
                    &copy; {currentYear} Badan Pusat Statistik Provinsi Sulawesi
                    Utara
                </p>
            </div>

            {/* Modal Login Lokal */}
            <Modal
                title="Login Lokal (Development)"
                open={isLocalModalOpen}
                onCancel={() => setIsLocalModalOpen(false)}
                centered
                footer={
                    <div className="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            onClick={() => setIsLocalModalOpen(false)}
                            className="rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            disabled={!selectedLocalUserId || isLocalSubmitting}
                            onClick={doLocalLogin}
                            className="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {isLocalSubmitting ? (
                                <LoadingOutlined />
                            ) : (
                                <LoginOutlined />
                            )}
                            <span>
                                {isLocalSubmitting
                                    ? "Memproses..."
                                    : "Masuk sebagai User Ini"}
                            </span>
                        </button>
                    </div>
                }
            >
                <div className="space-y-4 py-2 text-xs">
                    <p className="text-slate-600">
                        Pilih user pegawai yang ingin Anda gunakan untuk
                        simulasi login di lingkungan lokal.
                    </p>

                    <div className="space-y-1.5">
                        <label className="block font-semibold text-slate-700">
                            Pilih User <span className="text-red-500">*</span>
                        </label>
                        {isLoadingUsers ? (
                            <div className="flex items-center gap-2 py-2 text-slate-500">
                                <LoadingOutlined className="text-sm" />
                                <span>Memuat daftar user...</span>
                            </div>
                        ) : (
                            <Select
                                showSearch
                                className="w-full"
                                size="large"
                                placeholder="-- Pilih User untuk Login --"
                                value={selectedLocalUserId}
                                onChange={(val) => setSelectedLocalUserId(val)}
                                options={localUserOptions}
                                filterOption={(input, option) =>
                                    (option?.searchText ?? "").includes(
                                        input.toLowerCase()
                                    )
                                }
                            />
                        )}
                    </div>

                    {/* Preview Info User Terpilih */}
                    {selectedUserDetail && (
                        <div className="space-y-2 rounded-xl border border-slate-200 bg-slate-50 p-3.5">
                            <div className="flex items-center justify-between gap-2">
                                <span className="text-slate-500">Nama:</span>
                                <span className="text-right font-semibold text-slate-900">
                                    {selectedUserDetail.name}
                                </span>
                            </div>
                            <div className="flex items-center justify-between gap-2">
                                <span className="text-slate-500">
                                    Username:
                                </span>
                                <span className="text-right font-medium text-slate-700">
                                    {selectedUserDetail.username}
                                </span>
                            </div>
                            <div className="flex items-center justify-between gap-2">
                                <span className="text-slate-500">
                                    NIP Lama:
                                </span>
                                <span className="text-right font-mono text-slate-700">
                                    {selectedUserDetail.nip_lama}
                                </span>
                            </div>
                            <div className="flex items-center justify-between gap-2">
                                <span className="text-slate-500">Jabatan:</span>
                                <span className="text-right text-slate-700">
                                    {selectedUserDetail.jabatan}
                                </span>
                            </div>
                            <div className="flex items-center justify-between gap-2">
                                <span className="text-slate-500">
                                    Organisasi:
                                </span>
                                <span className="text-right text-slate-700">
                                    {selectedUserDetail.organisasi}
                                </span>
                            </div>
                            <div className="flex items-center justify-between gap-2">
                                <span className="text-slate-500">Email:</span>
                                <span className="text-right text-slate-700">
                                    {selectedUserDetail.email}
                                </span>
                            </div>
                        </div>
                    )}

                    {localLoginError && (
                        <div className="rounded-lg border border-red-200 bg-red-50 p-2.5 text-xs text-red-600">
                            {localLoginError}
                        </div>
                    )}
                </div>
            </Modal>
        </div>
    );
}
