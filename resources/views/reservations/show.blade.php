@extends('layouts.app')

@section('title', 'Rezervatsiya #' . $reservation->reservation_number)
@section('page-title', 'Rezervatsiya Tafsilotlari')

@section('content')
<div class="row">
    <!-- Header Section -->
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Rezervatsiya #{{ $reservation->reservation_number }}</h4>
                <div class="mt-2">
                    @switch($reservation->status)
                        @case('confirmed')
                            <span class="badge bg-success fs-6">Tasdiqlangan</span>
                            @break
                        @case('checked_in')
                            <span class="badge bg-info fs-6">Keldi</span>
                            @break
                        @case('completed')
                            <span class="badge bg-secondary fs-6">Tugallangan</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-danger fs-6">Bekor qilingan</span>
                            @break
                    @endswitch
                    
                    @if($reservation->is_active)
                        <span class="badge bg-warning fs-6 ms-2">Hozir Aktiv</span>
                    @endif
                    
                    @if($reservation->is_expired)
                        <span class="badge bg-dark fs-6 ms-2">Muddati O'tgan</span>
                    @endif
                </div>
                <p class="text-muted mb-0 mt-1">
                    Yaratilgan: {{ $reservation->created_at->format('d.m.Y H:i') }} 
                    ({{ $reservation->user->name }} tomonidan)
                </p>
            </div>
            <div>
                <div class="btn-group">
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                    @if(!$reservation->is_expired && $reservation->status !== 'completed')
                    <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    @endif
                    @if($reservation->status === 'confirmed')
                    <form action="{{ route('reservations.checkin', $reservation) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-user-check"></i> Keldi
                        </button>
                    </form>
                    @endif
                    @if($reservation->status === 'checked_in')
                    <form action="{{ route('reservations.complete', $reservation) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-flag-checkered"></i> Tugallash
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Customer & Reservation Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Asosiy Ma'lumotlar</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Customer Info -->
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Mijoz Ma'lumotlari</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold" width="40%">Ism:</td>
                                <td>{{ $reservation->customer->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Telefon:</td>
                                <td>
                                    <a href="tel:{{ $reservation->customer->phone }}">
                                        {{ $reservation->customer->phone }}
                                    </a>
                                </td>
                            </tr>
                            @if($reservation->customer->email)
                            <tr>
                                <td class="fw-bold">Email:</td>
                                <td>
                                    <a href="mailto:{{ $reservation->customer->email }}">
                                        {{ $reservation->customer->email }}
                                    </a>
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-bold">Mehmonlar:</td>
                                <td>
                                    <span class="badge bg-primary">{{ $reservation->guest_count }} kishi</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Reservation Info -->
                    <div class="col-md-6">
                        <h6 class="border-bottom pb-2 mb-3">Rezervatsiya Ma'lumotlari</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold" width="40%">Xona:</td>
                                <td>
                                    <span class="badge bg-info">{{ $reservation->room->name_uz }}</span>
                                    <br><small class="text-muted">{{ $reservation->room->capacity }} kishilik</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Boshlanish:</td>
                                <td>
                                    {{ $reservation->reservation_date->format('d.m.Y') }}
                                    <br><small class="text-muted">{{ $reservation->reservation_date->format('l') }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tugash:</td>
                                <td>
                                    {{ $reservation->end_date->format('d.m.Y') }}
                                    <br><small class="text-muted">{{ $reservation->end_date->format('l') }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Davomiyligi:</td>
                                <td>
                                    <span class="badge bg-success">{{ $reservation->days_count }} kun</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Kunlik narx:</td>
                                <td>{{ number_format($reservation->room->daily_rate) }} so'm</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Mas'ul:</td>
                                <td>{{ $reservation->user->name }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($reservation->special_requests)
                <div class="mt-3">
                    <h6 class="border-bottom pb-2 mb-3">Maxsus So'rovlar</h6>
                    <div class="alert alert-info">
                        <i class="fas fa-comment"></i>
                        {{ $reservation->special_requests }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Room Amenities -->
        @if($reservation->room->amenities && count($reservation->room->amenities) > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-star"></i> Xona Imkoniyatlari</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($reservation->room->amenities as $amenity)
                    <div class="col-md-3 col-sm-6 mb-2">
                        <span class="badge bg-light text-dark border w-100 py-2">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            {{ $amenity }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Orders Section -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-utensils"></i> Buyurtmalar</h5>
                @if($reservation->status === 'confirmed' || $reservation->status === 'checked_in')
                <a href="{{ route('orders.create', ['reservation_id' => $reservation->id]) }}" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Yangi Buyurtma
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($reservation->orders->count() > 0)
                @foreach($reservation->orders as $order)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Buyurtma #{{ $order->order_number ?? $order->id }}</h6>
                        <div>
                            <span class="badge bg-{{ $order->status === 'completed' ? 'secondary' : 'warning' }}">
                                @switch($order->status)
                                    @case('pending') Kutilmoqda @break
                                    @default Tugallangan
                                @endswitch
                            </span>
                            <small class="text-muted ms-2">{{ $order->created_at->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mahsulot</th>
                                    <th>Miqdor</th>
                                    <th>Narx</th>
                                    <th>Jami</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($order->items))
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product->name_uz ?? $item->product->name }}
                                            @if($item->special_instructions)
                                                <br><small class="text-muted">{{ $item->special_instructions }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price) }}</td>
                                        <td>{{ number_format($item->total_price) }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Jami:</th>
                                    <th>{{ number_format($order->total_amount) }} so'm</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-4">
                    <i class="fas fa-utensils fa-2x text-muted mb-3"></i>
                    <h6 class="text-muted">Hali buyurtmalar yo'q</h6>
                    @if($reservation->status === 'confirmed' || $reservation->status === 'checked_in')
                    <a href="{{ route('orders.create', ['reservation_id' => $reservation->id]) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-plus"></i> Birinchi Buyurtma Yaratish
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Financial Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calculator"></i> Moliyaviy Hisob-kitob</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>Xona to'lovi:</td>
                        <td class="text-end fw-bold">{{ number_format($reservation->room_charge) }} so'm</td>
                    </tr>
                    <tr>
                        <td>Ovqat buyurtmalari:</td>
                        <td class="text-end fw-bold">{{ number_format($reservation->orders->sum('total_amount')) }} so'm</td>
                    </tr>
                    <tr class="border-top">
                        <td class="fw-bold">Jami summa:</td>
                        <td class="text-end fw-bold text-primary fs-5">
                            {{ number_format($reservation->getTotalAmount()) }} so'm
                        </td>
                    </tr>
                </table>
                
                @if($reservation->payments && $reservation->payments->count() > 0)
                <hr>
                <h6>To'lovlar tarixi:</h6>
                <div class="payment-history" style="max-height: 200px; overflow-y: auto;">
                    @foreach($reservation->payments as $payment)
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <div>
                            <small>{{ $payment->created_at->format('d.m.Y H:i') }}</small>
                            <br><span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                {{ $payment->payment_method === "cash" ? "Naqd pul" : "Plastik karta" }}
                            </span>
                        </div>
                        <strong>{{ number_format($payment->amount) }} so'm</strong>
                    </div>
                    @endforeach
                </div>
                @endif
                
                @if(in_array($reservation->status, ['checked_in', 'completed']))
                <div class="mt-3">
                    <a href="{{ route('payments.create', ['reservation_id' => $reservation->id]) }}" 
                       class="btn btn-success w-100">
                        <i class="fas fa-credit-card"></i> To'lov Qilish
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Status History -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Holat Tarixi</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Rezervatsiya yaratildi</h6>
                            <small class="text-muted">
                                {{ $reservation->created_at->format('d.m.Y H:i') }}
                                <br>{{ $reservation->user->name }} tomonidan
                            </small>
                        </div>
                    </div>
                    
                    @if($reservation->status !== 'confirmed')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Tasdiqlandi</h6>
                            <small class="text-muted">{{ $reservation->updated_at->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    
                    @if(in_array($reservation->status, ['checked_in', 'completed']))
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Mijoz keldi</h6>
                            <small class="text-muted">{{ $reservation->reservation_date->format('d.m.Y') }}</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($reservation->status === 'completed')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-secondary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Tugallandi</h6>
                            <small class="text-muted">{{ $reservation->end_date->format('d.m.Y') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    height: 100%;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -24px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content h6 {
    margin-bottom: 5px;
}

.payment-history::-webkit-scrollbar {
    width: 4px;
}

.payment-history::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.payment-history::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 2px;
}
</style>
@endsection