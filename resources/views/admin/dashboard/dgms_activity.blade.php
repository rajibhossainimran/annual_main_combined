@extends('admin.master')

@push('css')
@endpush

@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner ">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">DGMS Activity</div>
                <div class="tabs-animation app-content-inner">
                    <div class="row app-content-inner">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="main-card card">
                                        <div class="card-body">
                                            <h5 class="card-title text-center bg">Demand</h5>
                                            <div id="demand-chart"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="main-card card">
                                        <div class="card-body">
                                            <h5 class="card-title text-center bg">Notesheet</h5>
                                            <div id="notesheet-chart"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-4">
                                    <div class="main-card card">
                                        <div class="card-body">
                                            <h5 class="card-title text-center bg">Tender</h5>
                                            <div id="tender-chart"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pt-4">
                                    <div class="main-card card">
                                        <div class="card-body">
                                            <h5 class="card-title text-center bg">CSR</h5>
                                            <div id="chartLast"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="main-card card">
                                <div class="card-body">
                                    <h5 class="card-title text-center bg">Pending</h5>
                                    <div id="demands_not_in_notesheet_data" class="p-3">
                                        @foreach ($demands_not_in_notesheet_data as $item)
                                            <span class="f16">{{ $item }}</span><br><br>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('admin/scripts/apexchart.js') }}"></script>
    <script nonce="all">
        // Demand Chart (Vertical Bar Chart with Different Colors)
        var options = {
            series: [{
                name: 'Demand',
                data: [
                    {{ $dgmsClerkCount }},
                    {{ $dgmsG2CCCount }},
                    {{ $dgmsG2PPCount }},
                    {{ $dgmsG2EquipmentCount }},
                    {{ $dgmsG1Count }},
                    {{ $dgmsDyDGMSCount }}
                ]
            }],
            chart: {
                type: 'bar',
                height: 365
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded',
                    distributed: true
                },
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '45px',
                    fontWeight: 'bold',
                    colors: ['#000']
                }
            },
            xaxis: {
                categories: ["Clerk", "G2 (CC)", "G2 (PP)", "G2 (Equip)", "G1", "DyDGMS"],
                labels: {
                    style: {
                        fontSize: '22px', // Set font size for the x-axis labels
                        colors: ['#000'], // Optional: change color if needed
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Count'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Records";
                    }
                }
            },
            legend: {
                show: false
            }
        };

        var chart = new ApexCharts(document.querySelector("#demand-chart"), options);
        chart.render();

        // Notesheet Chart
        var optionsNotesheet = {
            series: [{
                name: 'Notesheet',
                data: [
                    {{ $notesheetClerkCount }},
                    {{ $notesheetG2CCCount }},
                    {{ $notesheetG2PPCount }},
                    {{ $notesheetG2EquipmentCount }},
                    {{ $notesheetG1Count }},
                    {{ $notesheetDyDGMSCount }},
                    {{ $notesheetConsPhyGenCount }},
                    {{ $notesheetConsSurGenCount }},
                    {{ $notesheetDgmsCount }}
                ]
            }],
            chart: {
                type: 'bar',
                height: 365
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded',
                    distributed: true
                },
            },
            colors: [
                '#e3d36b',
                '#9c27b0',
                '#ff9800',
                '#2196f3',
                '#4caf50',
                '#f44336',
                '#ff5722',
                '#673ab7',
                '#607d8b'
            ],
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '45px',
                    fontWeight: 'bold',
                    colors: ['#000']
                }
            },
            xaxis: {
                categories: ["Clerk", "G2 (CC)", "G2 (PP)", "G2 (Equip)", "G1", "DyDGMS", "Cons Phy Gen",
                    "Cons Sur Gen", "DGMS"
                ],
                labels: {
                    style: {
                        fontSize: '22px', // Set font size for the x-axis labels
                        colors: ['#000'], // Optional: change color if needed
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Count'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Records";
                    }
                }
            },
            legend: {
                show: false,
            }
        };

        var notesheetChart = new ApexCharts(document.querySelector("#notesheet-chart"), optionsNotesheet);
        notesheetChart.render();

        // Tender Pie Chart
        var optionsTender = {
            series: [
                {{ $runningCount }},
                {{ $crsCount }},
                {{ $finishedCount }}
            ],
            chart: {
                type: 'pie',
                height: 425,
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 10,
                    left: 0,
                    blur: 10,
                    opacity: 0.25
                }
            },
            labels: ["Running", "CSR Stage", "Completed"],
            colors: ['#FF0000', '#0000FF', '#008000'],
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '30px',
                    fontWeight: 'bold',
                    colors: ['#000']
                },
                dropShadow: {
                    enabled: true,
                    top: 1,
                    left: 1,
                    blur: 1,
                    opacity: 0.45
                },
                formatter: function(val, opts) {
                    return opts.w.globals.series[opts.seriesIndex];
                },
                background: {
                    enabled: true,
                    borderRadius: 5, // Rounded corners for the background
                    padding: 8, // Padding inside the label
                    opacity: 0.8, // Background opacity
                    borderWidth: 2, // Border width around label
                    borderColor: '#fff', // Border color
                    color: function(seriesIndex) {
                        // Assign background color based on the slice color
                        var colors = ['#FF0000', '#0000FF', '#008000'];
                        return colors[seriesIndex];
                    }
                }
            },
            fill: {
                type: 'gradient',
            },
            legend: {
                position: 'bottom',
                fontSize: '22px',
                labels: {
                    colors: ['#333']
                },
                markers: {
                    radius: 12
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Records";
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var tenderChart = new ApexCharts(document.querySelector("#tender-chart"), optionsTender);
        tenderChart.render();

        // CSR Chart
        var optionsCSR = {
            series: [{
                name: 'CSR',
                data: [
                    {{ $csrG2CCCount }},
                    {{ $csrG2PPCount }},
                    {{ $csrG2EquipmentCount }},
                    {{ $csrG1Count }},
                    {{ $csrHodCount }},
                    {{ $csrDyDGMSCount }},
                    {{ $csrConsPhyGenCount }},
                    {{ $csrConsSurGenCount }},
                ]
            }],
            chart: {
                type: 'bar',
                height: 365
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded',
                    distributed: true
                },
            },
            colors: [
                '#4A90E2', // Soft Blue
                '#50E3C2', // Aqua Green
                '#B8E986', // Light Green
                '#F5A623', // Amber
                '#F8E71C', // Yellow
                '#546E7A',
                '#7ED321', // Lime Green
                '#417505' // Dark Green
            ],
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '45px',
                    fontWeight: 'bold',
                    colors: ['#000']
                }
            },
            xaxis: {
                categories: ["G2 (CC)", "G2 (PP)", "G2 (Equip)", "G1", "HOD", "DyDGMS", "Cons Phy Gen",
                    "Cons Sur Gen"
                ],
                labels: {
                    style: {
                        fontSize: '22px', // Set font size for the x-axis labels
                        colors: ['#000'], // Optional: change color if needed
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Count'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Records";
                    }
                }
            },
            legend: {
                show: false
            }
        };

        var csrChart = new ApexCharts(document.querySelector("#chartLast"), optionsCSR);
        csrChart.render();
    </script>
@endpush
