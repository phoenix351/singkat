import { router } from "@inertiajs/react";

const Card = ({ imagePath, name, description, link }) => {
    return (
        <div
            className="w-full flex flex-col cursor-pointer bg-slate-100  shadow-slate-400 items-center dark:hover:text-slate-900 dark:bg-boxdark hover:bg-slate-100 dark:hover:bg-slate-100   rounded-xl p-2 py-4 md:hover:scale-110  transition ease-in-out delay-150"
            onClick={() =>
                router.get(
                    link,
                    {},
                    {
                        preserveState: true,
                    }
                )
            }
        >
            <img
                onError={(event) => {
                    event.target.src = route('index')+"/images/logo/logo-bps.png";
                }}
                src={imagePath}
                // src={"/images/logo-bps.png"}
                alt={`Logo aplikasi ${name}`}
                className="h-[32px] md:h-[100px] rounded-md  mb-4"
                loading="lazy"
            />
            <span className="text-[0.8rem] md:text-lg font-medium mb-2 px-8 text-gray-900 text-center ">
                {name}
            </span>
            <p className="text-gray-600 mb-4 px-8 text-center text-sm hidden md:inline-block">
                {description}
            </p>
        </div>
    );
};

export default Card;
