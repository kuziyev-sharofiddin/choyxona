@extends('layouts.app')

@section('title', 'Yangi Rezervatsiya')
@section('page-title', 'Rezervatsiya Yaratish')

@section('content')
<div class="row">
    <div class="col-md-12">
        <form action="{{ route('reservations.store') }}" method="POST">
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
                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                    value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">Telefon Raqami *</label>
                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                    value="" required>
                            </div>
                            <div class="mb-3">
                                <label for="customer_email" class="form-label">Email (ixtiyoriy)</label>
                                <input type="email" class="form-control" id="customer_email" name="customer_email"
                                    value="">
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
                                <select class="form-control" id="room_id" name="room_id" required>
                                    <option value="">Tanlang...</option>
                                    @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" data-price="{{ $room->hourly_rate }}">
                                        {{ $room->name_uz }} ({{ $room->capacity }} kishi) - {{ number_format($room->hourly_rate) }} so'm/soat
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_time" class="form-label">Boshlanish Vaqti *</label>
                                    <input type="datetime-local" class="form-control" id="start_time" name="start_time" required onchange="calculatePrice()">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_time" class="form-label">Tugash Vaqti *</label>
                                    <input type="datetime-local" class="form-control" id="end_time" name="end_time" required onchange="calculatePrice()">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="guest_count" class="form-label">Mehmonlar Soni *</label>
                                <input type="number" class="form-control" id="guest_count" name="guest_count" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="user_id" class="form-label">Ofitsiant *</label>
                                <select class="form-control" id="user_id" name="user_id" required>
                                    <option value="">Tanlang...</option>
                                    @foreach($waiters as $waiter)
                                    <option value="{{ $waiter->id }}">{{ $waiter->name }}</option>
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
                    <div class="mb-3">
                        <label for="special_requests" class="form-label">Maxsus So'rovlar</label>
                        <textarea class="form-control" id="special_requests" name="special_requests" rows="3"></textarea>
                    </div>

                    <div class="alert alert-info" id="price-info" style="display:none;">
                        <h6>Narx Hisoblash:</h6>
                        <div class="row">
                            <div class="col-md-4"><strong>Soatlik narx:</strong> <span id="hourly-rate">0</span> so'm</div>
                            <div class="col-md-4"><strong>Muddat:</strong> <span id="duration">0</span> soat</div>
                            <div class="col-md-4"><strong>Jami xona narxi:</strong> <span id="total-price">0</span> so'm</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('reservations.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Orqaga</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Rezervatsiya Yaratish</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function calculatePrice() {
        const roomSelect = document.getElementById('room_id');
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;

        if (roomSelect.value && startTime && endTime) {
            const hourlyRate = parseFloat(roomSelect.options[roomSelect.selectedIndex].dataset.price);
            const start = new Date(startTime);
            const end = new Date(endTime);
            const duration = Math.ceil((end - start) / (1000 * 60 * 60)); // hours
            const totalPrice = hourlyRate * duration;

            document.getElementById('hourly-rate').textContent = hourlyRate.toLocaleString();
            document.getElementById('duration').textContent = duration;
            document.getElementById('total-price').textContent = totalPrice.toLocaleString();
            document.getElementById('price-info').style.display = 'block';
        } else {
            document.getElementById('price-info').style.display = 'none';
        }
    }

    document.getElementById('start_time').min = new Date().toISOString().slice(0, 16);
</script>
@endsection