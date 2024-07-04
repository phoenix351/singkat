import React, { useState } from "react";

const SearchInput = ({ initialQuery, handleSearch }) => {
    const [query, setQuery] = useState(initialQuery || "");

    function handleSearchForm(e) {
        e.preventDefault();
        handleSearch(query);
    }

    return (
        <form className="flex  w-90" onSubmit={handleSearchForm}>
            <div className="flex">
                <div className="relative md:w-72 w-[90%]">
                    <input
                        type="text"
                        placeholder="Cari Data..."
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        className="w-full rounded-lg border-[1.5px] border-stroke bg-transparent py-3 px-5 text-black outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white dark:focus:border-primary"
                    />
                    <button
                        type="submit"
                        className="absolute top-0 end-0 p-4 text-sm font-medium md:h-full h-13 text-white bg-primary rounded-e-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    >
                        <svg
                            className="w-4 h-4"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 20 20"
                        >
                            <path
                                stroke="currentColor"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                            />
                        </svg>
                        <span className="sr-only">Search</span>
                    </button>
                </div>
            </div>
        </form>
    );
};

export default SearchInput;
