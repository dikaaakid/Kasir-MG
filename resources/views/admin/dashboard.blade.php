@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <!-- Kategori -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $kategori }}</h3>
                <p>Total Kategori</p>
            </div>
            <div class="icon">
                <i class="fa fa-cube"></i>
            </div>
            <a href="{{ route('kategori.index') }}" class="small-box-footer">
                Lihat <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Produk -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $produk }}</h3>
                <p>Total Produk</p>
            </div>
            <div class="icon">
                <i class="fa fa-cubes"></i>
            </div>
            <a href="{{ route('produk.index') }}" class="small-box-footer">
                Lihat <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Member -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $member }}</h3>
                <p>Total Member</p>
            </div>
            <div class="icon">
                <i class="fa fa-id-card"></i>
            </div>
            <a href="{{ route('member.index') }}" class="small-box-footer">
                Lihat <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Supplier -->
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>{{ $supplier }}</h3>
                <p>Total Supplier</p>
            </div>
            <div class="icon">
                <i class="fa fa-truck"></i>
            </div>
            <a href="{{ route('supplier.index') }}" class="small-box-footer">
                Lihat <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>
<!-- /.row -->

<!-- Tombol Transaksi Baru -->
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-body text-center">
                <a href="{{ route('transaksi.baru') }}" class="btn btn-success btn-lg btn-flat">
                    <i class="fa fa-shopping-cart"></i> Transaksi Baru
                </a>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->

<!-- Main row grafik pendapatan -->
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Grafik Pendapatan {{ isset($tanggal_awal) ? tanggal_indonesia($tanggal_awal, false) : '' }} s/d {{ isset($tanggal_akhir) ? tanggal_indonesia($tanggal_akhir, false) : '' }}</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="chart">
                            <canvas id="salesChart" style="height: 180px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.row -->
@endsection

@push('scripts')
<!-- ChartJS -->
<script src="{{ asset('AdminLTE-2/bower_components/chart.js/Chart.js') }}"></script>
<script>
$(function() {
    // Check if the chart element exists
    if ($('#salesChart').length === 0) {
        console.error("Sales chart canvas element not found");
        return;
    }

    // Get the canvas element
    var salesChartCanvas = $('#salesChart').get(0).getContext('2d');

    // Define data with fallbacks for empty arrays
    var labels = {!! json_encode($data_tanggal ?? []) !!};
    var pendapatan = {!! json_encode($data_pendapatan ?? []) !!};

    // Check if data is available
    if (!labels.length || !pendapatan.length) {
        console.warn("No data available for chart");
        // Draw empty chart or message
        return;
    }

    var salesChartData = {
        labels: labels,
        datasets: [{
            label: 'Pendapatan',
            fillColor: 'rgba(60,141,188,0.9)',
            strokeColor: 'rgba(60,141,188,0.8)',
            pointColor: '#3b8bba',
            pointStrokeColor: 'rgba(60,141,188,1)',
            pointHighlightFill: '#fff',
            pointHighlightStroke: 'rgba(60,141,188,1)',
            data: pendapatan
        }]
    };

    var salesChartOptions = {
        pointDot: false,
        responsive: true
    };

    // Create chart
    try {
        var salesChart = new Chart(salesChartCanvas);
        salesChart.Line(salesChartData, salesChartOptions);
    } catch (e) {
        console.error("Error creating chart:", e);
    }
});
</script>
@endpush
