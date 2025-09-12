<!-- resources/views/reports/monthly.blade.php -->
@extends('layouts.app')

@section('title', 'Oylik Hisobot')
@section('page-title', 'Oylik Hisobot')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4>{{ \Carbon\Carbon::parse($month)->format('F Y') }} - Oylik Hisobot</h4>
            <form method="GET" class="d-flex">
                <input type="month" class="form-control me-2" name="month" value="{{ $month }}">
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
    <!-- Daily Chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Kunlik Daromad Grafigi</h5>
            </div>
            <div class="card-body">
                <canvas id="dailyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-crown"></i> Top Mijozlar</h5>
            </div>
            <div class="card-body">
                @forelse($topCustomers as $index => $customer)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }} me-2">{{ $index + 1 }}</span>
                        <div>
                            <strong>{{ $customer->name }}</strong>
                            <br><small class="text-muted">{{ $customer->phone }}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <strong class="text-success">{{ number_format($customer->total_spent) }}</strong>
                        <br><small class="text-muted">so'm</small>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">Ma'lumotlar topilmadi</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
const dailyData = @json($dailyData);
const ctx = document.getElementById('dailyChart').getContext('2d');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: dailyData.map(item => new Date(item.date).getDate()),
        datasets: [{
            label: 'Daromad (so\'m)',
            data: dailyData.map(item => item.revenue),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' so\'m';
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Daromad: ' + context.parsed.y.toLocaleString() + ' so\'m';
                    }
                }
            }
        }
    }
});
</script>
@endsection