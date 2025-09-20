<!-- resources/views/orders/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Buyurtmani Tahrirlash')
@section('page-title')
    Buyurtma #{{ $order->order_number }}
    @if($order->reservation && $order->reservation->room && $order->reservation->room->name_uz)
    <span class="badge bg-primary">
    {{ $order->reservation->room->name_uz }} -xona
    </span>
@else
    - Xona mavjud emas
@endif

    - Tahrirlash
@endsection


@section('content')
<form action="{{ route('orders.update', $order) }}" method="POST" id="orderEditForm">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Order Type Display & Basic Info -->
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle"></i> Buyurtma Ma'lumotlari
                        </h6>
                        <div>
                            @if($order->order_type === 'dine_in')
                                <span class="badge bg-primary"><i class="fas fa-utensils"></i> Ichkarida Ovqatlanish</span>
                            @elseif($order->order_type === 'takeaway')
                                <span class="badge bg-success"><i class="fas fa-shopping-bag"></i> Olib Ketish</span>
                            @else
                                <span class="badge bg-warning"><i class="fas fa-truck"></i> Yetkazib Berish</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Dine-in: Reservation Info -->
                        @if($order->order_type === 'dine_in' && $order->reservation)
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <strong><i class="fas fa-calendar-check"></i> Rezervatsiya Ma'lumotlari:</strong><br>
                                <strong>№:</strong> {{ $order->reservation->reservation_number }}<br>
                                <strong>Xona:</strong> {{ $order->reservation->room->name_uz }}<br>
                                <strong>Mehmonlar:</strong> {{ $order->reservation->guest_count }} kishi<br>
                                <strong>Vaqt:</strong> {{ $order->reservation->reservation_date->format('d.m.Y H:i') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-primary">
                                <strong><i class="fas fa-user"></i> Mijoz:</strong> {{ $order->customer->name }}<br>
                                <strong><i class="fas fa-phone"></i> Telefon:</strong> {{ $order->customer->phone }}<br>
                                <strong><i class="fas fa-user-tie"></i> Ofitsiant:</strong> {{ $order->waiter->name }}
                            </div>
                        </div>
                        @endif

                        <!-- Takeaway & Delivery: Customer Info -->
                        @if(in_array($order->order_type, ['takeaway', 'delivery']))
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Mijoz Ma'lumotlari</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">Mijoz Ismi</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                               value="{{ old('customer_name', $order->customer_name) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="customer_phone" class="form-label">Telefon</label>
                                        <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                               value="{{ old('customer_phone', $order->customer_phone) }}" required>
                                    </div>
                                    <div class="small text-muted">
                                        <strong>Ofitsiant:</strong> {{ $order->waiter->name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery: Address & Fee -->
                        @if($order->order_type === 'delivery')
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Yetkazib Berish Ma'lumotlari</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="delivery_address" class="form-label">Manzil</label>
                                        <textarea class="form-control" id="delivery_address" name="delivery_address" 
                                                  rows="2" required>{{ old('delivery_address', $order->delivery_address) }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="delivery_fee" class="form-label">Yetkazib Berish Haqi (so'm)</label>
                                        <input type="number" class="form-control" id="delivery_fee" name="delivery_fee" 
                                               value="{{ old('delivery_fee', $order->delivery_fee) }}" min="0" step="1000" onchange="calculateTotal()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Takeaway: Pickup Info -->
                        @if($order->order_type === 'takeaway')
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Olib Ketish Ma'lumotlari</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-success">
                                        <i class="fas fa-clock"></i> <strong>Buyurtma Vaqti:</strong> {{ $order->order_time->format('d.m.Y H:i') }}<br>
                                        @if($order->served_time)
                                        <i class="fas fa-check"></i> <strong>Tayyor Bo'lgan:</strong> {{ $order->served_time->format('d.m.Y H:i') }}
                                        @else
                                        <i class="fas fa-hourglass-half"></i> <strong>Holat:</strong> 
                                        @if($order->status === 'pending')
                                        Jarayonda
                                        @elseif($order->status === 'preparing')
                                            Tayyorlanmoqda
                                        @elseif($order->status === 'ready')
                                            Tayyor
                                        @else
                                            {{ ucfirst($order->status) }}
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items & Management -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-utensils"></i> Buyurtma Mahsulotlari</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-success me-2" id="addProductBtn" onclick="showAddProductModal()">
                            <i class="fas fa-plus"></i> Mahsulot Qo'shish
                        </button>
                        <select class="form-control form-control-sm d-inline" style="width: auto;" id="status" name="status" onchange="checkEditability()">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Jarayonda</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Tugallangan</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="orderItemsContainer">
                        @foreach($order->items as $index => $item)
                        <div class="order-item-row border rounded p-3 mb-3" data-item-id="{{ $item->id }}">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center">
                                        @if($item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" class="me-2 rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong>{{ $item->product->name_uz }}</strong>
                                            <br><small class="text-muted">{{ $item->product->category->name_uz }}</small>
                                            <br><small class="text-success">{{ number_format($item->unit_price) }} so'm</small>
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                            <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}">
                                        </div>
                                    </div>
                                    @if($item->special_instructions)
                                    <div class="mt-2">
                                        <small class="text-warning">
                                            <i class="fas fa-exclamation-circle"></i> {{ $item->special_instructions }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(this, -1)">-</button>
                                        <input type="number" class="form-control text-center quantity-input" 
                                               name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" 
                                               min="0" onchange="updateItemTotal(this)">
                                        <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(this, 1)">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <strong class="item-total">{{ number_format($item->total_price) }} so'm</strong>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control form-control-sm" name="items[{{ $index }}][status]">
                                        <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Jarayonda</option>
                                        <option value="preparing" {{ $item->status === 'preparing' ? 'selected' : '' }}>Tayyorlanmoqda</option>
                                        <option value="ready" {{ $item->status === 'ready' ? 'selected' : '' }}>Tayyor</option>
                                        <option value="served" {{ $item->status === 'served' ? 'selected' : '' }}>Berildi</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" onclick="removeItem(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Notes -->
                    <div class="mt-3">
                        <label for="notes" class="form-label">Izoh</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2">{{ $order->notes }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary & Controls -->
        <div class="col-md-4">
            <div class="card sticky-top">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-calculator"></i> Hisob-kitob</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td>Mahsulotlar:</td>
                            <td class="text-end" id="subtotalDisplay">{{ number_format($order->subtotal) }} so'm</td>
                        </tr>
                        @if($order->order_type === 'dine_in')
                        <tr>
                            <td>Ofitsiant (10%):</td>
                            <td class="text-end" id="commissionDisplay">{{ number_format($order->waiter_commission) }} so'm</td>
                        </tr>
                        @endif
                        @if($order->order_type === 'delivery')
                        <tr>
                            <td>Yetkazib berish:</td>
                            <td class="text-end" id="deliveryFeeDisplay">{{ number_format($order->delivery_fee) }} so'm</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Chegirma:</td>
                            <td class="text-end">
                                <input type="number" class="form-control form-control-sm text-end" 
                                       name="discount_amount" id="discount_amount" value="{{ $order->discount_amount }}" 
                                       min="0" step="1000" onchange="calculateTotal()" style="width: 100px;">
                            </td>
                        </tr>
                        <tr class="border-top">
                            <th>Jami:</th>
                            <th class="text-end" id="totalDisplay">{{ number_format($order->total_amount) }} so'm</th>
                        </tr>
                    </table>

                    <hr>

                    <!-- Order Type Specific Info -->
                    @if($order->order_type === 'dine_in')
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle"></i> <strong>Dine-in:</strong><br>
                        • 10% ofitsiant komissiyasi qo'shiladi<br>
                        • Xona: {{ $order->reservation->room->name_uz ?? 'N/A' }}
                    </div>
                    @elseif($order->order_type === 'takeaway')
                    <div class="alert alert-success small">
                        <i class="fas fa-shopping-bag"></i> <strong>Takeaway:</strong><br>
                        • Ofitsiant komissiyasi yo'q<br>
                        • Mijoz o'zi olib ketadi
                    </div>
                    @else
                    <div class="alert alert-warning small">
                        <i class="fas fa-truck"></i> <strong>Delivery:</strong><br>
                        • Yetkazib berish haqi qo'shiladi<br>
                        • Manzil: {{ Str::limit($order->delivery_address, 30) }}
                    </div>
                    @endif

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Yangilash
                        </button>    
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eye"></i> Ko'rish
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mahsulot Qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="productSearch" placeholder="Mahsulot qidirish..." onkeyup="searchProducts()">
                    </div>
                    <div class="col-md-6">
                        <select class="form-control" id="categoryFilter" onchange="filterByCategory()">
                            <option value="">Barcha kategoriyalar</option>
                            @foreach(\App\Models\Category::where('is_active', true)->get() as $category)
                            <option value="{{ $category->id }}">{{ $category->name_uz }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div id="productsGrid" class="row">
                    @foreach(\App\Models\Product::where('is_available', true)->with('category')->get() as $product)
                    <div class="col-md-6 mb-2 product-item" data-category="{{ $product->category_id }}" data-name="{{ strtolower($product->name_uz) }}">
                        <div class="card card-body p-2" style="cursor: pointer;" onclick="selectProduct({{ $product->id }}, '{{ $product->name_uz }}', {{ $product->price }})">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $product->name_uz }}</strong>
                                    <br><small class="text-muted">{{ $product->category->name_uz }}</small>
                                    <br><small class="text-success">{{ number_format($product->price) }} so'm</small>
                                </div>
                                @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="rounded" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let itemCount = {{ $order->items->count() }};
const orderType = '{{ $order->order_type }}';

function checkEditability() {
    const status = document.getElementById('status').value;
    const canEditItems = status === 'pending';
    
    // Enable/disable item editing
    document.getElementById('addProductBtn').disabled = !canEditItems;
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.disabled = !canEditItems;
    });
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.disabled = !canEditItems;
    });
    
    // Update button text
    if (!canEditItems) {
        document.getElementById('addProductBtn').innerHTML = '<i class="fas fa-lock"></i> Faqat "Jarayonda" holatida tahrirlash mumkin';
    } else {
        document.getElementById('addProductBtn').innerHTML = '<i class="fas fa-plus"></i> Mahsulot Qo\'shish';
    }
}

function changeQuantity(button, change) {
    const input = button.parentNode.querySelector('.quantity-input');
    const newValue = parseInt(input.value) + change;
    
    if (newValue >= 0) {
        input.value = newValue;
        updateItemTotal(input);
        
        if (newValue === 0) {
            removeItem(button);
        }
    }
}

function updateItemTotal(input) {
    const row = input.closest('.order-item-row');
    const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value);
    const quantity = parseInt(input.value);
    const total = unitPrice * quantity;
    
    row.querySelector('.item-total').textContent = total.toLocaleString() + ' so\'m';
    calculateTotal();
}

function removeItem(button) {
    if (confirm('Bu mahsulotni olib tashlashni xohlaysizmi?')) {
        button.closest('.order-item-row').remove();
        calculateTotal();
    }
}

function calculateTotal() {
    let subtotal = 0;
    
    // Calculate subtotal from all items
    document.querySelectorAll('.order-item-row').forEach(row => {
        const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value);
        const quantity = parseInt(row.querySelector('.quantity-input').value);
        subtotal += unitPrice * quantity;
    });
    
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    
    // Calculate commission based on order type
    let commission = 0;
    if (orderType === 'dine_in') {
        commission = subtotal * 0.10; // 10% for dine-in
    }
    
    // Calculate delivery fee for delivery orders
    let deliveryFee = 0;
    if (orderType === 'delivery') {
        deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;
    }
    
    const total = subtotal + commission + deliveryFee - discount;
    
    // Update displays
    document.getElementById('subtotalDisplay').textContent = subtotal.toLocaleString() + ' so\'m';
    
    if (orderType === 'dine_in') {
        document.getElementById('commissionDisplay').textContent = commission.toLocaleString() + ' so\'m';
    }
    
    if (orderType === 'delivery') {
        document.getElementById('deliveryFeeDisplay').textContent = deliveryFee.toLocaleString() + ' so\'m';
    }
    
    document.getElementById('totalDisplay').textContent = total.toLocaleString() + ' so\'m';
}

function showAddProductModal() {
    const status = document.getElementById('status').value;
    if (status !== 'pending') {
        alert('Faqat "Jarayonda" holatidagi buyurtmalarga mahsulot qo\'shish mumkin!');
        return;
    }
    new bootstrap.Modal(document.getElementById('addProductModal')).show();
}

function selectProduct(productId, productName, productPrice) {
    addProductToOrder(productId, productName, productPrice, 1);
    bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
}

function addProductToOrder(productId, productName, productPrice, quantity) {
    const container = document.getElementById('orderItemsContainer');
    
    const newRow = document.createElement('div');
    newRow.className = 'order-item-row border rounded p-3 mb-3';
    newRow.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-5">
                <div class="d-flex align-items-center">
                    <div>
                        <strong>${productName}</strong>
                        <br><small class="text-success">${productPrice.toLocaleString()} so'm</small>
                        <input type="hidden" name="items[${itemCount}][product_id]" value="${productId}">
                        <input type="hidden" name="items[${itemCount}][unit_price]" value="${productPrice}">
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(this, -1)">-</button>
                    <input type="number" class="form-control text-center quantity-input" 
                           name="items[${itemCount}][quantity]" value="${quantity}" min="0" onchange="updateItemTotal(this)">
                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(this, 1)">+</button>
                </div>
            </div>
            <div class="col-md-2">
                <div class="text-center">
                    <strong class="item-total">${(productPrice * quantity).toLocaleString()} so'm</strong>
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-control form-control-sm" name="items[${itemCount}][status]">
                    <option value="pending">Jarayonda</option>
                    <option value="preparing">Tayyorlanmoqda</option>
                    <option value="ready">Tayyor</option>
                    <option value="served">Berildi</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" onclick="removeItem(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(newRow);
    itemCount++;
    calculateTotal();
}

function searchProducts() {
    const searchTerm = document.getElementById('productSearch').value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(item => {
        const productName = item.dataset.name;
        item.style.display = productName.includes(searchTerm) ? 'block' : 'none';
    });
}

function filterByCategory() {
    const categoryId = document.getElementById('categoryFilter').value;
    document.querySelectorAll('.product-item').forEach(item => {
        const itemCategory = item.dataset.category;
        item.style.display = !categoryId || itemCategory === categoryId ? 'block' : 'none';
    });
}

function quickStatusChange(newStatus) {
    if (confirm(`Buyurtma holatini "${newStatus}" ga o'zgartirmoqchimisiz?`)) {
        document.getElementById('status').value = newStatus;
        checkEditability();
    }
}

// Delivery fee change handler
if (orderType === 'delivery') {
    document.getElementById('delivery_fee').addEventListener('input', calculateTotal);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    checkEditability();
    calculateTotal();
});
</script>

<style>
.order-item-row {
    transition: all 0.3s ease;
}

.order-item-row:hover {
    background-color: #f8f9fa;
}

.product-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.quantity-input:disabled {
    background-color: #e9ecef;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.sticky-top {
    top: 20px;
}
</style>
@endsection