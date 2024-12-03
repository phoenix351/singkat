import Card from "@/Components/Card";

const Content = () => {
    const daftarAplikasi = [
        {
            imagePath: route("index"),
            name: <span className="text-nowrap">SINGKAT</span>,
            description: "Pengelolaan Angka Kredit dan Analisis Beban Kerja",
            link: route("singkat"),
        },
    ];
    return (
        <div className="mt-16">
           
            <div className="py-8 px-4 sm:px-8 md:px-16 grid grid-cols-4 sm:grid-cols-5 md:grid-cols-3 xl:grid-cols-5 gap-8 ">
                {daftarAplikasi.map((aplikasi, index) => (
                    <Card
                        imagePath={aplikasi.imagePath}
                        link={aplikasi.link}
                        description={aplikasi.description}
                        name={aplikasi.name}
                        key={index}
                    />
                ))}
            </div>
        </div>
    );
};

export default Content;
