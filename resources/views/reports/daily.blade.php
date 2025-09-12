<!-- resources/views/reports/daily.blade.php -->
@extends('layouts.app')

@section('title', 'Kunlik Hisobot')
@section('page-title', 'Kunlik Hisobot')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4>{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }} - Kunlik Hisobot</h4>
            <form method="GET" class="d-flex">
                <input type="date" class="form-control me-2" name="date" value="{{ $date }}">
                <button type="submit" class="btn btn-primary">Ko'rish</button>
            </form>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Daromad</h6>
                        <h4>{{ number_format($revenue) }} so'm</h4>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Buyurtmalar</h6>
                        <h4>{{ $orders }}</h4>
                    </div>
                    <i class="fas fa-utensils fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Rezervatsiyalar</h6>
                        <h4>{{ $reservations }}</h4>
                    </div>
                    <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Mijozlar</h6>
                        <h4>{{ $customers }}</h4>
                    </div>
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Products -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-trophy"></i> Eng Ko'p Sotilgan Mahsulotlar</h5>
            </div>
            <div class="card-body">
                @forelse($topProducts as $product)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>{{ $product->name_uz }}</strong>
                        <br><small class="text-muted">{{ $product->category->name_uz }}</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">{{ $product->total_quantity }} dona</span>
                        <br><small class="text-success">{{ number_format($product->total_quantity * $product->price) }} so'm</small>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">Ma'lumotlar topilmadi</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Hourly Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Soatlik Statistika</h5>
            </div>
            <div class="card-body">
                <canvas id="hourlyChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
const hourlyData = @json($hourlyData);
const ctx = document.getElementById('hourlyChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: hourlyData.map(item => item.hour + ':00'),
        datasets: [{
            label: 'Buyurtmalar',
            data: hourlyData.map(item => item.orders_count),
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }, {
            label: 'Daromad (ming so\'m)',
            data: hourlyData.map(item => item.revenue / 1000),
            borderColor: 'rgb(255, 99, 132)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endsection