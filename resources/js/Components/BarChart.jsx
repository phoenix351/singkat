import React, { useEffect, useRef } from "react";
import { Card } from "antd";
import * as echarts from "echarts";

const BarChart = ({ chartData }) => {
    const chartRef = useRef(null);

    useEffect(() => {
        if (chartData.length > 0) {
            const chartDom = chartRef.current;
            const myChart = echarts.init(chartDom);

            const option = {
                title: {
                    text: "Total ABK, Eksisting, Kebutuhan Pegawai per Unit Kerja",
                    left: "center",
                },
                tooltip: {
                    trigger: "axis",
                    axisPointer: {
                        type: "shadow",
                    },
                },
                legend: {
                    data: ["ABK", "Eksisting", "Kebutuhan Pegawai"],
                    bottom: 0,
                },
                grid: {
                    left: "3%",
                    right: "4%",
                    bottom: "3%",
                    containLabel: true,
                },
                xAxis: {
                    type: "value",
                },
                yAxis: {
                    type: "category",
                    data: chartData.map((item) => item.unit_kerja),
                    inverse: true, // Mengatur batang dari kiri ke kanan
                },

                series: [
                    {
                        name: "ABK",
                        type: "bar",
                        data: chartData.map((item) => item.abk),
                        label: {
                            show: true,
                            position: "right",
                            formatter: (params) =>
                                params.value !== 0 ? params.value : "",
                        },
                    },
                    {
                        name: "Eksisting",
                        type: "bar",
                        data: chartData.map((item) => item.eksisting),
                        label: {
                            show: true,
                            position: "right",
                            formatter: (params) =>
                                params.value !== 0 ? params.value : "",
                        },
                    },
                    {
                        name: "Kebutuhan Pegawai",
                        type: "bar",
                        data: chartData.map((item) => item.kebutuhan_pegawai),
                        label: {
                            show: true,
                            position: "right",
                            formatter: (params) =>
                                params.value !== 0 ? params.value : "",
                        },
                    },
                ],
            };

            myChart.setOption(option);

            const resizeChart = () => {
                myChart.resize();
            };

            window.addEventListener("resize", resizeChart);

            return () => {
                window.removeEventListener("resize", resizeChart);
                myChart.dispose();
            };
        }
    }, [chartData]);

    return (
        <Card style={{ width: "100%", height: "100%" }}>
            <div
                ref={chartRef}
                style={{ width: "100%", height: "600px" }}
            ></div>
        </Card>
    );
};

export default BarChart;
