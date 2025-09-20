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
                <h6>Jarayonda</h6>
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

<!-- Order Type Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-utensils fa-2x text-primary mb-2"></i>
                <h6>Ichkarida Ovqatlanish</h6>
                <h4 class="text-primary">{{ $orders->where('order_type', 'dine_in')->count() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-shopping-bag fa-2x text-success mb-2"></i>
                <h6>Olib Ketish</h6>
                <h4 class="text-success">{{ $orders->where('order_type', 'takeaway')->count() }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-truck fa-2x text-warning mb-2"></i>
                <h6>Yetkazib Berish</h6>
                <h4 class="text-warning">{{ $orders->where('order_type', 'delivery')->count() }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3" id="filterForm">
            <div class="col-md-2">
                <label class="form-label">Buyurtma Turi</label>
                <select class="form-control" name="order_type" onchange="this.form.submit()">
                    <option value="">Barcha turlar</option>
                    <option value="dine_in" {{ request('order_type') === 'dine_in' ? 'selected' : '' }}>Ichkarida Ovqatlanish</option>
                    <option value="takeaway" {{ request('order_type') === 'takeaway' ? 'selected' : '' }}>Olib Ketish</option>
                    <option value="delivery" {{ request('order_type') === 'delivery' ? 'selected' : '' }}>Yetkazib Berish</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Holat</label>
                <select class="form-control" name="status" onchange="this.form.submit()">
                    <option value="">Barcha holatlar</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Jarayonda</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Tugallangan</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Sana</label>
                <input type="date" class="form-control" name="date" value="{{ request('date') }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-2">
                <label class="form-label">Dan</label>
                <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Gacha</label>
                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Qidirish</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Buyurtma, mijoz...">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Active Filters Display -->
        @if(request()->hasAny(['order_type', 'status', 'date', 'start_date', 'end_date', 'search']))
        <div class="mt-3">
            <small class="text-muted">Faol filtrlar:</small>
            <div class="d-flex flex-wrap gap-1 mt-1">
                @if(request('order_type'))
                    <span class="badge bg-primary">
                        Tur: {{ ['dine_in' => 'Ichkarida', 'takeaway' => 'Olib ketish', 'delivery' => 'Yetkazib berish'][request('order_type')] }}
                        <a href="{{ request()->fullUrlWithQuery(['order_type' => null]) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                @if(request('status'))
                    <span class="badge bg-info">
                        Holat: {{ ucfirst(request('status')) }}
                        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                @if(request('date'))
                    <span class="badge bg-success">
                        Sana: {{ request('date') }}
                        <a href="{{ request()->fullUrlWithQuery(['date' => null]) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                @if(request('start_date') && request('end_date'))
                    <span class="badge bg-warning">
                        Oraliq: {{ request('start_date') }} - {{ request('end_date') }}
                        <a href="{{ request()->fullUrlWithQuery(['start_date' => null, 'end_date' => null]) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                @if(request('search'))
                    <span class="badge bg-secondary">
                        Qidiruv: "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                <a href="{{ route('orders.index') }}" class="badge bg-danger text-decoration-none">
                    Barcha filtrlarni tozalash
                </a>
            </div>
        </div>
        @endif
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
                        <th>Tur</th>
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
                            @if($order->order_type === 'dine_in')
                                <span class="badge bg-primary">
                                    <i class="fas fa-utensils"></i> Ichkarida
                                </span>
                            @elseif($order->order_type === 'takeaway')
                                <span class="badge bg-success">
                                    <i class="fas fa-shopping-bag"></i> Olib ketish
                                </span>
                            @elseif($order->order_type === 'delivery')
                                <span class="badge bg-warning">
                                    <i class="fas fa-truck"></i> Yetkazib berish
                                </span>
                            @else
                                <span class="badge bg-secondary">{{ $order->order_type }}</span>
                            @endif
                        </td>
                        <td>
                            @if($order->order_type === 'dine_in' && $order->reservation)
                                <strong>{{ $order->reservation->room->name_uz }}</strong>
                                <br><small>{{ $order->customer->name }}</small>
                                <br><small class="text-muted">{{ $order->customer->phone }}</small>
                            @else
                                <strong>{{ $order->customer_name ?? $order->customer->name }}</strong>
                                <br><small class="text-muted">{{ $order->customer_phone ?? $order->customer->phone }}</small>
                                @if($order->order_type === 'delivery' && $order->delivery_address)
                                    <br><small class="text-info"><i class="fas fa-map-marker-alt"></i> {{ Str::limit($order->delivery_address, 30) }}</small>
                                @endif
                            @endif
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
                            @if($order->order_type === 'dine_in' && $order->waiter_commission > 0)
                                <br><small class="text-info">+{{ number_format($order->waiter_commission) }} komis.</small>
                            @endif
                            @if($order->order_type === 'delivery' && $order->delivery_fee > 0)
                                <br><small class="text-warning">+{{ number_format($order->delivery_fee) }} yetk.</small>
                            @endif
                        </td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge bg-warning">Jarayonda</span>
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
                        <td colspan="9" class="text-center py-4">
                            <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Buyurtmalar topilmadi</h5>
                            <p class="text-muted">Yangi buyurtma yaratish uchun yuqoridagi tugmani bosing</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            {{ $orders->appends(request()->query())->links() }}
            
            <!-- Per page selector -->
            <div class="d-flex align-items-center">
                <span class="me-2 text-muted">Ko'rsatish:</span>
                <form method="GET" class="d-inline">
                    @foreach(request()->query() as $key => $value)
                        @if($key !== 'per_page' && $key !== 'page')
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span class="ms-2 text-muted">ta</span>
            </div>
        </div>
        
        <!-- Pagination Info -->
        <div class="mt-2 text-center">
            <small class="text-muted">
                {{ $orders->firstItem() }}-{{ $orders->lastItem() }} dan {{ $orders->total() }} ta ko'rsatilmoqda
                @if(request()->hasAny(['order_type', 'status', 'date', 'search']))
                    (filtrlangan)
                @endif
            </small>
        </div>
    </div>
</div>

<!-- Quick Status Update Modal -->
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
                        <option value="pending">Jarayonda</option>
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
@endsection

@section('scripts')
<script>
function showQuickStatus(orderId, currentStatus) {
    document.getElementById('currentOrderId').value = orderId;
    document.getElementById('quickStatusSelect').value = currentStatus;
    new bootstrap.Modal(document.getElementById('quickStatusModal')).show();
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

// Auto refresh every 30 seconds for kitchen orders
setInterval(() => {
    const url = new URL(window.location);
    if (url.searchParams.get('status') === 'preparing' || url.searchParams.get('status') === 'ready') {
        location.reload();
    }
}, 30000);
</script>

<style>
.table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.1);
}

.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.7rem;
    }
}
</style>
@endsection