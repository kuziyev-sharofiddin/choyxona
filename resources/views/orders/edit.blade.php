@extends('layouts.app')

@section('title', 'Buyurtmani Tahrirlash')
@section('page-title', 'Buyurtma #' . $order->order_number . ' - Tahrirlash')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Buyurtmani Tahrirlash</h5>
                <small class="text-muted">
                    Faqat "Kutilmoqda" va "Pending" holatidagi buyurtmalarni to'liq tahrirlash mumkin
                </small>
            </div>
            <div class="card-body">
                <!-- Order Info -->
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Mijoz:</strong> {{ $order->customer->name }}<br>
                            <strong>Xona:</strong> {{ $order->reservation->room->name_uz }}
                        </div>
                        <div class="col-md-6">
                            <strong>Ofitsiant:</strong> {{ $order->waiter->name }}<br>
                            <strong>Vaqt:</strong> {{ $order->order_time->format('d.m.Y H:i') }}
                        </div>
                    </div>
                </div>

                <form id="orderEditForm" action="{{ route('orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Status and Basic Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Buyurtma Holati</label>
                                <select class="form-control" id="status" name="status" onchange="checkEditability()">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Tugallangan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label">Chegirma (so'm)</label>
                                <input type="number" class="form-control" id="discount_amount" name="discount_amount" 
                                       value="{{ $order->discount_amount }}" min="0" step="100" onchange="calculateTotal()">
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Buyurtma Mahsulotlari</h6>
                            <button type="button" class="btn btn-sm btn-success" id="addProductBtn" onclick="showAddProductModal()">
                                <i class="fas fa-plus"></i> Mahsulot Qo'shish
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="orderItemsContainer">
                                @foreach($order->items as $index => $item)
                                <div class="order-item-row border rounded p-3 mb-3" data-item-id="{{ $item->id }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                @if($item->product->image)
                                                <img src="{{ Storage::url($item->product->image) }}" class="me-2 rounded" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product->name_uz }}</strong>
                                                    <br><small class="text-muted">{{ number_format($item->unit_price) }} so'm</small>
                                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                                    <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}">
                                                </div>
                                            </div>
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
                                            <span class="item-total">{{ number_format($item->total_price) }} so'm</span>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control form-control-sm" name="items[{{ $index }}][status]">
                                                <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                                                <option value="preparing" {{ $item->status === 'preparing' ? 'selected' : '' }}>Tayyorlanmoqda</option>
                                                <option value="ready" {{ $item->status === 'ready' ? 'selected' : '' }}>Tayyor</option>
                                                <option value="served" {{ $item->status === 'served' ? 'selected' : '' }}>Berildi</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" onclick="removeItem(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @if($item->special_instructions)
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-circle"></i> {{ $item->special_instructions }}
                                            </small>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            <!-- Totals -->
                            <div class="border-top pt-3">
                                <div class="row">
                                    <div class="col-md-8"></div>
                                    <div class="col-md-4">
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Mahsulotlar:</td>
                                                <td class="text-end" id="subtotalDisplay">{{ number_format($order->subtotal) }} so'm</td>
                                            </tr>
                                            <tr>
                                                <td>Ofitsiant (10%):</td>
                                                <td class="text-end" id="taxDisplay">{{ number_format($order->tax_amount) }} so'm</td>
                                            </tr>
                                            <tr>
                                                <td>Chegirma:</td>
                                                <td class="text-end" id="discountDisplay">{{ number_format($order->discount_amount) }} so'm</td>
                                            </tr>
                                            <tr class="fw-bold">
                                                <td>Jami:</td>
                                                <td class="text-end" id="totalDisplay">{{ number_format($order->total_amount) }} so'm</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Izoh</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ $order->notes }}</textarea>
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

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mahsulot Qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control mb-3" id="productSearch" placeholder="Mahsulot qidirish..." onkeyup="searchProducts()">
                    </div>
                    <div class="col-md-6">
                        <select class="form-control mb-3" id="categoryFilter" onchange="filterByCategory()">
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
                        <div class="card card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small><strong>{{ $product->name_uz }}</strong></small>
                                    <br><small class="text-muted">{{ $product->category->name_uz }}</small>
                                    <br><small class="text-success">{{ number_format($product->price) }} so'm</small>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" onclick="selectProduct({{ $product->id }}, '{{ $product->name_uz }}', {{ $product->price }})">
                                    Tanlash
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Status Modal -->
<div class="modal fade" id="quickStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Holat O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Buyurtma holatini o'zgartirmoqchimisiz?</p>
                <select class="form-control" id="quickStatusSelect">
                    <option value="pending">Kutilmoqda</option>
                    <option value="completed">Tugallangan</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" onclick="applyQuickStatus()">O'zgartirish</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let itemCount = {{ $order->items->count() }};

function checkEditability() {
    const status = document.getElementById('status').value;
    const isEditable = status === 'pending';
    
    // Disable/enable editing based on status
    document.getElementById('addProductBtn').disabled = !isEditable;
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.disabled = !isEditable;
    });
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.disabled = !isEditable;
    });
    
    if (!isEditable) {
        document.getElementById('addProductBtn').innerHTML = '<i class="fas fa-lock"></i> Faqat "Kutilmoqda" holatida tahrirlash mumkin';
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
    
    document.querySelectorAll('.order-item-row').forEach(row => {
        const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]').value);
        const quantity = parseInt(row.querySelector('.quantity-input').value);
        subtotal += unitPrice * quantity;
    });
    
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const tax = subtotal * 0.10;
    const total = subtotal + tax - discount;
    
    document.getElementById('subtotalDisplay').textContent = subtotal.toLocaleString() + ' so\'m';
    document.getElementById('taxDisplay').textContent = tax.toLocaleString() + ' so\'m';
    document.getElementById('discountDisplay').textContent = discount.toLocaleString() + ' so\'m';
    document.getElementById('totalDisplay').textContent = total.toLocaleString() + ' so\'m';
}

