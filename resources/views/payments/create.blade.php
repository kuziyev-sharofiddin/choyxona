@extends('layouts.app')

@section('title', 'Yangi To\'lov')
@section('page-title', 'Yangi To\'lov Yaratish')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus"></i> Yangi To'lov</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.store') }}" method="POST">
                    @csrf
                    
                    @if($reservation)
                    <!-- Reservation Payment -->
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-calendar-check"></i> Rezervatsiya To'lovi</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Rezervatsiya №:</strong> {{ $reservation->reservation_number }}</p>
                                <p><strong>Mijoz:</strong> {{ $reservation->customer->name }}</p>
                                <p><strong>Xona:</strong> {{ $reservation->room->name_uz }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Xona narxi:</strong> {{ number_format($reservation->room_charge) }} so'm</p>
                                <p><strong>Buyurtmalar:</strong> {{ number_format($reservation->orders->sum('total_amount')) }} so'm</p>
                                <p><strong>Jami summa:</strong> <span class="text-success fw-bold">{{ number_format($reservation->getTotalAmount()) }} so'm</span></p>
                            </div>
                        </div>
                    </div>
                    
                    @elseif($order)
                    <!-- Order Payment -->
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="alert alert-success">
                        <h6><i class="fas fa-utensils"></i> Buyurtma To'lovi</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Buyurtma №:</strong> {{ $order->order_number }}</p>
                                <p><strong>Mijoz:</strong> {{ $order->customer->name }}</p>
                                <p><strong>Xona:</strong> {{ $order->reservation->room->name_uz }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Mahsulotlar:</strong> {{ $order->items->count() }} ta</p>
                                <p><strong>Buyurtma summasi:</strong> {{ number_format($order->total_amount) }} so'm</p>
                                @if($order->getTotalPaid() > 0)
                                <p><strong>To'langan:</strong> {{ number_format($order->getTotalPaid()) }} so'm</p>
                                <p><strong>Qoldiq:</strong> <span class="text-danger fw-bold">{{ number_format($order->getRemainingAmount()) }} so'm</span></p>
                                @else
                                <p><strong>To'lanishi kerak:</strong> <span class="text-success fw-bold">{{ number_format($order->total_amount) }} so'm</span></p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Order Items -->
                        <div class="mt-3">
                            <h6>Buyurtma tarkibi:</h6>
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
                                            <td>{{ $item->product->name_uz }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->unit_price) }}</td>
                                            <td>{{ number_format($item->total_price) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    @else
                    <!-- Manual Selection -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_type" class="form-label">To'lov turi *</label>
                                <select class="form-control" id="payment_type" onchange="togglePaymentType()" required>
                                    <option value="">To'lov turini tanlang</option>
                                    <option value="reservation">Rezervatsiya uchun</option>
                                    <option value="order">Buyurtma uchun</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div id="reservation_select" style="display: none;">
                        <div class="mb-3">
                            <label for="reservation_id" class="form-label">Rezervatsiya *</label>
                            <select class="form-control" id="reservation_id" name="reservation_id">
                                <option value="">Rezervatsiyani tanlang</option>
                                @foreach(\App\Models\Reservation::whereIn('status', ['checked_in', 'completed'])->with(['customer', 'room'])->get() as $res)
                                <option value="{{ $res->id }}" data-amount="{{ $res->getTotalAmount() }}">
                                    {{ $res->reservation_number }} - {{ $res->customer->name }} ({{ $res->room->name_uz }}) - {{ number_format($res->getTotalAmount()) }} so'm
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div id="order_select" style="display: none;">
                        <div class="mb-3">
                            <label for="order_id" class="form-label">Buyurtma *</label>
                            <select class="form-control" id="order_id" name="order_id">
                                <option value="">Buyurtmani tanlang</option>
                                @foreach(\App\Models\Order::whereIn('status', ['served', 'completed'])->with(['customer', 'reservation.room'])->get() as $ord)
                                <option value="{{ $ord->id }}" data-amount="{{ $ord->getRemainingAmount() }}">
                                    {{ $ord->order_number }} - {{ $ord->customer->name }} ({{ $ord->reservation->room->name_uz  ?? "Ma'lumot yo'q" }}) - 
                                    @if($ord->isFullyPaid())
                                        <span class="text-success">To'langan</span>
                                    @else
                                        {{ number_format($ord->getRemainingAmount()) }} so'm qoldiq
                                    @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">To'lov Summasi (so'm) *</label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       value="{{ old('amount', $reservation ? $reservation->getTotalAmount() : ($order ? $order->getRemainingAmount() : '')) }}" 
                                       min="0" step="100" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">To'lov Usuli *</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="">To'lov usulini tanlang</option>
                                    <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Naqd pul</option>
                                    <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Plastik karta</option>
                                    <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Bank o'tkazmasi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Izoh</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="To'lov haqida qo'shimcha ma'lumot">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-credit-card"></i> To'lovni Qabul Qilish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePaymentType() {
    const paymentType = document.getElementById('payment_type').value;
    const reservationDiv = document.getElementById('reservation_select');
    const orderDiv = document.getElementById('order_select');
    
    if (paymentType === 'reservation') {
        reservationDiv.style.display = 'block';
        orderDiv.style.display = 'none';
        document.getElementById('order_id').value = '';
    } else if (paymentType === 'order') {
        reservationDiv.style.display = 'none';
        orderDiv.style.display = 'block';
        document.getElementById('reservation_id').value = '';
    } else {
        reservationDiv.style.display = 'none';
        orderDiv.style.display = 'none';
    }
}

// Auto-fill amount when reservation/order selected
document.getElementById('reservation_id')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    if (amount) {
        document.getElementById('amount').value = amount;
    }
});

document.getElementById('order_id')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const amount = selectedOption.getAttribute('data-amount');
    if (amount && amount > 0) {
        document.getElementById('amount').value = amount;
    }
});
</script>
@endsection