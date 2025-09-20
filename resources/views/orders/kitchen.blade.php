<!-- resources/views/orders/kitchen.blade.php -->
@extends('layouts.app')

@section('title', 'Oshxona Dashboard')
@section('page-title', 'Oshxona - Buyurtmalar Dashboard')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4>Faol Buyurtmalar</h4>
            <div>
                <button class="btn btn-success" onclick="location.reload()">
                    <i class="fas fa-sync"></i> Yangilash
                </button>
                <span class="badge bg-info ms-2">Avtomatik yangilanish: 30s</span>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Jarayonda</h6>
                        <h3 id="pendingCount">{{ $orders->where('status', 'pending')->count() }}</h3>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Tayyorlanayotgan</h6>
                        <h3 id="preparingCount">{{ $orders->where('status', 'preparing')->count() }}</h3>
                    </div>
                    <i class="fas fa-utensils fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Tayyor</h6>
                        <h3 id="readyCount">{{ $orders->where('status', 'ready')->count() }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Orders Grid -->
<div class="row" id="ordersGrid">
    @foreach($orders as $order)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card order-card border-start-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'preparing' ? 'info' : 'success') }}" 
             style="border-left-width: 5px !important;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">{{ $order->order_number }}</h6>
                    <small class="text-muted">{{ $order->reservation->room->name_uz }}</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'preparing' ? 'info' : 'success') }}">
                        @if($order->status === 'pending') Jarayonda
                        @else Tugallangan
                        @endif
                    </span>
                    <div class="small text-muted mt-1">
                        {{ $order->order_time->diffForHumans() }}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Order Items -->
                <div class="order-items mb-3">
                    @foreach($order->items as $item)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <strong>{{ $item->product->name_uz }}</strong>
                            <span class="badge bg-primary ms-1">{{ $item->quantity }}x</span>
                            @if($item->special_instructions)
                                <div class="small text-danger mt-1">
                                    <i class="fas fa-exclamation-circle"></i> {{ $item->special_instructions }}
                                </div>
                            @endif
                        </div>
                        <div class="text-end">
                            <small class="text-muted">~{{ $item->product->preparation_time }} min</small>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Customer Info -->
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-user"></i> {{ $order->customer->name }}<br>
                        <i class="fas fa-phone"></i> {{ $order->customer->phone }}
                    </small>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-1">
                    @if($order->status === 'pending')
                        <button class="btn btn-info btn-sm" onclick="updateOrderStatus('{{ $order->id }}', 'preparing')">
                            <i class="fas fa-play"></i> Tayyorlashni Boshlash
                        </button>
                    @elseif($order->status === 'preparing')
                        <button class="btn btn-success btn-sm" onclick="updateOrderStatus('{{ $order->id }}', 'ready')">
                            <i class="fas fa-check"></i> Tayyor Deb Belgilash
                        </button>
                    @else
                        <button class="btn btn-outline-secondary btn-sm" onclick="updateOrderStatus('{{ $order->id }}', 'served')">
                            <i class="fas fa-hand-paper"></i> Berildi
                        </button>
                    @endif
                    
                    @if($order->status !== 'ready')
                        <button class="btn btn-outline-warning btn-sm" onclick="addNote('{{ $order->id }}')">
                            <i class="fas fa-sticky-note"></i> Izoh Qo'shish
                        </button>
                    @endif
                </div>

                @if($order->notes)
                <div class="mt-2">
                    <small class="text-info">
                        <i class="fas fa-comment"></i> {{ $order->notes }}
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($orders->isEmpty())
<div class="text-center py-5">
    <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
    <h5 class="text-muted">Hozirda faol buyurtmalar yo'q</h5>
    <p class="text-muted">Yangi buyurtmalar kelishi bilan bu yerda ko'rsatiladi</p>
</div>
@endif

<!-- Note Modal -->
<div class="modal fade" id="noteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Izoh Qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="orderNote" rows="3" placeholder="Izoh yozing..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" onclick="saveNote()">Saqlash</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentOrderId = null;

function updateOrderStatus(orderId, status) {
    fetch(`/orders/${orderId}/status`, {
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
    })
    .catch(error => {
        console.error('Xatolik:', error);
        alert('Status yangilashda xatolik yuz berdi');
    });
}

function addNote(orderId) {
    currentOrderId = orderId;
    document.getElementById('orderNote').value = '';
    new bootstrap.Modal(document.getElementById('noteModal')).show();
}

function saveNote() {
    const note = document.getElementById('orderNote').value;
    
    fetch(`/orders/${currentOrderId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ notes: note })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('noteModal')).hide();
            location.reload();
        }
    })
    .catch(error => {
        console.error('Xatolik:', error);
        alert('Izoh saqlashda xatolik yuz berdi');
    });
}

// Auto refresh every 30 seconds
setInterval(() => {
    location.reload();
}, 30000);

// Sound notification for new orders
function playNotification() {
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUYrTp66hVFApGn+DyvmwhBSuJ3O7Vgy4GH3P');
    audio.play().catch(e => console.log('Sound play failed'));
}

// Check for new orders every 10 seconds
let lastOrderCount = {{ $orders->count() }};
setInterval(() => {
    fetch('/api/orders/status/pending')
        .then(response => response.json())
        .then(data => {
            if (data.count > lastOrderCount) {
                playNotification();
                lastOrderCount = data.count;
            }
        });
}, 10000);
</script>

<style>
.border-start-warning {
    border-left-color: #ffc107 !important;
}

.border-start-info {
    border-left-color: #0dcaf0 !important;
}

.border-start-success {
    border-left-color: #198754 !important;
}

.order-card {
    transition: transform 0.2s;
    height: 100%;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.order-items {
    max-height: 200px;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .col-lg-4 {
        margin-bottom: 1rem;
    }
}
</style>
@endsection