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
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-info" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(in_array($order->status, ['pending']))
                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-primary" title="To'liq Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @if($order->status !== 'completed')
                                <button class="btn btn-outline-warning" onclick="showQuickStatus('{{ $order->id }}', '{{ $order->status }}')" title="Holat O'zgartirish">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                @endif
                                @if(in_array($order->status, ['preparing', 'ready']))
                                <button class="btn btn-outline-success" onclick="quickServe('{{ $order->id }}')" title="Tez Xizmat">
                                    <i class="fas fa-hand-paper"></i>
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
<div class="modal fade" id="orderStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buyurtma Holatini O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Buyurtma holatini tanlang:</p>
                <select class="form-control" id="orderStatusSelect">
                    <option value="pending">Kutilmoqda</option>
                    <option value="completed">Tugallangan</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" onclick="updateOrderStatus()">O'zgartirish</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showQuickStatus(orderId, currentStatus) {
    const modal = new bootstrap.Modal(document.getElementById('quickStatusModal'));
    document.getElementById('currentOrderId').value = orderId;
    document.getElementById('quickStatusSelect').value = currentStatus;
    modal.show();
}

function quickServe(orderId) {
    if (confirm('Bu buyurtmani berildi deb belgilaysizmi?')) {
        updateOrderQuickStatus(orderId, 'served');
    }
}

function updateOrderQuickStatus(orderId, status) {
    fetch(`/orders/${orderId}/quick-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Xatolik yuz berdi');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi');
    });
}

function applyQuickStatus() {
    const orderId = document.getElementById('currentOrderId').value;
    const newStatus = document.getElementById('quickStatusSelect').value;
    
    updateOrderQuickStatus(orderId, newStatus);
    bootstrap.Modal.getInstance(document.getElementById('quickStatusModal')).hide();
}
</script>

<div class="modal fade" id="quickStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tezkor Holat O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentOrderId">
                <div class="mb-3">
                    <label class="form-label">Yangi Holat:</label>
                    <select class="form-control" id="quickStatusSelect">
                        <option value="pending">Kutilmoqda</option>
                        <option value="completed">Tugallangan</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor</button>
                <button type="button" class="btn btn-primary" onclick="applyQuickStatus()">O'zgartirish</button>
            </div>
        </div>
    </div>
</div>
<script>
    let currentOrderId = null;

    function showStatusModal(orderId, currentStatus) {
        currentOrderId = orderId;
        document.getElementById('orderStatusSelect').value = currentStatus;
        new bootstrap.Modal(document.getElementById('orderStatusModal')).show();
    }

    function updateOrderStatus() {
        const newStatus = document.getElementById('orderStatusSelect').value;

        fetch(`/orders/${currentOrderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Xatolik yuz berdi');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Xatolik yuz berdi');
            });

        bootstrap.Modal.getInstance(document.getElementById('orderStatusModal')).hide();
    }
</script>
@endsection