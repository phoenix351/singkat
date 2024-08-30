import { useEffect } from "react";
import Checkbox from "@/Components/Checkbox";
import GuestLayout from "@/Layouts/GuestLayout";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Head, Link, router, useForm } from "@inertiajs/react";
import { set } from "date-fns";

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        login: "",
        password: "",
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset("password");
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();

        post(route("login"));
    };

    return (
        <div className="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 md:bg-gray bg-white">
            <Head title="Masuk" />
            <div className="md:w-1/2 w-full bg-white mx-auto rounded-lg md:border border-none border-stroke bg-meta md:shadow-default dark:border-strokedark dark:bg-boxdark">
                <div className="flex flex-wrap items-center justify-center ">
                    <div className="w-full border-stroke dark:border-strokedark ">
                        <div className="w-full p-4 sm:p-12.5 xl:p-17.5">
                            <div className="mb-9 flex justify-center  text-2xl font-bold text-black dark:text-white sm:text-title-xl2">
                                <img
                                    src="/images/logo/Satker-Hitam.png"
                                    alt=""
                                    className="w-90"
                                />
                            </div>

                            <form onSubmit={submit}>
                                <div className="mb-4">
                                    <label className="mb-2.5 block font-medium text-black dark:text-white">
                                        Email / Username
                                    </label>
                                    <div className="relative">
                                        <input
                                            id="email"
                                            type="text"
                                            name="login"
                                            value={data.login}
                                            autoComplete="username"
                                            isFocused={true}
                                            onChange={(e) =>
                                                setData("login", e.target.value)
                                            }
                                            className="w-full rounded-lg border border-stroke bg-transparent py-4 pl-6 pr-10 text-black outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary"
                                        />

                                        <span className="absolute right-4 top-4">
                                            <svg
                                                className="fill-current"
                                                width="22"
                                                height="22"
                                                viewBox="0 0 22 22"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <g opacity="0.5">
                                                    <path
                                                        d="M19.2516 3.30005H2.75156C1.58281 3.30005 0.585938 4.26255 0.585938 5.46567V16.6032C0.585938 17.7719 1.54844 18.7688 2.75156 18.7688H19.2516C20.4203 18.7688 21.4172 17.8063 21.4172 16.6032V5.4313C21.4172 4.26255 20.4203 3.30005 19.2516 3.30005ZM19.2516 4.84692C19.2859 4.84692 19.3203 4.84692 19.3547 4.84692L11.0016 10.2094L2.64844 4.84692C2.68281 4.84692 2.71719 4.84692 2.75156 4.84692H19.2516ZM19.2516 17.1532H2.75156C2.40781 17.1532 2.13281 16.8782 2.13281 16.5344V6.35942L10.1766 11.5157C10.4172 11.6875 10.6922 11.7563 10.9672 11.7563C11.2422 11.7563 11.5172 11.6875 11.7578 11.5157L19.8016 6.35942V16.5688C19.8703 16.9125 19.5953 17.1532 19.2516 17.1532Z"
                                                        fill=""
                                                    />
                                                </g>
                                            </svg>
                                        </span>
                                    </div>
                                    <InputError
                                        message={errors.login}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mb-6">
                                    <label className="mb-2.5 block font-medium text-black dark:text-white">
                                        Password
                                    </label>
                                    <div className="relative">
                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            value={data.password}
                                            autoComplete="current-password"
                                            onChange={(e) =>
                                                setData(
                                                    "password",
                                                    e.target.value
                                                )
                                            }
                                            className="w-full rounded-lg border border-stroke bg-transparent py-4 pl-6 pr-10 text-black outline-none focus:border-primary focus-visible:shadow-none dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary"
                                        />

                                        <span className="absolute right-4 top-4">
                                            <svg
                                                className="fill-current"
                                                width="22"
                                                height="22"
                                                viewBox="0 0 22 22"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <g opacity="0.5">
                                                    <path
                                                        d="M16.1547 6.80626V5.91251C16.1547 3.16251 14.0922 0.825009 11.4797 0.618759C10.0359 0.481259 8.59219 0.996884 7.52656 1.95938C6.46094 2.92188 5.84219 4.29688 5.84219 5.70626V6.80626C3.84844 7.18438 2.33594 8.93751 2.33594 11.0688V17.2906C2.33594 19.5594 4.19219 21.3813 6.42656 21.3813H15.5016C17.7703 21.3813 19.6266 19.525 19.6266 17.2563V11C19.6609 8.93751 18.1484 7.21876 16.1547 6.80626ZM8.55781 3.09376C9.31406 2.40626 10.3109 2.06251 11.3422 2.16563C13.1641 2.33751 14.6078 3.98751 14.6078 5.91251V6.70313H7.38906V5.67188C7.38906 4.70938 7.80156 3.78126 8.55781 3.09376ZM18.1141 17.2906C18.1141 18.7 16.9453 19.8688 15.5359 19.8688H6.46094C5.05156 19.8688 3.91719 18.7344 3.91719 17.325V11.0688C3.91719 9.52189 5.15469 8.28438 6.70156 8.28438H15.2953C16.8422 8.28438 18.1141 9.52188 18.1141 11V17.2906Z"
                                                        fill=""
                                                    />
                                                    <path
                                                        d="M10.9977 11.8594C10.5852 11.8594 10.207 12.2031 10.207 12.65V16.2594C10.207 16.6719 10.5508 17.05 10.9977 17.05C11.4102 17.05 11.7883 16.7063 11.7883 16.2594V12.6156C11.7883 12.2031 11.4102 11.8594 10.9977 11.8594Z"
                                                        fill=""
                                                    />
                                                </g>
                                            </svg>
                                        </span>
                                    </div>
                                    <InputError
                                        message={errors.password}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-5 mb-5.5 flex items-center justify-between">
                                    <label
                                        htmlFor="formCheckbox"
                                        className="flex cursor-pointer"
                                    >
                                        <div className="relative pt-0.5">
                                            <input
                                                name="remember"
                                                checked={data.remember}
                                                onChange={(e) =>
                                                    setData(
                                                        "remember",
                                                        e.target.checked
                                                    )
                                                }
                                                type="checkbox"
                                                id="formCheckbox"
                                                className="taskCheckbox sr-only"
                                            />
                                            <div className="box mr-3 flex h-5 w-5 items-center justify-center rounded border border-stroke dark:border-strokedark">
                                                <span className="text-white opacity-0">
                                                    <svg
                                                        className="fill-current"
                                                        width="10"
                                                        height="7"
                                                        viewBox="0 0 10 7"
                                                        fill="none"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                    >
                                                        <path
                                                            fillRule="evenodd"
                                                            clipRule="evenodd"
                                                            d="M9.70685 0.292804C9.89455 0.480344 10 0.734667 10 0.999847C10 1.26503 9.89455 1.51935 9.70685 1.70689L4.70059 6.7072C4.51283 6.89468 4.2582 7 3.9927 7C3.72721 7 3.47258 6.89468 3.28482 6.7072L0.281063 3.70701C0.0986771 3.5184 -0.00224342 3.26578 3.785e-05 3.00357C0.00231912 2.74136 0.10762 2.49053 0.29326 2.30511C0.4789 2.11969 0.730026 2.01451 0.992551 2.01224C1.25508 2.00996 1.50799 2.11076 1.69683 2.29293L3.9927 4.58607L8.29108 0.292804C8.47884 0.105322 8.73347 0 8.99896 0C9.26446 0 9.51908 0.105322 9.70685 0.292804Z"
                                                            fill=""
                                                        />
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                        <p>Ingat saya</p>
                                    </label>
                                </div>

                                <div className="mb-5">
                                    <input
                                        type="submit"
                                        value="Masuk"
                                        className="w-full cursor-pointer rounded-lg border border-primary bg-primary p-4 text-white transition hover:bg-opacity-90"
                                    />
                                </div>
                                <div
                                    className="mb-5"
                                    onClick={() => {
                                        window.location.href = "/redirect";
                                    }}
                                >
                                    <input
                                        // type="submit"
                                        value="Login SSO"
                                        className="w-full cursor-pointer rounded-lg border  bg-slate-300 p-4  transition hover:bg-opacity-90"
                                    />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
