import Card from "@/Components/Card";
import { usePage } from "@inertiajs/react";
const Content = () => {
    const page = usePage()
    const daftarAplikasi = [
        {
            imagePath: route('index'),
            name: <span className="text-nowrap">SINGKAT</span>,
            description: "Pengelolaan Angka Kredit dan Analisis Beban Kerja",
            link: route("singkat"),
            navMode: 'react',
            isMaintenance: true,
            maintenanceMessage: "Aplikasi sedang dalam pemeliharaan"
        },
        {
            imagePath: route('index') + "/images/logo/MeetSulut-Zoom-Logo.png",
            name: <span className="text-nowrap">MeetSulut</span>,
            description: "Pengajuan dan Pengelolaan Zoom",
            link: route("meeting.dashboard"),
            navMode: 'reload',
        },
        {
            imagePath: route('index') + "/images/logo/Simple-Logo.png",
            name: <span className="text-nowrap">SIMPLE</span>,
            description: "Pengajuan dan Pengelolaan Lembur",
            link: route("singkat"),
            navMode: 'reload',
            isMaintenance: true,
            maintenanceMessage: "Aplikasi sedang dalam proses migrasi"
        },
        {
            imagePath: route('index') + "/images/logo/Man-Management-Logo.png",
            name: <span className="text-nowrap">MANMENT</span>,
            description: "Pengelolaan Pegawai dalam Aplikasi",
            link: route("man-management.index"),
            navMode: 'reload',
        },
    ];
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
