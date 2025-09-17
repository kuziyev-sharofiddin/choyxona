@extends('layouts.app')

@section('title', 'Qaytarilgan Mahsulotlar')
@section('page-title', 'Qaytarilgan Mahsulotlar')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4>Qaytarilgan Mahsulotlar</h4>
            <p class="text-muted">Jami: {{ $returnedItems->total() }} ta qaytarilgan mahsulot</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-undo"></i> Qaytarilgan Mahsulotlar Ro'yxati</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Sana</th>
                        <th>Buyurtma №</th>
                        <th>Mijoz</th>
                        <th>Xona</th>
                        <th>Mahsulot</th>
                        <th>Miqdor</th>
                        <th>Summa</th>
                        <th>Sabab</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnedItems as $item)
                    <tr>
                        <td>{{ $item->updated_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $item->order) }}">
                                {{ $item->order->order_number }}
                            </a>
                        </td>
                        <td>{{ $item->order->customer->name }}</td>
                        <td>{{ $item->order->reservation->room->name_uz }}</td>
                        <td>
                            <strong>{{ $item->product->name_uz }}</strong>
                            <br><small class="text-muted">{{ number_format($item->unit_price) }} so'm</small>
                        </td>
                        <td>{{ $item->quantity }} dona</td>
                        <td><span class="text-danger">-{{ number_format($item->total_price) }} so'm</span></td>
                        <td>
                            <small class="text-muted">
                                {{ $item->special_instructions ?: 'Sabab ko\'rsatilmagan' }}
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('orders.show', $item->order) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $item->order) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">Qaytarilgan mahsulotlar yo'q</h5>
                            <p class="text-muted">Bu yaxshi yangilik!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $returnedItems->links() }}
        </div>
    </div>
</div>
@endsection