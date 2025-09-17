@extends('layouts.app')

@section('title', 'Buyurtmalar')
@section('page-title', 'Buyurtmalar Boshqaruvi')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Barcha Buyurtmalar</h4>
                <p class="text-muted">Jami: {{ $orders->total() }} ta buyurtma</p>
            </div>
            <div>
                <!-- <a href="{{ route('orders.kitchen') }}" class="btn btn-warning me-2">
                    <i class="fas fa-fire"></i> Oshxona Dashboard
                </a> -->
                <a href="{{ route('create_order_by_type') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Yangi Buyurtma
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Status Filter -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-white bg-warning">
            <div class="card-body text-center">
                <h6>Kutilayotgan</h6>
                <h4>{{ $orders->where('status', 'pending')->count() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-secondary">
            <div class="card-body text-center">
                <h6>Tugallangan</h6>
                <h4>{{ $orders->where('status', 'completed')->count() }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> Buyurtmalar Ro'yxati</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Buyurtma №</th>
                        <th>Xona/Mijoz</th>
                        <th>Ofitsiant</th>
                        <th>Vaqt</th>
                        <th>Mahsulotlar</th>
                        <th>Summa</th>
                        <th>Holat</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                            <br><small class="text-muted">{{ $order->order_time->format('d.m.Y H:i') }}</small>
                        </td>
                        <td>
                            <strong>{{ $order->reservation->room->name_uz ?? "Ma'lumot yo'q" }}</strong>
                            <br><small>{{ $order->customer->name }}</small>
                            <br><small class="text-muted">{{ $order->customer->phone }}</small>
                        </td>
                        <td>{{ $order->waiter->name }}</td>
                        <td>
                            {{ $order->order_time->format('H:i') }}
                            @if($order->served_time)
                                <br><small class="text-success">Berildi: {{ $order->served_time->format('H:i') }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $order->items->count() }} mahsulot</span>
                            <br><small class="text-muted">{{ $order->items->sum('quantity') }} dona</small>
                        </td>
                        <td>
                            <strong>{{ number_format($order->total_amount) }} so'm</strong>
                        </td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge bg-warning">Kutilmoqda</span>
                            @elseif($order->status === 'completed')
                                <span class="badge bg-secondary">Tugallangan</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($order->status !== 'completed')
                                <button class="btn btn-outline-primary" onclick="updateStatus('{{ $order->id }}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Buyurtmalar topilmadi</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buyurtma Holatini O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <div class="mb-3">
                        <label class="form-label">Yangi Holat</label>
                        <select class="form-control" id="newStatus" required>
                            <option value="pending">Kutilmoqda</option>
                            <option value="completed">Tugallangan</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" onclick="saveStatus()">Saqlash</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentOrderId = null;

function updateStatus(orderId) {
    currentOrderId = orderId;
    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

function saveStatus() {
    const status = document.getElementById('newStatus').value;
    
    fetch(`/orders/${currentOrderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection