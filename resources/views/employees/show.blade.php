<!-- resources/views/employees/show.blade.php -->
@extends('layouts.app')

@section('title', 'Xodim Ma\'lumotlari')
@section('page-title', $employee->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user"></i> Xodim Ma'lumotlari</h5>
                <div>
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    <form action="{{ route('employees.toggle', $employee) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-{{ $employee->is_active ? 'warning' : 'success' }} btn-sm">
                            <i class="fas fa-{{ $employee->is_active ? 'pause' : 'play' }}"></i>
                            {{ $employee->is_active ? 'Nofaol qilish' : 'Faollashtirish' }}
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                        </div>
                    </div>
                    <div class="col-md-10">
                        <table class="table table-borderless">
                            <tr>
                                <th>To'liq ism:</th>
                                <td>{{ $employee->name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <th>Telefon:</th>
                                <td>{{ $employee->phone }}</td>
                            </tr>
                            <tr>
                                <th>Lavozim:</th>
                                <td><span class="badge bg-info">{{ $employee->role->display_name }}</span></td>
                            </tr>
                            <tr>
                                <th>Ish haqi:</th>
                                <td><strong class="text-success">{{ number_format($employee->salary) }} so'm</strong></td>
                            </tr>
                            <tr>
                                <th>Ish boshlagan:</th>
                                <td>{{ $employee->hire_date ? $employee->hire_date->format('d.m.Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Ish staji:</th>
                                <td>{{ $stats['work_days'] }} kun</td>
                            </tr>
                            <tr>
                                <th>Holat:</th>
                                <td>
                                    <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                        {{ $employee->is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        @if($recentOrders->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> So'nggi Buyurtmalar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sana</th>
                                <th>Buyurtma №</th>
                                <th>Mijoz</th>
                                <th>Xona</th>
                                <th>Summa</th>
                                <th>Holat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr onclick="window.location.href='{{ route('orders.show', $order) }}'" style="cursor: pointer;">
                                <td>{{ $order->order_time->format('d.m.Y H:i') }}</td>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer->name }}</td>
                                <td>{{ $order->reservation->room->name_uz }}</td>
                                <td>{{ number_format($order->total_amount) }} so'm</td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'info' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                    <h3 class="text-primary">{{ $stats['total_orders'] }}</h3>
                    <p class="text-muted mb-0">Jami buyurtmalar</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-success">{{ number_format($stats['total_sales']) }}</h3>
                    <p class="text-muted mb-0">Jami sotuv (so'm)</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-warning">{{ number_format($stats['avg_order_value']) }}</h3>
                    <p class="text-muted mb-0">O'rtacha buyurtma (so'm)</p>
                </div>
                
                <div class="text-center">
                    <h3 class="text-info">{{ number_format($stats['work_days'] / 30, 1) }}</h3>
                    <p class="text-muted mb-0">Ish oylari</p>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-trophy"></i> Yutuqlar</h5>
            </div>
            <div class="card-body">
                @if($stats['total_orders'] > 100)
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-medal text-warning me-2"></i>
                    <span>100+ buyurtma</span>
                </div>
                @endif
                
                @if($stats['total_sales'] > 1000000)
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-star text-success me-2"></i>
                    <span>1 mln+ sotuv</span>
                </div>
                @endif
                
                @if($stats['work_days'] > 365)
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-clock text-info me-2"></i>
                    <span>1+ yil tajriba</span>
                </div>
                @endif

                @if($stats['total_orders'] == 0)
                <p class="text-muted text-center">Hali yutuqlar yo'q</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection