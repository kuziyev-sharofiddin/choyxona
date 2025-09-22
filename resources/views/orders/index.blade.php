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
        </form>
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
                        <th>To'lov</th>
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
                            @php
                                $totalPaid = $order->getTotalPaid();
                                $remainingAmount = $order->getRemainingAmount();
                            @endphp
                            
                            @if($totalPaid == 0)
                                <span class="badge bg-danger">To'lanmagan</span>
                                <br><small class="text-muted">{{ number_format($order->total_amount) }} so'm</small>
                            @elseif($remainingAmount == 0)
                                <span class="badge bg-success">To'langan</span>
                                <br><small class="text-success">{{ number_format($totalPaid) }} so'm</small>
                            @else
                                <span class="badge bg-warning">Qisman</span>
                                <br><small class="text-success">{{ number_format($totalPaid) }} so'm</small>
                                <br><small class="text-danger">Qoldi: {{ number_format($remainingAmount) }}</small>
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
                                <!-- Ko'rish tugmasi -->
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-info" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Tahrirlash tugmasi -->
                                @if(in_array($order->status, ['pending']))
                                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-primary" title="Tahrirlash">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                
                                <!-- To'lov tugmasi -->
                                @if($order->status === 'completed' && $order->getRemainingAmount() > 0)
                                    <button class="btn btn-outline-success" onclick="showPaymentModal('{{ $order->id }}', '{{ $order->order_number }}', {{ $order->getRemainingAmount() }})" title="To'lov">
                                        <i class="fas fa-credit-card"></i>
                                    </button>
                                @endif
                                
                                <!-- Chek chop etish tugmasi -->
                                @if($order->status === 'completed')
                                    <button class="btn btn-outline-secondary" onclick="printReceipt('{{ $order->id }}')" title="Chek">
                                        <i class="fas fa-receipt"></i>
                                    </button>
                                @endif
                                
                                <!-- Holat o'zgartirish -->
                                @if($order->status !== 'completed')
                                    <button class="btn btn-outline-warning" onclick="showQuickStatus('{{ $order->id }}', '{{ $order->status }}')" title="Holat">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
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
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">To'lov Qabul Qilish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <input type="hidden" id="paymentOrderId">
                    
                    <div class="alert alert-info">
                        <strong>Buyurtma:</strong> <span id="paymentOrderNumber"></span><br>
                        <strong>To'lov summasi:</strong> <span id="paymentAmount"></span> so'm
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">To'lov Usuli *</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">To'lov usulini tanlang</option>
                            <option value="cash">Naqd pul</option>
                            <option value="card">Plastik karta</option>
                            <option value="transfer">Bank o'tkazmasi</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">To'lov summasi (so'm) *</label>
                        <input type="number" class="form-control" id="payment_amount" name="amount"  required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Izoh</label>
                        <textarea class="form-control" id="payment_notes" name="notes" rows="2" placeholder="Qo'shimcha ma'lumotlar..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-credit-card"></i> To'lovni Qabul Qilish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="receiptContent">
                <!-- Receipt content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yopish</button>
                <button type="button" class="btn btn-primary" onclick="printReceiptContent()">
                    <i class="fas fa-print"></i> Chop Etish
                </button>
            </div>
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
// Payment Modal Functions
function showPaymentModal(orderId, orderNumber, amount) {
    document.getElementById('paymentOrderId').value = orderId;
    document.getElementById('paymentOrderNumber').textContent = orderNumber;
    document.getElementById('paymentAmount').textContent = amount.toLocaleString();
    document.getElementById('payment_amount').value = amount;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

// Process Payment
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('paymentOrderId').value;
    const formData = new FormData(this);
    
    fetch(`/orders/${orderId}/payment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            location.reload();
        } else {
            alert(data.message || 'To\'lovni qabul qilishda xatolik yuz berdi');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('To\'lovni qabul qilishda xatolik yuz berdi');
    });
});

// Print Receipt Function
function printReceipt(orderId) {
    fetch(`/orders/${orderId}/receipt`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('receiptContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('receiptModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Chekni yuklashda xatolik yuz berdi');
        });
}

function printReceiptContent() {
    const printContent = document.getElementById('receiptContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Chek</title>
                <style>
                    body { font-family: 'Courier New', monospace; margin: 20px; }
                    .receipt { max-width: 300px; margin: 0 auto; }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .border-top { border-top: 1px dashed #000; margin-top: 10px; padding-top: 5px; }
                    .mb-2 { margin-bottom: 10px; }
                    .fw-bold { font-weight: bold; }
                    hr { border: none; border-top: 1px dashed #000; }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                ${printContent}
            </body>
        </html>
    `);
    printWindow.document.close();
}

// Quick Status Functions
function showQuickStatus(orderId, currentStatus) {
    document.getElementById('currentOrderId').value = orderId;
    document.getElementById('quickStatusSelect').value = currentStatus;
    new bootstrap.Modal(document.getElementById('quickStatusModal')).show();
}

function applyQuickStatus() {
    const orderId = document.getElementById('currentOrderId').value;
    const newStatus = document.getElementById('quickStatusSelect').value;
    
    fetch(`/orders/${orderId}/quick-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('quickStatusModal')).hide();
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
    padding: 0.25rem 0.4rem;
    font-size: 0.75rem;
}

.receipt {
    font-family: 'Courier New', monospace;
    max-width: 300px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.2rem 0.3rem;
        font-size: 0.7rem;
    }
}

/* Print styles */
@media print {
    body * {
        visibility: hidden;
    }
    #receiptContent, #receiptContent * {
        visibility: visible;
    }
    #receiptContent {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
@endsection