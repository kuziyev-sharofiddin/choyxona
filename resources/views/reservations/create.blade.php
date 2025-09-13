@extends('layouts.app')

@section('title', 'Yangi Rezervatsiya')
@section('page-title', 'Yangi Rezervatsiya Yaratish')

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm">
            @csrf
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
                                       id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">Telefon Raqami *</label>
                                <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                       id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" 
                                       placeholder="+998 90 123 45 67" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="customer_email" class="form-label">Email (ixtiyoriy)</label>
                                <input type="email" class="form-control" id="customer_email" 
                                       name="customer_email" value="{{ old('customer_email') }}">
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
                                    <option value="">Xonani tanlang</option>
                                    @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" 
                                            data-price="{{ $room->daily_rate }}"
                                            data-capacity="{{ $room->capacity }}"
                                            @if($selectedRoom && $selectedRoom->id == $room->id) selected @endif>
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
                                               value="{{ old('reservation_date') }}" required onchange="calculatePrice()">
                                        @error('reservation_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="days_count" class="form-label">Kunlar Soni *</label>
                                        <input type="number" class="form-control @error('days_count') is-invalid @enderror" 
                                               id="days_count" name="days_count" value="{{ old('days_count', 1) }}" 
                                               min="1" max="30" required onchange="calculatePrice()">
                                        @error('days_count')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="guest_count" class="form-label">Mehmonlar Soni *</label>
                                <input type="number" class="form-control @error('guest_count') is-invalid @enderror" 
                                       id="guest_count" name="guest_count" value="{{ old('guest_count', 1) }}" 
                                       min="1" max="50" required>
                                @error('guest_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="user_id" class="form-label">Mas'ul Xodim *</label>
                                <select class="form-control @error('user_id') is-invalid @enderror" 
                                        id="user_id" name="user_id" required>
                                    <option value="">Xodimni tanlang</option>
                                    @foreach($waiters as $waiter)
                                    <option value="{{ $waiter->id }}" @if(old('user_id') == $waiter->id) selected @endif>
                                        {{ $waiter->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('user_id')
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
                    <div class="mb-3">
                        <label for="special_requests" class="form-label">Maxsus So'rovlar</label>
                        <textarea class="form-control" id="special_requests" name="special_requests" 
                                  rows="3" placeholder="Qo'shimcha talablar yoki izohlar...">{{ old('special_requests') }}</textarea>
                    </div>

                    <!-- Price Information -->
                    <div class="alert alert-info" id="price-info" style="display:none;">
                        <h6>Narx Hisoblash:</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Kunlik narx:</strong> <span id="daily-rate">0</span> so'm
                            </div>
                            <div class="col-md-3">
                                <strong>Kunlar soni:</strong> <span id="days-count-display">0</span> kun
                            </div>
                            <div class="col-md-3">
                                <strong>Tugash sanasi:</strong> <span id="end-date-display">-</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Jami xona narxi:</strong> <span id="total-price">0</span> so'm
                            </div>
                        </div>
                    </div>
                    
                    <!-- Availability Check -->
                    <div class="alert" id="availability-info" style="display: none;">
                        <span id="availability-message"></span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Orqaga
                </a>
                <div>
                    <button type="button" class="btn btn-outline-primary me-2" onclick="checkAvailability()">
                        <i class="fas fa-search"></i> Mavjudlikni Tekshirish
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Rezervatsiya Yaratish
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Bugungi sanani minimum qiymat sifatida belgilash
document.getElementById('reservation_date').min = new Date().toISOString().split('T')[0];

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
            days_count: daysCount
        })
    })
    .then(response => response.json())
    .then(data => {
        const availabilityInfo = document.getElementById('availability-info');
        const availabilityMessage = document.getElementById('availability-message');
        
        if (data.available) {
            availabilityInfo.className = 'alert alert-success';
            availabilityMessage.textContent = 'Xona tanlangan kunlarda mavjud! Rezervatsiya qilishingiz mumkin.';
        } else {
            availabilityInfo.className = 'alert alert-danger';
            availabilityMessage.textContent = 'Xona tanlangan kunlarda band! Boshqa kun yoki xona tanlang.';
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
</script>
@endsection