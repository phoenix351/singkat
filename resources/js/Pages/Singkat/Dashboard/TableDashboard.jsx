import React, { useEffect, useState } from "react";
import axios from "axios";
import { Empty } from "antd";

const TableDashboard = () => {
    const [data, setData] = useState([]);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get(
                    route("index") + "/api/abk-summary"
                );
                setData(response.data);
            } catch (error) {
                console.error("Error fetching data: ", error);
            }
        };

        fetchData();
    }, []);

    return (
        <div className="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div className="max-w-full overflow-x-auto">
                <div className="flex justify-between items-center mb-5">
                    <h3 className="font-lg font-bold text-black dark:text-white">
                        Jumlah ABK Berdasarkan Unit Kerja
                    </h3>
                </div>
                <table className="w-full table-auto">
                    <thead>
                        <tr className="bg-gray-2  text-left dark:bg-meta-4">
                            <th className="py-4 px-4 font-bold text-black dark:text-white">
                                No
                            </th>
                            <th className="min-w-[50px] py-4 px-4 font-bold text-black dark:text-white xl:pl-11">
                                Unit Kerja
                            </th>
                            <th className="min-w-[220px] py-4 px-4 font-bold text-black dark:text-white xl:pl-11">
                                Jumlah ABK
                            </th>
                            <th className="min-w-[220px] py-4 px-4 font-bold text-black dark:text-white">
                                Jumlah Eksisting
                            </th>
                            <th className="min-w-[120px] py-4 px-4 font-bold text-black dark:text-white">
                                Jumlah Kebutuhan Pegawai
                            </th>
                            <th className="min-w-[120px] py-4 px-4 font-bold text-black dark:text-white">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody className="">
                        {data.length > 0 ? (
                            <>
                                {data.map((item, index) => (
                                    <tr
                                        key={index}
                                        className={
                                            index % 3 === 0
                                                ? "bg-[#addfff] dark:bg-meta-4"
                                                : index % 3 === 1
                                                ? "bg-[#d1ffbd] dark:bg-meta-4"
                                                : "bg-[#ffeb8f] dark:bg-meta-4"
                                        }
                                    >
                                        <td className=" bg-gray-2 py-2 px-4 dark:border-strokedark dark:bg-meta-4">
                                            <p className="text-black dark:text-white">
                                                {index + 1}
                                            </p>
                                        </td>
                                        <td className=" border border-s-1 font-bold bg-gray-2 border-[#eee] py-5 px-4 dark:border-strokedark dark:bg-meta-4">
                                            <p className="text-black dark:text-white">
                                                {item.unit_kerja_nama}
                                            </p>
                                        </td>
                                        <td className="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                                            <p className="text-black dark:text-white">
                                                {item.total_abk}
                                            </p>
                                        </td>
                                        <td className="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                                            <p className="text-black dark:text-white">
                                                {item.total_eksisting}
                                            </p>
                                        </td>
                                        <td className="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                                            <p className="text-black dark:text-white">
                                                {item.total_kebutuhan_pegawai}
                                            </p>
                                        </td>

                                        <td className="border-b border-[#eee] py-5 px-4 dark:border-strokedark">
                                            <p className="text-black dark:text-white">
                                                {item.total}
                                            </p>
                                        </td>
                                    </tr>
                                ))}
                            </>
                        ) : (
                            <tr>
                                <td
                                    colSpan="7"
                                    className="text-center py-5 dark:border-strokedark"
                                >
                                    <Empty description={"Data ABK Belum Ada"} />
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default TableDashboard;
