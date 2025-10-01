<!-- resources/views/payments/index.blade.php -->
@extends('layouts.app')

@section('title', 'To\'lovlar')
@section('page-title', 'To\'lovlar Boshqaruvi')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Barcha To'lovlar</h4>
                <p class="text-muted">Jami: {{ $payments->total() }} ta to'lov</p>
            </div>
            <a href="{{ route('payments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi To'lov
            </a>
        </div>
    </div>
</div>

<!-- Filter Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Muvaffaqiyatli</h6>
                        <h4>{{ $payments->where('status', 'completed')->count() }}</h4>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Jarayonda</h6>
                        <h4>{{ $payments->where('status', 'pending')->count() }}</h4>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Bekor qilingan</h6>
                        <h4>{{ $payments->where('status', 'failed')->count() }}</h4>
                    </div>
                    <i class="fas fa-times-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> To'lovlar Ro'yxati</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>To'lov №</th>
                        <th>Mijoz</th>
                        <th>Tur</th>
                        <th>Summa</th>
                        <th>To'lov Usuli</th>
                        <th>Kassir</th>
                        <th>Sana</th>
                        <th>Holat</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>
                            <strong>{{ $payment->payment_number }}</strong>
                        </td>
                        <td>
                            <strong>{{ $payment->reservation->customer->name ?? $payment->order->customer->name }}</strong>
                            <br><small class="text-muted">{{ $payment->reservation->customer->phone ?? $payment->order->customer->phone }}</small>
                        </td>
                        @if ($payment->reservation)
                        <td>
                            <a href="{{ route('reservations.show', $payment->reservation) }}">
                                {{ $payment->reservation->reservation_number }}
                            </a>
                            <br><small class="text-muted">{{ $payment->reservation->room->name_uz }}</small>
                        </td>
                        @else
                        <td><a href="{{ route('payments.show', $payment->order) }}">
                        {{ $payment->order->order_number }}
                            </a></td>
                        @endif
                        <td>
                            <strong class="text-success">{{ number_format($payment->amount) }} so'm</strong>
                        </td>
                        <td>
                            @if($payment->payment_method === 'cash')
                                <span class="badge bg-success">Naqd</span>
                            @elseif($payment->payment_method === 'card')
                                <span class="badge bg-info">Karta</span>
                            @else
                                <span class="badge bg-warning">O'tkazma</span>
                            @endif
                        </td>
                        <td>{{ $payment->cashier->name }}</td>
                        <td>
                            {{ $payment->payment_time->format('d.m.Y H:i') }}
                        </td>
                        <td>
                            @if($payment->status === 'completed')
                                <span class="badge bg-secondary">Tugallangan</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge bg-warning">Jarayonda</span>
                            @else
                                <span class="badge bg-danger">Xato</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('payments.show', $payment) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($payment->status === 'pending')
                                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">To'lovlar topilmadi</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection