import CardDataStats from "@/Components/CardDataStats";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, usePage } from "@inertiajs/react";

import React, { useEffect } from "react";
import axios from "axios";

import LandingLayout from "@/Layouts/LandingLayout";
import Content from "./Content";

export default function index({ auth }) {
    return (
        <LandingLayout user={auth.user}>
            <Head title="Dashboard" />
            <Content />
        </LandingLayout>
    );
}
