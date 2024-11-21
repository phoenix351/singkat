import React from "react";

const CardPAK = ({ title, children }) => {
    return (
        <div className="col-span-5 xl:col-span-3">
            <div className="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div className="border-b border-stroke py-4 px-7 dark:border-strokedark">
                    <h3 className="font-lg font-bold text-black dark:text-white">
                        {title}
                    </h3>
                </div>
                <div className="p-7 text-2lg">{children}</div>
            </div>
        </div>
    );
};

export default CardPAK;
