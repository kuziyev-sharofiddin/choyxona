{{-- resources/views/reservations/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Rezervatsiyani Tahrirlash')
@section('page-title', 'Rezervatsiyani Tahrirlash')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Rezervatsiyani Tahrirlash</h5>
                    <div>
                        <span class="badge bg-info">{{ $reservation->reservation_number }}</span>
                        <span class="badge bg-{{ $reservation->status === 'confirmed' ? 'success' : 'warning' }} ms-1">
                            {{ ucfirst($reservation->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('reservations.update', $reservation) }}" method="POST" id="editReservationForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Mijoz Ma'lumotlari</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">Mijoz Ismi *</label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                               id="customer_name" name="customer_name" 
                                               value="{{ old('customer_name', $reservation->customer->name) }}" required>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="customer_phone" class="form-label">Telefon Raqami *</label>
                                        <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                               id="customer_phone" name="customer_phone" 
                                               value="{{ old('customer_phone', $reservation->customer->phone) }}" required>
                                        @error('customer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="customer_email" class="form-label">Email (ixtiyoriy)</label>
                                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                               id="customer_email" name="customer_email" 
                                               value="{{ old('customer_email', $reservation->customer->email) }}">
                                        @error('customer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="guest_count" class="form-label">Mehmonlar Soni *</label>
                                        <input type="number" class="form-control @error('guest_count') is-invalid @enderror" 
                                               id="guest_count" name="guest_count" 
                                               value="{{ old('guest_count', $reservation->guest_count) }}" 
                                               min="1" max="50" required>
                                        @error('guest_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reservation Details -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Rezervatsiya Ma'lumotlari</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="room_id" class="form-label">Xona *</label>
                                        <select class="form-control @error('room_id') is-invalid @enderror" 
                                                id="room_id" name="room_id" required onchange="calculatePrice()">
                                            @foreach($rooms as $room)
                                            <option value="{{ $room->id }}" 
                                                    data-price="{{ $room->daily_rate }}"
                                                    data-capacity="{{ $room->capacity }}"
                                                    {{ old('room_id', $reservation->room_id) == $room->id ? 'selected' : '' }}>
                                                {{ $room->name_uz }} ({{ $room->capacity }} kishi) - {{ number_format($room->daily_rate) }} so'm/kun
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('room_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="reservation_date" class="form-label">Boshlanish Sanasi *</label>
                                                <input type="date" class="form-control @error('reservation_date') is-invalid @enderror" 
                                                       id="reservation_date" name="reservation_date" 
                                                       value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d')) }}" 
                                                       required onchange="calculatePrice()">
                                                @error('reservation_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="days_count" class="form-label">Kunlar Soni *</label>
                                                <input type="number" class="form-control @error('days_count') is-invalid @enderror" 
                                                       id="days_count" name="days_count" 
                                                       value="{{ old('days_count', $reservation->days_count) }}" 
                                                       min="1" max="30" required onchange="calculatePrice()">
                                                @error('days_count')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="user_id" class="form-label">Mas'ul Xodim *</label>
                                        <select class="form-control @error('user_id') is-invalid @enderror" 
                                                id="user_id" name="user_id" required>
                                            @foreach($waiters as $waiter)
                                            <option value="{{ $waiter->id }}" 
                                                {{ old('user_id', $reservation->user_id) == $waiter->id ? 'selected' : '' }}>
                                                {{ $waiter->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Holat</label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status">
                                            <option value="confirmed" 
                                                {{ old('status', $reservation->status) === 'confirmed' ? 'selected' : '' }}>
                                                Tasdiqlangan
                                            </option>
                                            <option value="checked_in" 
                                                {{ old('status', $reservation->status) === 'checked_in' ? 'selected' : '' }}>
                                                Keldi
                                            </option>
                                            <option value="completed" 
                                                {{ old('status', $reservation->status) === 'completed' ? 'selected' : '' }}>
                                                Tugallangan
                                            </option>
                                            <option value="cancelled" 
                                                {{ old('status', $reservation->status) === 'cancelled' ? 'selected' : '' }}>
                                                Bekor qilingan
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Qo'shimcha Ma'lumotlar</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="special_requests" class="form-label">Maxsus So'rovlar</label>
                                        <textarea class="form-control" id="special_requests" name="special_requests" 
                                                  rows="4" placeholder="Qo'shimcha talablar yoki izohlar...">{{ old('special_requests', $reservation->special_requests) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- Current Price Display -->
                                    <div class="alert alert-light border">
                                        <h6>Hozirgi Narx Ma'lumotlari:</h6>
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td><strong>Joriy xona narxi:</strong></td>
                                                <td>{{ number_format($reservation->room_charge) }} so'm</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Kunlar soni:</strong></td>
                                                <td>{{ $reservation->days_count }} kun</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tugash sanasi:</strong></td>
                                                <td>{{ $reservation->end_date->format('d.m.Y') }}</td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- New Price Calculation -->
                                    <div class="alert alert-info" id="price-info" style="display:none;">
                                        <h6>Yangi Narx Hisoblash:</h6>
                                        <div class="row small">
                                            <div class="col-6">
                                                <strong>Kunlik narx:</strong><br>
                                                <span id="daily-rate">0</span> so'm
                                            </div>
                                            <div class="col-6">
                                                <strong>Kunlar soni:</strong><br>
                                                <span id="days-count-display">0</span> kun
                                            </div>
                                            <div class="col-6 mt-2">
                                                <strong>Tugash sanasi:</strong><br>
                                                <span id="end-date-display">-</span>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <strong>Yangi narx:</strong><br>
                                                <span id="total-price" class="text-primary fw-bold">0</span> so'm
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Availability Check -->
                                    <div class="alert" id="availability-info" style="display: none;">
                                        <span id="availability-message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                        <div>
                            <button type="button" class="btn btn-outline-info me-2" onclick="checkAvailability()">
                                <i class="fas fa-search"></i> Mavjudlikni Tekshirish
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Yangilash
                            </button>
                            @if($reservation->status !== 'completed' && $reservation->orders->count() === 0)
                            <button type="submit" name="delete" value="1" class="btn btn-danger ms-2" 
                                    onclick="return confirm('Rezervatsiyani o\'chirishni tasdiqlaysizmi?')">
                                <i class="fas fa-trash"></i> O'chirish
                            </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Bugungi sanani minimum qiymat sifatida belgilash
document.getElementById('reservation_date').min = new Date().toISOString().split('T')[0];

// Telefon raqam formatlash
document.getElementById('customer_phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('998')) {
        value = '+' + value.replace(/(\d{3})(\d{2})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
    }
    e.target.value = value;
});

// Narxni hisoblash
function calculatePrice() {
    const roomSelect = document.getElementById('room_id');
    const daysInput = document.getElementById('days_count');
    const dateInput = document.getElementById('reservation_date');
    
    if (roomSelect.value && daysInput.value && dateInput.value) {
        const dailyRate = parseInt(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price'));
        const days = parseInt(daysInput.value);
        const startDate = new Date(dateInput.value);
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + days - 1);
        
        const totalPrice = dailyRate * days;
        
        document.getElementById('daily-rate').textContent = dailyRate.toLocaleString();
        document.getElementById('days-count-display').textContent = days;
        document.getElementById('end-date-display').textContent = endDate.toLocaleDateString('uz-UZ');
        document.getElementById('total-price').textContent = totalPrice.toLocaleString();
        document.getElementById('price-info').style.display = 'block';
        
        // Mavjudlikni avtomatik tekshirish
        checkAvailability();
    } else {
        document.getElementById('price-info').style.display = 'none';
        document.getElementById('availability-info').style.display = 'none';
    }
}

// Mavjudlikni tekshirish
function checkAvailability() {
    const roomId = document.getElementById('room_id').value;
    const date = document.getElementById('reservation_date').value;
    const daysCount = document.getElementById('days_count').value;
    
    if (!roomId || !date || !daysCount) {
        return;
    }
    
    fetch('/reservations/check-availability', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            room_id: roomId,
            date: date,
            days_count: daysCount,
            exclude_reservation_id: {{ $reservation->id }} // Joriy rezervatsiyani hisobga olmaslik
        })
    })
    .then(response => response.json())
    .then(data => {
        const availabilityInfo = document.getElementById('availability-info');
        const availabilityMessage = document.getElementById('availability-message');
        const submitBtn = document.getElementById('submitBtn');
        
        if (data.available) {
            availabilityInfo.className = 'alert alert-success';
            availabilityMessage.textContent = 'Xona tanlangan kunlarda mavjud! Yangilashingiz mumkin.';
            submitBtn.disabled = false;
        } else {
            availabilityInfo.className = 'alert alert-danger';
            availabilityMessage.textContent = 'Xona tanlangan kunlarda band! Boshqa kun yoki xona tanlang.';
            submitBtn.disabled = true;
        }
        
        availabilityInfo.style.display = 'block';
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Mehmonlar soni tekshirish
document.getElementById('guest_count').addEventListener('change', function() {
    const roomSelect = document.getElementById('room_id');
    if (roomSelect.value) {
        const roomCapacity = parseInt(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-capacity'));
        const guestCount = parseInt(this.value);
        
        if (guestCount > roomCapacity) {
            alert(`Bu xona faqat ${roomCapacity} kishigacha sig'adi!`);
            this.value = roomCapacity;
        }
    }
});

// Form submit qilishdan oldin tekshirish
document.getElementById('editReservationForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn.disabled && !e.submitter.name === 'delete') {
        e.preventDefault();
        alert('Xona mavjudligini tekshiring va barcha maydonlarni to\'ldiring!');
    }
});

// Sahifa yuklanganda narxni hisoblash
document.addEventListener('DOMContentLoaded', function() {
    calculatePrice();
});
</script>

<style>
.alert {
    border-radius: 8px;
}

.table-borderless td {
    padding: 0.25rem 0.5rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 8px;
}

.btn {
    border-radius: 6px;
}

@media (max-width: 768px) {
    .btn {
        margin-bottom: 5px;
    }
}
</style>
@endsection