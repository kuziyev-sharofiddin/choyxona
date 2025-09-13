<!-- resources/views/customers/show.blade.php -->
@extends('layouts.app')

@section('title', 'Mijoz Ma\'lumotlari')
@section('page-title', $customer->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user"></i> Mijoz Ma'lumotlari</h5>
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Tahrirlash
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                        </div>
                    </div>
                    <div class="col-md-10">
                        <table class="table table-borderless">
                            <tr>
                                <th>To'liq ism:</th>
                                <td>{{ $customer->name }}</td>
                            </tr>
                            <tr>
                                <th>Telefon:</th>
                                <td>{{ $customer->phone }}</td>
                            </tr>
                            @if($customer->email)
                            <tr>
                                <th>Email:</th>
                                <td>{{ $customer->email }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Ro'yxatdan o'tgan:</th>
                                <td>{{ $customer->created_at->format('d.m.Y') }}</td>
                            </tr>
                            @if($stats['last_visit'])
                            <tr>
                                <th>So'nggi tashrif:</th>
                                <td>{{ $stats['last_visit']->format('d.m.Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Reservations -->
        @if($customer->reservations->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> So'nggi Rezervatsiyalar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sana</th>
                                <th>Xona</th>
                                <th>Mehmonlar</th>
                                <th>Summa</th>
                                <th>Holat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->reservations->take(10) as $reservation)
                            <tr onclick="window.location.href='{{ route('reservations.show', $reservation) }}'" style="cursor: pointer;">
                                <td>{{ $reservation->reservation_date->format('d.m.Y H:i') }}</td>
                                <td>{{ $reservation->room->name_uz }}</td>
                                <td>{{ $reservation->guest_count }} kishi</td>
                                <td>{{ number_format($reservation->getTotalAmount()) }} so'm</td>
                                <td>
                                    @if($reservation->status === 'completed')
                                        <span class="badge bg-success">Tugallangan</span>
                                    @elseif($reservation->status === 'checked_in')
                                        <span class="badge bg-info">Faol</span>
                                    @else
                                        <span class="badge bg-warning">{{ ucfirst($reservation->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Favorite Products -->
        @if($stats['favorite_products']->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-heart"></i> Sevimli Mahsulotlar</h5>
            </div>
            <div class="card-body">
                @foreach($stats['favorite_products'] as $product)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ $product->name_uz }}</span>
                    <span class="badge bg-primary">{{ $product->total_quantity }} marta</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Statistics -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistika</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="text-primary">{{ $stats['total_visits'] }}</h3>
                    <p class="text-muted mb-0">Jami tashriflar</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-success">{{ number_format($stats['total_spent']) }}</h3>
                    <p class="text-muted mb-0">Jami xarajat (so'm)</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-warning">{{ number_format($stats['avg_spending']) }}</h3>
                    <p class="text-muted mb-0">O'rtacha xarajat (so'm)</p>
                </div>

                @if($stats['total_spent'] > 500000)
                <div class="text-center">
                    <span class="badge bg-warning" style="font-size: 1rem; padding: 10px;">
                        <i class="fas fa-crown"></i> VIP Mijoz
                    </span>
                </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('reservations.create', ['customer_id' => $customer->id]) }}" class="btn btn-success">
                        <i class="fas fa-calendar-plus"></i> Yangi Rezervatsiya
                    </a>
                    <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary">
                        <i class="fas fa-utensils"></i> Yangi Buyurtma
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection