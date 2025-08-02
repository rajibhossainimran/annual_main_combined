@extends('admin.master')
@push('css')
@endpush
@section('content')
    <div class="app-main__outer">
        <div class="app-main__inner ">
            <div class="col-lg-12 app-content">
                <div class="app-content-top-title">Dashboard</div>
                <div class="tabs-animation app-content-inner">
                    {{-- <div class="d-flex justify-content-between align-items-center table-header-bg" style="padding: 7px;margin-top: 5px;margin-bottom: 7px">
                <h5 class="f-14">Dashboard</h5>
            </div> --}}
                    <div class="row app-content-inner">
                        <div class="col-md-3 col-xl-3 col-padding-reomve-right border-right ">
                            <div
                                class="bg-1 card widget-chart widget-chart2 text-left card-btm-border card-shadow-success border-success">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="row">
                                        <div class="col-md-8 col-xl-8 col-padding-reomve-right">
                                            Total Work Order <br>
                                            {{-- <span class="wed-color">Approved @if ($totalWorkOrder > 0)
                                        <?php

                                        $avv = ($appWorkorder * 100) / $totalWorkOrder;
                                        echo number_format($avv, 0, '.');
                                        ?>

                                        @endif% </span> --}}
                                        </div>
                                        <div class="col-md-4 col-xl-4 col-padding-reomve-left">
                                            <p><span class="span-float-right font-size-22">{{ $totalWorkOrder }}
                                                    &nbsp;</span></p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xl-3 col-padding-remove border-right">
                            <div
                                class="bg-2 card widget-chart widget-chart2 text-left card-btm-border card-shadow-success border-success">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="row">
                                        <div class="col-md-8 col-xl-8 col-padding-reomve-right">
                                            Total Tender <br>
                                            {{-- <span class="wed-color">Approved % </span> --}}
                                        </div>
                                        <div class="col-md-4 col-xl-4 col-padding-reomve-left">
                                            <p><span class="span-float-right font-size-22">{{ $tender }}&nbsp;</span>
                                            </p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xl-3 col-padding-remove border-right">
                            <div
                                class="bg-3 card widget-chart widget-chart2 text-left card-btm-border card-shadow-success border-success">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="row">
                                        <div class="col-md-8 col-xl-8 col-padding-reomve-right">
                                            Total CSR <br>
                                            {{-- <span class="wed-color">Approved @if ($totalDemand > 0)
                                    <?php
                                    $avv = (($totalDemand - $DemandPendding) * 100) / $totalDemand;

                                    echo number_format($avv, 0, '.');
                                    ?>
                                    @endif% </span> --}}
                                        </div>
                                        <div class="col-md-4 col-xl-4 col-padding-reomve-left">
                                            <p><span class="span-float-right font-size-22"> {{ $csr }}
                                                    &nbsp;</span></p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xl-3 col-padding-remove">
                            <div
                                class="bg-4 card widget-chart widget-chart2 text-left card-btm-border card-shadow-success border-success">
                                <div class="widget-chat-wrapper-outer">
                                    <div class="row">
                                        <div class="col-md-9 col-xl-9 col-padding-reomve-right">
                                            Issue Pending <br>
                                            {{-- <span class="wed-color">Approved @if ($totalIssue > 0)
                                        <?php
                                        $avv = ($issuePending * 100) / $totalIssue;
                                        echo number_format($avv, 0, '.');
                                        ?>
                                        @endif% </span> --}}
                                        </div>
                                        <div class="col-md-3 col-xl-3 col-padding-reomve-left">
                                            <p><span class="span-float-right font-size-22"> {{ $issuePending }}
                                                    &nbsp;</span></p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row app-content-inner">
                        <div class="col-md-6">
                            <div class="main-card card">
                                <div class="card-body">
                                    <h5 class="card-title text-center bg2"> Low Stock </h5>
                                    <div id="chart-apex-area"></div>

                                    {{-- <img class="demo-img" src="{{asset('admin/img/1.1.jpg')}}"> --}}
                                </div>
                            </div>
                            <div class="main-card mb-3 card">
                                <div class="card-body">
                                    {{-- <h5 class="card-title">Collection Report</h5> --}}
                                    <div id="chart2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="main-card  card">
                                <div class="card-body">
                                    <h5 class="card-title text-center bg2">MEDICINE EXPIRE within 30 days</h5>
                                    <div id="chart6"></div>
                                    {{-- <img class="demo-img" src="{{asset('admin/img/1.2.jpg')}}"> --}}
                                </div>
                            </div>
                            {{-- <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">CMH wise LP items</h5>
                            <div id="chart-apex-stacked"></div>
                        </div>
                    </div> --}}
                        </div>
                        <div class="col-md-6">
                            <div class="main-card  card">
                                <div class="card-body">
                                    <h5 class="card-title text-center bg">Tender Fee Collection </h5>
                                    <div id="chart"></div>
                                    {{-- <img class="demo-img" src="{{asset('admin/img/1.3.jpg')}}"> --}}
                                </div>
                            </div>
                            {{-- <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">CMH wise LP items</h5>
                            <div id="chart5"></div>
                        </div>
                    </div> --}}
                        </div>
                        <div class="col-md-6">
                            <div class="main-card  card">
                                <div class="card-body">
                                    <h5 class="card-title text-center bg">CMH LP items</h5>
                                    <div id="chartLast"></div>
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
        var options = {
            series: [{
                name: 'Tender Fee',
                data: [
                    @foreach ($totalAmount as $t)
                        @if ($t->amount > 0)
                            {{ floor($t->amount) }}
                        @else
                            0
                        @endif ,
                    @endforeach
                ]
            }, {
                name: 'SSL Fee',
                data: [
                    @foreach ($sslAmount as $t)
                        @if ($t->ssl_fee > 0)
                            {{ floor($t->ssl_fee) }}
                        @else
                            0
                        @endif ,
                    @endforeach
                ]
            }, {
                name: 'DGMS Fee',
                data: [
                    @foreach ($dgmsAmount as $t)
                        @if ($t->dgms_fee > 0)
                            {{ floor($t->dgms_fee) }}
                        @else
                            0
                        @endif ,
                    @endforeach
                ]
            }],
            chart: {
                type: 'bar',
                height: 365
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: [
                    @foreach ($totalAmount as $t)
                        <?php $monthName = date('M', mktime(0, 0, 0, $t->month, 10));
                        echo '"' . $monthName . '"'; ?>,
                    @endforeach
                ],
            },
            yaxis: {
                title: {
                    text: 'BDT'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "" + val + " BDT"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
        ///

        var options = {
            series: [{
                data: [
                    @foreach ($low_stock as $s)
                        {{ $s->stock }},
                    @endforeach
                ]
            }],
            chart: {
                type: 'bar',
                height: 365
            },
            plotOptions: {
                bar: {
                    barHeight: '100%',
                    distributed: true,
                    horizontal: true,
                    dataLabels: {
                        position: 'bottom'
                    },
                }
            },
            colors: ['#33b2df', '#546E7A', '#d4526e', '#13d8aa', '#A5978B', '#2b908f', '#f9a3a4', '#90ee7e',
                '#f48024', '#69d2e7'
            ],
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#0e0e0e'],
                    fontWeight: ['400']
                },
                formatter: function(val, opt) {
                    return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val
                },
                offsetX: 0,
                dropShadow: {
                    enabled: true
                }
            },
            stroke: {
                width: 1,
                colors: ['#eee']
            },
            xaxis: {
                categories: [
                    @foreach ($low_stock as $s)
                        <?php echo '"' . $s->name . '"'; ?>,
                    @endforeach
                ],
            },
            yaxis: {
                labels: {
                    show: false,
                    floating: true
                }
            },
            subtitle: {
                text: 'PVMS(nomenclature)',
                align: 'center',
            },
            tooltip: {
                theme: 'dark',
                x: {
                    show: false
                },
                y: {
                    title: {
                        formatter: function() {
                            return ''
                        }
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);
        chart.render();
        /////

        var options = {
            series: [
                @foreach ($lpCountCMH as $d)
                    {{ $d->t }},
                @endforeach
            ],
            chart: {
                //   width: 380,
                height: 365,
                type: 'pie',
            },
            labels: [
                @foreach ($lpCountCMH as $d)
                    <?php echo '"' . $d->name . '"'; ?>,
                @endforeach
            ],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200,
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#chartLast"), options);
        chart.render();

        /////

        var options = {
            series: [{
                data: [
                    @foreach ($expires as $ex)
                        {{ $ex->stock }},
                    @endforeach
                ]
            }],
            chart: {
                type: 'bar',
                height: 365
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    horizontal: true,
                }
            },
            colors: ['#d4526e'],
            dataLabels: {
                enabled: true,
            },
            xaxis: {
                categories: [
                    @foreach ($expires as $s)
                        <?php echo '"' . $s->nomenclature . '"'; ?>,
                    @endforeach
                ],
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart6"), options);
        chart.render();


        /////
        var options = {
            series: [42, 47, 52, 58, 65],
            chart: {
                width: 380,
                type: 'polarArea',
                height: 400,
            },
            labels: ['Pentazocine', 'Tenoxicam', 'Cap Piroxicam', 'Ketorolac', 'Naproxen gel'],

            fill: {
                opacity: 1,
            },
            stroke: {
                width: 1,
                colors: undefined
            },
            yaxis: {
                show: false
            },
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                polarArea: {
                    rings: {
                        strokeWidth: 0
                    },
                    spokes: {
                        strokeWidth: 0
                    },
                }
            },
            theme: {
                monochrome: {
                    enabled: true,
                    shadeTo: 'light',
                    shadeIntensity: 0.6
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart4"), options);
        chart.render();
        /////
        var options = {
            series: [{
                name: 'Demand',
                data: [31, 40, 28, 51, 42, 109, 100]
            }, {
                name: 'CSR',
                data: [11, 32, 45, 32, 34, 52, 41]
            }],
            chart: {
                height: 365,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z",
                    "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z",
                    "2018-09-19T06:30:00.000Z"
                ]
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart5"), options);
        chart.render();
    </script>
@endpush
