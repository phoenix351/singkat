import React from "react";

const Alert = ({ tipe, pesan }) => {
    return (
        <>
            {tipe == "error" ? (
                <div
                    className="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-800 dark:text-red-400"
                    role="alert"
                >
                    <span className="font-bold">Gagal!</span> {pesan}
                </div>
            ) : (
                <div
                    className="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                    role="alert"
                >
                    <span className="font-bold">Berhasil!</span> {pesan}
                </div>
            )}
        </>
    );
};

export default Alert;
