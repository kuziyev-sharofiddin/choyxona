<!-- resources/views/customers/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Mijozni Tahrirlash')
@section('page-title', 'Mijozni Tahrirlash')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-edit"></i> Mijozni Tahrirlash</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.update', $customer) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">To'liq Ism *</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon Raqami *</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email (ixtiyoriy)</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $customer->email) }}">
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Saqlash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection