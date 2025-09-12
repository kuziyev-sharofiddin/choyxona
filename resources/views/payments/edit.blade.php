<!-- resources/views/payments/edit.blade.php -->
@extends('layouts.app')

@section('title', 'To\'lovni Tahrirlash')
@section('page-title', 'To\'lovni Tahrirlash')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> To'lovni Tahrirlash</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.update', $payment) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> To'lov Ma'lumotlari</h6>
                        <p><strong>To'lov №:</strong> {{ $payment->payment_number }}</p>
                        <p><strong>Mijoz:</strong> {{ $payment->reservation->customer->name }}</p>
                        <p><strong>Rezervatsiya:</strong> {{ $payment->reservation->reservation_number }}</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">To'lov Summasi (so'm) *</label>
                                <input type="number" class="form-control" id="amount" name="amount" 
                                       value="{{ old('amount', $payment->amount) }}" min="0" step="100" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">To'lov Usuli *</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="cash" {{ old('payment_method', $payment->payment_method) === 'cash' ? 'selected' : '' }}>Naqd pul</option>
                                    <option value="card" {{ old('payment_method', $payment->payment_method) === 'card' ? 'selected' : '' }}>Plastik karta</option>
                                    <option value="transfer" {{ old('payment_method', $payment->payment_method) === 'transfer' ? 'selected' : '' }}>Bank o'tkazmasi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Izoh</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('payments.show', $payment) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Yangilash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection