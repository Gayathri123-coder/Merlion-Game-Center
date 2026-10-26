@extends('app')

@section('title', 'Dashboard')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <h1>Dashboard</h1>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daily Player Cost</h3>
                        <div class="card-tools text-muted">
                            {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="text-muted">Total player cost for this period</div>
                                <div class="h3 mb-0">{{ number_format($periodTotal, 2) }}</div>
                            </div>
                        </div>
                        <div style="height: 350px;">
                            <canvas id="daily-player-cost-chart" role="img" aria-label="Daily total player cost over the last 30 days"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
    $(function () {
        var chartElement = document.getElementById('daily-player-cost-chart');
        if (!chartElement) {
            return;
        }

        new Chart(chartElement.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Total player cost',
                    data: @json($costs),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.12)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#007bff',
                    fill: true,
                    lineTension: 0.25
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: true
                },
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Date'
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Total cost'
                        }
                    }]
                }
            }
        });
    });
</script>
@endsection
