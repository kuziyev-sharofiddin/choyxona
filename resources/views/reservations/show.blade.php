@extends('layouts.app')

@section('title', 'Rezervatsiya Ma\'lumotlari')
@section('page-title', 'Rezervatsiya #' . $reservation->reservation_number)

@section('content')
<div class="row">
    <!-- Reservation Details -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Rezervatsiya Ma'lumotlari</h5>
                <div>
                    @if($reservation->status === 'confirmed')
                        <form action="{{ route('reservations.checkin', $reservation) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-user-check"></i> Keldi
                            </button>
                        </form>
                    @endif
                    @if(in_array($reservation->status, ['confirmed', 'checked_in']))
                        <a href="{{ route('orders.create', ['reservation_id' => $reservation->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-utensils"></i> Buyurtma Berish
                        </a>
                    @endif
                    @if($reservation->status === 'checked_in')
                        <form action="{{ route('reservations.complete', $reservation) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">
                                <i class="fas fa-flag-checkered"></i> Tugallash
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Rezervatsiya №:</th>
                                <td>{{ $reservation->reservation_number }}</td>
                            </tr>
                            <tr>
                                <th>Mijoz:</th>
                                <td>
                                    <strong>{{ $reservation->customer->name }}</strong><br>
                                    <small>{{ $reservation->customer->phone }}</small>
                                    @if($reservation->customer->email)
                                        <br><small>{{ $reservation->customer->email }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Xona:</th>
                                <td>
                                    <strong>{{ $reservation->room->name_uz }}</strong><br>
                                    <small>{{ $reservation->room->capacity }} kishi sig'imi</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Ofitsiant:</th>
                                <td>{{ $reservation->waiter->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Boshlanish:</th>
                                <td>{{ $reservation->start_time->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Tugash:</th>
                                <td>{{ $reservation->end_time->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Muddat:</th>
                                <td>{{ $reservation->getDuration() }} soat</td>
                            </tr>
                            <tr>
                                <th>Mehmonlar:</th>
                                <td>{{ $reservation->guest_count }} kishi</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($reservation->special_requests)
                <div class="alert alert-info">
                    <strong><i class="fas fa-comment"></i> Maxsus So'rovlar:</strong><br>
                    {{ $reservation->special_requests }}
                </div>
                @endif
            </div>
        </div>

        <!-- Orders -->
        @if($reservation->orders->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-utensils"></i> Buyurtmalar</h5>
            </div>
            <div class="card-body">
                @foreach($reservation->orders as $order)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6>Buyurtma #{{ $order->order_number }}</h6>
                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'info' }}">
                            @if($order->status === 'pending') Kutilmoqda
                            @elseif($order->status === 'preparing') Tayyorlanmoqda
                            @elseif($order->status === 'ready') Tayyor
                            @elseif($order->status === 'served') Berildi
                            @else Tugallangan
                            @endif
                        </span>
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
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->product->name_uz }}
                                        @if($item->special_instructions)
                                            <br><small class="text-muted">{{ $item->special_instructions }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price) }}</td>
                                    <td>{{ number_format($item->total_price) }}</td>
                                </tr>
                                @endforeach
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
            </div>
        </div>
        @endif
    </div>

    <!-- Summary Card -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calculator"></i> Hisob-kitob</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td>Xona narxi:</td>
                        <td class="text-end">{{ number_format($reservation->room_charge) }} so'm</td>
                    </tr>
                    @if($reservation->orders->count() > 0)
                    <tr>
                        <td>Buyurtmalar:</td>
                        <td class="text-end">{{ number_format($reservation->orders->sum('total_amount')) }} so'm</td>
                    </tr>
                    @endif
                    <tr class="border-top">
                        <th>Umumiy summa:</th>
                        <th class="text-end">{{ number_format($reservation->getTotalAmount()) }} so'm</th>
                    </tr>
                </table>

                @if(in_array($reservation->status, ['checked_in', 'completed']))
                <div class="d-grid">
                    <a href="{{ route('payments.create', ['reservation_id' => $reservation->id]) }}" class="btn btn-success">
                        <i class="fas fa-credit-card"></i> To'lov Qabul Qilish
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
                            <h6>Yaratildi</h6>
                            <small>{{ $reservation->created_at->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>
                    @if($reservation->status !== 'pending')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6>Tasdiqlandi</h6>
                            <small>{{ $reservation->updated_at->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    @if(in_array($reservation->status, ['checked_in', 'completed']))
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6>Mijoz keldi</h6>
                            <small>{{ $reservation->start_time->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                    @if($reservation->status === 'completed')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-secondary"></div>
                        <div class="timeline-content">
                            <h6>Tugallandi</h6>
                            <small>{{ $reservation->end_time->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
</style>
@endsection