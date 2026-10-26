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
                        <div class="card-tools text-muted">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</div>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                        <form method="GET" action="{{ route('home') }}" class="row align-items-end mb-4">
                            <div class="col-md-3 mb-2">
                                <label for="from_date">From date</label>
                                <input type="date" id="from_date" name="from_date" class="form-control"
                                       value="{{ old('from_date', $startDate->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="to_date">To date</label>
                                <input type="date" id="to_date" name="to_date" class="form-control"
                                       value="{{ old('to_date', $endDate->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="console_id">Console</label>
                                <select id="console_id" name="console_id" class="form-control">
                                    <option value="">All consoles</option>
                                    @foreach ($consoleList as $console)
                                        <option value="{{ $console->id }}"
                                            {{ (string) old('console_id', $selectedConsoleId) === (string) $console->id ? 'selected' : '' }}>
                                            {{ $console->console_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button type="submit" class="btn btn-primary">Apply filters</button>
                                <a href="{{ route('home') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="text-muted">Total player cost for this period</div>
                                <div class="h3 mb-0">{{ number_format($periodTotal, 2) }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted">Food and beverages cost for this period</div>
                                <div class="h3 mb-0">{{ number_format($foodAndBeveragePeriodTotal, 2) }}</div>
                            </div>
                        </div>
                        <div style="height: 350px;">
                            <canvas id="daily-player-cost-chart" role="img" aria-label="Daily total player cost for the selected date range"></canvas>
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
                }, {
                    label: 'Food and beverages cost',
                    data: @json($foodAndBeverageCosts),
                    borderColor: '#fd7e14',
                    backgroundColor: 'rgba(253, 126, 20, 0.08)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#fd7e14',
                    fill: false,
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
