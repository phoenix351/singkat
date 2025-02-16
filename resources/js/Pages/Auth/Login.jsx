import { useEffect, useState } from "react";
import Checkbox from "@/Components/Checkbox";
import GuestLayout from "@/Layouts/GuestLayout";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Head, Link, router, useForm } from "@inertiajs/react";
import { set } from "date-fns";
import axios from "axios";
import { EyeInvisibleOutlined, EyeOutlined } from "@ant-design/icons";

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        login: "",
        password: "",
        remember: false,
        _token: "",
    });
    const [passwordVisible, setPasswordVisible] = useState(false)

    useEffect(() => {
        async function getToken() {
            try {
                const { data } = await axios.get(route("api.token.csrf"));
                setData("_token",data);
                
            } catch (error) {
                console.log("error get token");
            }
        }
        getToken();
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
                                    src={
                                        route("index") +
                                        "/images/logo/Satker-Hitam.png"
                                    }
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
                                            type={passwordVisible? "text":"password"}
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

                                        <span className="absolute right-4 top-4" onClick={()=>{
                                            
                                            setPasswordVisible(!passwordVisible)
                                        }}>
                                            {passwordVisible?<EyeOutlined/>:<EyeInvisibleOutlined/>}
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
                                        className="w-full cursor-pointer rounded-lg border border-gray bg-gray p-4  transition hover:bg-opacity-90"
                                    />
                                </div>
                                <div
                                    className="mb-5"
                                    onClick={() => {
                                        window.location.href = "/sso-login";
                                    }}
                                >
                                    <span
                                        // type="submit"
                                        className="flex items-center justify-center cursor-pointer rounded-lg border  bg-blue-700 p-4  transition hover:bg-opacity-90 text-white"
                                    >Masuk dengan SSO
<img
                                    src={
                                        route("index") +
                                        "/images/logo/logo-bps.png"
                                    }
                                    alt=""
                                    className="ml-2 w-[30px]"
                                />                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
