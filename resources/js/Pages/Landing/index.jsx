import CardDataStats from "@/Components/CardDataStats";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";

import React, { useEffect } from "react";
import axios from "axios";

import LandingLayout from "@/Layouts/LandingLayout";
import Content from "./Content";

export default function index({ auth, jumlahPegawai, users, unitKerja }) {
    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get("/api/abk-summary");
                setChartData(response.data);
            } catch (error) {
                console.error("Error fetching data: ", error);
            }
        };

        fetchData();
    }, []);
    return (
        <LandingLayout user={auth.user}>
            <Head title="Dashboard" />

            <Content />
        </LandingLayout>
    );
}
