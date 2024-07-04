import React from "react";
import { Link } from "@inertiajs/react";

const Pagination = ({ links, currentPage, lastPage, total, from, to }) => {
    const getPaginationGroup = () => {
        const totalShownPages = 5;
        let startPage = Math.max(
            currentPage - Math.floor(totalShownPages / 2),
            1
        );
        let endPage = startPage + totalShownPages - 1;

        if (endPage > lastPage) {
            endPage = lastPage;
            startPage = Math.max(endPage - totalShownPages + 1, 1);
        }

        const pages = [];
        for (let i = startPage; i <= endPage; i++) {
            pages.push(i);
        }
        return pages;
    };

    const paginationGroup = getPaginationGroup();

    const getUrlForPage = (page) => {
        const link = links.find((link) => link.label == page);
        return link ? link.url : "#";
    };

    return (
        <>
            <div className="flex justify-between mt-4">
                <p className="text-slate-500">
                    Menampilkan {from} - {to} dari {total} data
                </p>
                <div className="flex justify-between">
                    <Link
                        href={
                            currentPage > 1
                                ? getUrlForPage(currentPage - 1)
                                : "#"
                        }
                        className={`px-4 py-2 rounded-s-lg  text-slate-500 flex justify-center items-center ${
                            currentPage === 1
                                ? "text-slate-300 cursor-not-allowed "
                                : "hover:bg-slate-200 rounded-lg mx-1"
                        }`}
                    >
                        <svg
                            className="w-3 h-3 rtl:rotate-180"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 6 10"
                        >
                            <path
                                stroke="currentColor"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M5 1 1 5l4 4"
                            />
                        </svg>
                    </Link>

                    {paginationGroup[0] > 1 && (
                        <>
                            <Link
                                href={getUrlForPage(1)}
                                className="px-4 py-2 hover:bg-slate-200 rounded-lg"
                            >
                                1
                            </Link>
                            {paginationGroup[0] > 2 && (
                                <span className="px-4 py-2 font-bold text-slate-400 tracking-widest">
                                    ...
                                </span>
                            )}
                        </>
                    )}

                    {paginationGroup.map((page) => (
                        <Link
                            key={page}
                            href={getUrlForPage(page)}
                            className={`px-4 py-2   ${
                                currentPage === page
                                    ? "bg-blue-100 text-primary border rounded-lg border-primary dark:bg-blue-300 dark:text-white dark:border-primary dark:bg-opacity-10"
                                    : "hover:bg-slate-200 rounded-lg mx-1"
                            }`}
                        >
                            {page}
                        </Link>
                    ))}

                    {paginationGroup[paginationGroup.length - 1] < lastPage && (
                        <>
                            {paginationGroup[paginationGroup.length - 1] <
                                lastPage - 1 && (
                                <span className="px-4 py-2 font-bold text-slate-400 tracking-widest">
                                    ...
                                </span>
                            )}
                            <Link
                                href={getUrlForPage(lastPage)}
                                className="px-4 py-2 hover:bg-slate-200 rounded-lg"
                            >
                                {lastPage}
                            </Link>
                        </>
                    )}

                    <Link
                        href={
                            currentPage < lastPage
                                ? getUrlForPage(currentPage + 1)
                                : "#"
                        }
                        className={`px-4 py-2  flex justify-center rounded-e-lg items-center text-slate-500 ${
                            currentPage === lastPage
                                ? "text-slate-300 cursor-not-allowed "
                                : "hover:bg-slate-200 rounded-lg mx-1"
                        }`}
                    >
                        <svg
                            className="w-3 h-3 rtl:rotate-180"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 6 10"
                        >
                            <path
                                stroke="currentColor"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="m1 9 4-4-4-4"
                            />
                        </svg>
                    </Link>
                </div>
            </div>

            <div className="flex justify-center mt-4"></div>
        </>
    );
};

export default Pagination;