function showAddProductModal() {
    const status = document.getElementById('status').value;
    if (status !== 'pending') {
        alert('Faqat "Kutilmoqda" holatidagi buyurtmalarga mahsulot qo\'shish mumkin!');
        return;
    }
    new bootstrap.Modal(document.getElementById('addProductModal')).show();
}

function selectProduct(productId, productName, productPrice) {
    addProductToOrder(productId, productName, productPrice, 1);
    bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
}

function quickAddProduct(productId, productName, productPrice) {
    const status = document.getElementById('status').value;
    if (status !== 'pending') {
        alert('Faqat "Kutilmoqda" holatidagi buyurtmalarga mahsulot qo\'shish mumkin!');
        return;
    }
    addProductToOrder(productId, productName, productPrice, 1);
}

function addProductToOrder(productId, productName, productPrice, quantity) {
    const container = document.getElementById('orderItemsContainer');
    
    const newRow = document.createElement('div');
    newRow.className = 'order-item-row border rounded p-3 mb-3';
    newRow.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <div>
                        <strong>${productName}</strong>
                        <br><small class="text-muted">${productPrice.toLocaleString()} so'm</small>
                        <input type="hidden" name="items[${itemCount}][product_id]" value="${productId}">
                        <input type="hidden" name="items[${itemCount}][unit_price]" value="${productPrice}">
                        <input type="hidden" name="items[${itemCount}][status]" value="pending">
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
                <span class="item-total">${(productPrice * quantity).toLocaleString()} so'm</span>
            </div>
            <div class="col-md-2">
                <select class="form-control form-control-sm" name="items[${itemCount}][status]">
                    <option value="pending">Kutilmoqda</option>
                    <option value="preparing">Tayyorlanmoqda</option>
                    <option value="ready">Tayyor</option>
                    <option value="served">Berildi</option>
                </select>
            </div>
            <div class="col-md-2">
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

function quickStatusChange(status) {
    document.getElementById('quickStatusSelect').value = status;
    new bootstrap.Modal(document.getElementById('quickStatusModal')).show();
}

function applyQuickStatus() {
    const newStatus = document.getElementById('quickStatusSelect').value;
    document.getElementById('status').value = newStatus;
    checkEditability();
    bootstrap.Modal.getInstance(document.getElementById('quickStatusModal')).hide();
}

function addDiscount() {
    const currentDiscount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const newDiscount = prompt('Chegirma summani kiriting (so\'m):', currentDiscount);
    if (newDiscount !== null && !isNaN(newDiscount)) {
        document.getElementById('discount_amount').value = Math.max(0, parseFloat(newDiscount));
        calculateTotal();
    }
}

function clearAllItems() {
    if (confirm('Barcha mahsulotlarni olib tashlashni xohlaysizmi?')) {
        document.querySelectorAll('.order-item-row').forEach(row => row.remove());
        calculateTotal();
    }
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

.product-item {
    transition: transform 0.2s;
}

.product-item:hover {
    transform: translateY(-2px);
}

.quantity-input:disabled {
    background-color: #e9ecef;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endsection