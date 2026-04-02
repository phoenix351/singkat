import { router } from "@inertiajs/react";

const Card = ({
    imagePath,
    name,
    description,
    link,
    navMode,
    isMaintenance = false,
    maintenanceMessage = "Aplikasi sedang dalam pemeliharaan",
}) => {
    const handleClick = () => {
        if (isMaintenance) return;

        if (navMode === "reload") {
            window.location.href = link;
            return;
        }

        router.get(link, {}, {
            preserveState: true,
        });
    };

    return (
        <div className="relative w-full group h-full">
            <div
                className={`w-full flex flex-col items-center rounded-xl p-2 py-4 shadow-slate-400 transition ease-in-out delay-150
                    ${
                        isMaintenance
                            ? "cursor-not-allowed bg-slate-200 opacity-80"
                            : "cursor-pointer bg-slate-100 dark:bg-boxdark hover:bg-slate-100 dark:hover:bg-slate-100 dark:hover:text-slate-900 md:hover:scale-110"
                    }`}
                onClick={handleClick}
            >
                <img
                    onError={(event) => {
                        event.target.src = route("index") + "/images/logo/logo-bps.png";
                    }}
                    src={imagePath}
                    alt={`Logo aplikasi ${name}`}
                    className="h-[32px] md:h-[100px] rounded-md mb-4"
                    loading="lazy"
                />

                <span className="text-[0.8rem] md:text-lg font-medium mb-2 px-8 text-gray-900 text-center">
                    {name}
                </span>

                <p className="text-gray-600 mb-4 px-8 text-center text-sm hidden md:inline-block">
                    {description}
                </p>
            </div>

            {isMaintenance && (
                <div className="absolute inset-0 bg-black/60 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                    <span className="text-white text-sm md:text-lg font-semibold text-center px-4">
                        {maintenanceMessage}
                    </span>
                </div>
            )}
        </div>
    );
};

export default Card;