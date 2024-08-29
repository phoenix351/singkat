import Card from "@/Components/Card";

const Content = () => {
    const daftarAplikasi = [
        {
            imagePath: "images/fordone.webp",
            name: <span className="text-nowrap">SINGKAT</span>,
            description: "Sistem Informasi Angka Kredit",
            link: "/singkat",
        },
    ];
    return (
        <div className="mt-16">
            {/* <Banner
        title="Salam Bakudapa !"
        description="Selamat datang di Portal BPS Provinsi Sulawesi Utara.
Portal ini memuat kumpulan aplikasi yang digunakan
oleh pegawai BPS Provinsi Sulawesi Utara."
        imagePath="images/banner.webp"
      /> */}
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
