import Card from "@/Components/Card";
import { usePage } from "@inertiajs/react";
const Content = () => {
    const page = usePage()
    return (
        <div className="mt-16">

            <div className="py-8 px-4 sm:px-8 md:px-16 grid grid-cols-4 sm:grid-cols-5 md:grid-cols-3 xl:grid-cols-5 gap-8 ">
                {page.props.app.map((aplikasi, index) => (
                    <Card
                        imagePath={aplikasi.image_path}
                        link={aplikasi.route_link}
                        description={aplikasi.deskripsi}
                        name={aplikasi.label}
                        key={index}
                        navMode={aplikasi.navigation}
                        isMaintenance={aplikasi.maintenance}
                        maintenanceMessage={aplikasi.maintenance_message}
                    />
                ))}
            </div>
        </div>
    );
};

export default Content;
