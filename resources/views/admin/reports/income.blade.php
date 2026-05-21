@extends('layouts.app')

@section('title', __('Income Report'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark">{{ __('Financial Income & Analytics Report') }}</h2>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.location.href='{{ route('reports.income.pdf', request()->all()) }}'" class="btn btn-outline-danger d-flex align-items-center gap-2">
            <i data-lucide="file-text" style="width: 18px;"></i>
            <span>{{ __('Export PDF') }}</span>
        </button>
        <button onclick="window.location.href='{{ route('reports.income.excel', request()->all()) }}'" class="btn btn-outline-success d-flex align-items-center gap-2">
            <i data-lucide="file-spreadsheet" style="width: 18px;"></i>
            <span>{{ __('Export Excel') }}</span>
        </button>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form id="filter-form" action="{{ route('reports.income') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-12">
                <label class="form-label small fw-bold">{{ __('Date Range') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i data-lucide="calendar" style="width: 16px;" class="text-muted"></i></span>
                    <input type="text" id="reportrange" class="form-control" style="cursor: pointer;" readonly>
                </div>
                <input type="hidden" name="start_date" id="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" id="end_date" value="{{ $endDate }}">
            </div>
        </form>
    </div>
</div>

<div id="report-content" style="transition: opacity 0.3s ease;">
    @include('admin.reports.partials.income_content')
</div>

@push('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function() {
        var start = moment('{{ $startDate }}');
        var end = moment('{{ $endDate }}');

        function cb(start, end) {
            $('#reportrange').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

        // Fetch report on date change
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            fetchReport();
        });
    });

    let chartInstance = null;

    function initChart(labels, data) {
        if (chartInstance) {
            chartInstance.destroy();
        }
        const ctx = document.getElementById('incomeChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.2)'); // Primary color
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: "{{ __('Gross Income') }}",
                    data: data,
                    borderColor: '#0d6efd',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.45,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#0d6efd',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return " {{ __('Sales') }}: $" + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5],
                            color: '#e2e8f0'
                        },
                        ticks: {
                            callback: value => '$' + value.toLocaleString()
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function fetchReport(url = null) {
        var fetchUrl = url || ('{{ route("reports.income") }}?' + $('#filter-form').serialize());

        $('#report-content').css('opacity', '0.5');

        $.ajax({
            url: fetchUrl,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#report-content').html(res.html);
                initChart(res.chart.labels, res.chart.data);
                $('#report-content').css('opacity', '1');
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            },
            error: function() {
                $('#report-content').css('opacity', '1');
                alert('{{ __("Failed to fetch report data. Please try again.") }}');
            }
        });
    }

    // Initial load
    initChart(@js($mainTrend->pluck('label')), @js($mainTrend->pluck('total')));

    // Hijack pagination links
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        fetchReport($(this).attr('href'));
    });
</script>
@endpush
@endsection