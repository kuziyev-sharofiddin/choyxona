<!-- resources/views/payments/create.blade.php -->
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
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id }}">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Rezervatsiya Ma'lumotlari</h6>
                        <p><strong>Mijoz:</strong> {{ $reservation->customer->name }}</p>
                        <p><strong>Xona:</strong> {{ $reservation->room->name_uz }}</p>
                        <p><strong>Jami summa:</strong> {{ number_format($reservation->getTotalAmount()) }} so'm</p>
                    </div>
                    @else
                    <div class="mb-3">
                        <label for="reservation_id" class="form-label">Rezervatsiya *</label>
                        <select class="form-control" id="reservation_id" name="reservation_id" required>
                            <option value="">Rezervatsiyani tanlang</option>
                            @foreach(\App\Models\Reservation::whereIn('status', ['checked_in', 'completed'])->with(['customer', 'room'])->get() as $res)
                            <option value="{{ $res->id }}">
                                {{ $res->reservation_number }} - {{ $res->customer->name }} ({{ $res->room->name_uz }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">To'lov Summasi (so'm) *</label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       value="{{ old('amount', $reservation ? $reservation->getTotalAmount() : '') }}" 
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
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> To'lovni Saqlash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection