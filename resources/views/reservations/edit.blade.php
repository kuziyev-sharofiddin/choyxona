<!-- resources/views/reservations/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Rezervatsiyani Tahrirlash')
@section('page-title', 'Rezervatsiyani Tahrirlash')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Rezervatsiyani Tahrirlash</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reservations.update', $reservation) }}" method="POST">
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
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                               value="{{ old('customer_name', $reservation->customer->name) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="customer_phone" class="form-label">Telefon Raqami *</label>
                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                               value="{{ old('customer_phone', $reservation->customer->phone) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="customer_email" class="form-label">Email (ixtiyoriy)</label>
                                        <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                               value="{{ old('customer_email', $reservation->customer->email) }}">
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
                                        <select class="form-control" id="room_id" name="room_id" required onchange="calculatePrice()">
                                            @foreach(\App\Models\Room::all() as $room)
                                            <option value="{{ $room->id }}" 
                                                    data-price="{{ $room->hourly_rate }}"
                                                    {{ old('room_id', $reservation->room_id) == $room->id ? 'selected' : '' }}>
                                                {{ $room->name_uz }} ({{ $room->capacity }} kishi) - {{ number_format($room->hourly_rate) }} so'm/soat
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_time" class="form-label">Boshlanish Vaqti *</label>
                                                <input type="datetime-local" class="form-control" id="start_time" name="start_time" 
                                                       value="{{ old('start_time', $reservation->start_time->format('Y-m-d\TH:i')) }}" required onchange="calculatePrice()">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_time" class="form-label">Tugash Vaqti *</label>
                                                <input type="datetime-local" class="form-control" id="end_time" name="end_time" 
                                                       value="{{ old('end_time', $reservation->end_time->format('Y-m-d\TH:i')) }}" required onchange="calculatePrice()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="guest_count" class="form-label">Mehmonlar Soni *</label>
                                        <input type="number" class="form-control" id="guest_count" name="guest_count" 
                                               value="{{ old('guest_count', $reservation->guest_count) }}" min="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="user_id" class="form-label">Ofitsiant *</label>
                                        <select class="form-control" id="user_id" name="user_id" required>
                                            @foreach(\App\Models\User::whereHas('role', function($q) { $q->where('name', 'waiter'); })->where('is_active', true)->get() as $waiter)
                                            <option value="{{ $waiter->id }}" {{ old('user_id', $reservation->user_id) == $waiter->id ? 'selected' : '' }}>
                                                {{ $waiter->name }}
                                            </option>
                                            @endforeach
                                        </select>
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
                                        <label for="status" class="form-label">Holat</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="pending" {{ old('status', $reservation->status) === 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                                            <option value="confirmed" {{ old('status', $reservation->status) === 'confirmed' ? 'selected' : '' }}>Tasdiqlangan</option>
                                            <option value="checked_in" {{ old('status', $reservation->status) === 'checked_in' ? 'selected' : '' }}>Keldi</option>
                                            <option value="completed" {{ old('status', $reservation->status) === 'completed' ? 'selected' : '' }}>Tugallangan</option>
                                            <option value="cancelled" {{ old('status', $reservation->status) === 'cancelled' ? 'selected' : '' }}>Bekor qilingan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="room_charge" class="form-label">Xona Narxi (so'm)</label>
                                        <input type="number" class="form-control" id="room_charge" name="room_charge" 
                                               value="{{ old('room_charge', $reservation->room_charge) }}" min="0" step="1000">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="special_requests" class="form-label">Maxsus So'rovlar</label>
                                <textarea class="form-control" id="special_requests" name="special_requests" rows="3">{{ old('special_requests', $reservation->special_requests) }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                        <div>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save"></i> Yangilash
                            </button>
                            @if($reservation->status !== 'completed')
                            <button type="submit" name="delete" value="1" class="btn btn-danger" onclick="return confirm('Rezervatsiyani o\'chirishni tasdiqlaysizmi?')">
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
@endsection

@section('scripts')
<script>
function calculatePrice() {
    const roomSelect = document.getElementById('room_id');
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    const roomChargeInput = document.getElementById('room_charge');
    
    if (roomSelect.value && startTime && endTime) {
        const hourlyRate = parseFloat(roomSelect.options[roomSelect.selectedIndex].dataset.price);
        const start = new Date(startTime);
        const end = new Date(endTime);
        const duration = Math.ceil((end - start) / (1000 * 60 * 60));
        const totalPrice = hourlyRate * duration;
        
        roomChargeInput.value = totalPrice;
    }
}
</script>
@endsection