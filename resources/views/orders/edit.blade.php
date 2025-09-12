<!-- resources/views/orders/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Buyurtmani Tahrirlash')
@section('page-title', 'Buyurtmani Tahrirlash')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Buyurtmani Tahrirlash</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Buyurtma Ma'lumotlari</h6>
                    <p><strong>Buyurtma №:</strong> {{ $order->order_number }}</p>
                    <p><strong>Mijoz:</strong> {{ $order->customer->name }}</p>
                    <p><strong>Xona:</strong> {{ $order->reservation->room->name_uz }}</p>
                </div>

                <form action="{{ route('orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Buyurtma Holati *</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                                    <option value="preparing" {{ old('status', $order->status) === 'preparing' ? 'selected' : '' }}>Tayyorlanmoqda</option>
                                    <option value="ready" {{ old('status', $order->status) === 'ready' ? 'selected' : '' }}>Tayyor</option>
                                    <option value="served" {{ old('status', $order->status) === 'served' ? 'selected' : '' }}>Berildi</option>
                                    <option value="completed" {{ old('status', $order->status) === 'completed' ? 'selected' : '' }}>Tugallandi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label">Chegirma (so'm)</label>
                                <input type="number" class="form-control" id="discount_amount" name="discount_amount" 
                                       value="{{ old('discount_amount', $order->discount_amount) }}" min="0" step="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Izoh</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Buyurtma Mahsulotlari</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Mahsulot</th>
                                            <th>Miqdor</th>
                                            <th>Narx</th>
                                            <th>Jami</th>
                                            <th>Holat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name_uz }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->unit_price) }} so'm</td>
                                            <td>{{ number_format($item->total_price) }} so'm</td>
                                            <td>
                                                <select class="form-control form-control-sm" name="item_status[{{ $item->id }}]">
                                                    <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                                                    <option value="preparing" {{ $item->status === 'preparing' ? 'selected' : '' }}>Tayyorlanmoqda</option>
                                                    <option value="ready" {{ $item->status === 'ready' ? 'selected' : '' }}>Tayyor</option>
                                                    <option value="served" {{ $item->status === 'served' ? 'selected' : '' }}>Berildi</option>
                                                </select>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
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