<!-- resources/views/reports/index.blade.php -->
@extends('layouts.app')

@section('title', 'Hisobotlar')
@section('page-title', 'Hisobotlar va Statistika')

@section('content')
<div class="row">
    <!-- Quick Stats -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Tez Statistika</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-success">{{ number_format($todayRevenue) }}</h3>
                            <p class="text-muted mb-0">Bugungi Daromad (so'm)</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-info">{{ $todayOrders }}</h3>
                            <p class="text-muted mb-0">Bugungi Buyurtmalar</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-warning">{{ number_format($thisMonthRevenue) }}</h3>
                            <p class="text-muted mb-0">Oylik Daromad (so'm)</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h3 class="text-primary">{{ $totalCustomers }}</h3>
                        <p class="text-muted mb-0">Jami Mijozlar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-calendar-day fa-3x text-success mb-3"></i>
                <h5 class="card-title">Kunlik Hisobot</h5>
                <p class="card-text">Kunlik daromad, buyurtmalar va mijozlar statistikasi</p>
                <a href="{{ route('reports.daily') }}" class="btn btn-success">
                    <i class="fas fa-eye"></i> Ko'rish
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
                <h5 class="card-title">Oylik Hisobot</h5>
                <p class="card-text">Oylik daromad dinamikasi va tahlili</p>
                <a href="{{ route('reports.monthly') }}" class="btn btn-info">
                    <i class="fas fa-eye"></i> Ko'rish
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-box fa-3x text-warning mb-3"></i>
                <h5 class="card-title">Mahsulotlar Hisoboti</h5>
                <p class="card-text">Eng ko'p sotilgan mahsulotlar va kategoriyalar</p>
                <a href="{{ route('reports.products') }}" class="btn btn-warning">
                    <i class="fas fa-eye"></i> Ko'rish
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Xodimlar Hisoboti</h5>
                <p class="card-text">Xodimlar faoliyati va samaradorlik ko'rsatkichlari</p>
                <a href="{{ route('reports.employees') }}" class="btn btn-primary">
                    <i class="fas fa-eye"></i> Ko'rish
                </a>
            </div>
        </div>
    </div>

    <!-- Custom Report Builder -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tools"></i> Maxsus Hisobot Yaratish</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.custom') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Hisobot Turi</label>
                                <select class="form-control" name="type" required>
                                    <option value="">Tanlang</option>
                                    <option value="revenue">Daromad</option>
                                    <option value="orders">Buyurtmalar</option>
                                    <option value="products">Mahsulotlar</option>
                                    <option value="customers">Mijozlar</option>
                                    <option value="rooms">Xonalar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Boshlanish Sanasi</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Tugash Sanasi</label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Hisobot Yaratish
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection