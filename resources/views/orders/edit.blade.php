@extends('layouts.app')

@section('title', 'Buyurtmani Tahrirlash')
@section('page-title', 'Buyurtma #' . $order->order_number . ' - Tahrirlash')

@section('content')
<div class="row">
    <!-- Order Header Info -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Buyurtma Ma'lumotlari</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Buyurtma №:</strong> {{ $order->order_number }}
                    </div>
                    <div class="col-md-3">
                        <strong>Mijoz:</strong> {{ $order->customer->name }}
                    </div>
                    <div class="col-md-3">
                        <strong>Xona:</strong> {{ $order->reservation->room->name_uz }}
                    </div>
                    <div class="col-md-3">
                        <strong>Ofitsiant:</strong> {{ $order->waiter->name }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="col-md-8">
        <form action="{{ route('orders.update', $order) }}" method="POST" id="orderEditForm">
            @csrf
            @method('PUT')
            
            <!-- Order Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> Buyurtma Sozlamalari</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Buyurtma Holati</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Tayyorlanmoqda</option>
                                    <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Tayyor</option>
                                    <option value="served" {{ $order->status === 'served' ? 'selected' : '' }}>Berildi</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Tugallandi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label">Chegirma (so'm)</label>
                                <input type="number" class="form-control" id="discount_amount" name="discount_amount" 
                                       value="{{ $order->discount_amount }}" min="0" step="100" onchange="calculateTotal()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Jami Summa</label>
                                <div class="form-control-plaintext fw-bold text-success" id="total_display">
                                    {{ number_format($order->total_amount) }} so'm
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Izoh</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2">{{ $order->notes }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Current Order Items -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Buyurtma Mahsulotlari</h5>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Mahsulot Qo'shish
                    </button>
                </div>
                <div class="card-body">
                    <div id="orderItems">
                        @foreach($order->items as $index => $item)
                        <div class="order-item mb-3 p-3 border rounded" data-item-id="{{ $item->id }}">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <div class="d-flex align-items-center">
                                        @if($item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" class="me-2 rounded" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong>{{ $item->product->name_uz }}</strong>
                                            <br><small class="text-muted">{{ number_format($item->unit_price) }} so'm</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label small">Miqdor</label>
                                    <div class="input-group input-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity({{ $item->id }}, -1)">-</button>
                                        <input type="number" class="form-control text-center quantity-input" 
                                               name="items[{{ $item->id }}][quantity]" 
                                               value="{{ $item->quantity }}" 
                                               min="1" 
                                               data-price="{{ $item->unit_price }}"
                                               onchange="updateItemTotal({{ $item->id }})">
                                        <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity({{ $item->id }}, 1)">+</button>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label small">Holat</label>
                                    <select class="form-control form-control-sm" name="items[{{ $item->id }}][status]">
                                        <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                                        <option value="preparing" {{ $item->status === 'preparing' ? 'selected' : '' }}>Tayyorlanmoqda</option>
                                        <option value="ready" {{ $item->status === 'ready' ? 'selected' : '' }}>Tayyor</option>
                                        <option value="served" {{ $item->status === 'served' ? 'selected' : '' }}>Berildi</option>
                                        <option value="returned" {{ $item->status === 'returned' ? 'selected' : '' }}>Qaytarildi</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label small">Jami</label>
                                    <div class="fw-bold item-total" id="item_total_{{ $item->id }}">
                                        {{ number_format($item->total_price) }} so'm
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label small">Maxsus</label>
                                    <input type="text" class="form-control form-control-sm" 
                                           name="items[{{ $item->id }}][special_instructions]" 
                                           value="{{ $item->special_instructions }}" 
                                           placeholder="Maxsus ko'rsatma">
                                </div>
                                
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem({{ $item->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <input type="hidden" name="items[{{ $item->id }}][remove]" value="0" id="remove_{{ $item->id }}">
                                </div>
                            </div>
                            
                            @if($item->special_instructions)
                            <div class="row mt-2">
                                <div class="col-12">
                                    <small class="text-info">
                                        <i class="fas fa-comment"></i> {{ $item->special_instructions }}
                                    </small>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                        
                        <!-- New items will be added here -->
                        <div id="newItems"></div>
                    </div>
                    
                    @if($order->items->count() === 0)
                    <div class="text-center py-4" id="emptyMessage">
                        <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Buyurtmada mahsulotlar yo'q</h5>
                        <p class="text-muted">Mahsulot qo'shish uchun yuqoridagi tugmani bosing</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between mb-4">
                <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Orqaga
                </a>
                <div>
                    <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                    <button type="submit" name="action" value="save_and_print" class="btn btn-success">
                        <i class="fas fa-print"></i> Saqlash va Chop Etish
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Order Summary -->
    <div class="col-md-4">
        <div class="card sticky-top">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calculator"></i> Hisob-kitob</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td>Mahsulotlar:</td>
                        <td class="text-end" id="subtotal_display">{{ number_format($order->subtotal) }} so'm</td>
                    </tr>
                    <tr>
                        <td>Soliq (12%):</td>
                        <td class="text-end" id="tax_display">{{ number_format($order->tax_amount) }} so'm</td>
                    </tr>
                    <tr>
                        <td>Chegirma:</td>
                        <td class="text-end text-success" id="discount_display">-{{ number_format($order->discount_amount) }} so'm</td>
                    </tr>
                    <tr class="border-top">
                        <th>Jami:</th>
                        <th class="text-end" id="total_summary">{{ number_format($order->total_amount) }} so'm</th>
                    </tr>
                </table>
                
                <hr>
                
                <div class="mb-2">
                    <strong>Mahsulotlar soni:</strong> <span id="item_count">{{ $order->items->count() }}</span>
                </div>
                <div class="mb-2">
                    <strong>Jami dona:</strong> <span id="total_quantity">{{ $order->items->sum('quantity') }}</span>
                </div>
                
                @php
                    $totalPrepTime = $order->items->sum(function($item) {
                        return $item->product->preparation_time * $item->quantity;
                    });
                @endphp
                <div class="mb-3">
                    <strong>Taxminiy vaqt:</strong> <span id="prep_time">~{{ $totalPrepTime }} daqiqa</span>
                </div>
                
                <!-- Order History -->
                <div class="mt-4">
                    <h6>Buyurtma Tarixi:</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between">
                            <span>Yaratildi:</span>
                            <span>{{ $order->order_time->format('d.m.Y H:i') }}</span>
                        </div>
                        @if($order->served_time)
                        <div class="d-flex justify-content-between">
                            <span>Berildi:</span>
                            <span>{{ $order->served_time->format('d.m.Y H:i') }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span>Oxirgi yangilanish:</span>
                            <span>{{ $order->updated_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>
                </div>
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
                <!-- Category Tabs -->
                <ul class="nav nav-tabs mb-3" id="categoryTabs">
                    @foreach($categories as $index => $category)
                    <li class="nav-item">
                        <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#category-{{ $category->id }}">
                            {{ $category->name_uz }}
                        </a>
                    </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    @foreach($categories as $index => $category)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="category-{{ $category->id }}">
                        <div class="row">
                            @foreach($category->products as $product)
                            <div class="col-md-6 mb-3">
                                <div class="card product-select-card" onclick="selectProduct({{ $product->id }}, '{{ $product->name_uz }}', {{ $product->price }})">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">{{ $product->name_uz }}</h6>
                                                <p class="card-text small text-muted">
                                                    {{ Str::limit($product->description_uz ?? $product->description, 50) }}
                                                </p>
                                                <span class="badge bg-primary">{{ number_format($product->price) }} so'm</span>
                                            </div>
                                            @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Customize Modal -->
<div class="modal fade" id="productCustomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productCustomTitle">Mahsulot Sozlamalari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Miqdor</label>
                    <div class="input-group">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeCustomQuantity(-1)">-</button>
                        <input type="number" class="form-control text-center" id="customQuantity" value="1" min="1">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeCustomQuantity(1)">+</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Maxsus ko'rsatmalar</label>
                    <textarea class="form-control" id="customInstructions" rows="2" 
                              placeholder="Masalan: achchiq bo'lmasin, kam tuzli..."></textarea>
                </div>
                <div class="text-center">
                    <span class="h6">Jami: </span>
                    <span class="h6 text-success" id="customTotal">0 so'm</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" onclick="addCustomProduct()">Qo'shish</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedProduct = null;
let newItemCounter = 0;

// Product selection
function selectProduct(productId, productName, productPrice) {
    selectedProduct = {
        id: productId,
        name: productName,
        price: productPrice
    };
    
    document.getElementById('productCustomTitle').textContent = productName;
    document.getElementById('customQuantity').value = 1;
    document.getElementById('customInstructions').value = '';
    updateCustomTotal();
    
    bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
    new bootstrap.Modal(document.getElementById('productCustomModal')).show();
}

function changeCustomQuantity(change) {
    const quantityInput = document.getElementById('customQuantity');
    const newValue = parseInt(quantityInput.value) + change;
    if (newValue >= 1) {
        quantityInput.value = newValue;
        updateCustomTotal();
    }
}

function updateCustomTotal() {
    const quantity = parseInt(document.getElementById('customQuantity').value) || 1;
    const total = selectedProduct.price * quantity;
    document.getElementById('customTotal').textContent = total.toLocaleString() + ' so\'m';
}

// Add new product to order
function addCustomProduct() {
    const quantity = parseInt(document.getElementById('customQuantity').value);
    const instructions = document.getElementById('customInstructions').value;
    const totalPrice = selectedProduct.price * quantity;
    
    newItemCounter++;
    const newItemHtml = `
        <div class="order-item mb-3 p-3 border rounded border-success" data-new-item="new_${newItemCounter}">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div>
                        <strong>${selectedProduct.name}</strong> <span class="badge bg-success">Yangi</span>
                        <br><small class="text-muted">${selectedProduct.price.toLocaleString()} so'm</small>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label small">Miqdor</label>
                    <div class="input-group input-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="changeNewQuantity('new_${newItemCounter}', -1)">-</button>
                        <input type="number" class="form-control text-center quantity-input" 
                               name="new_items[new_${newItemCounter}][quantity]" 
                               value="${quantity}" 
                               min="1" 
                               data-price="${selectedProduct.price}"
                               onchange="updateNewItemTotal('new_${newItemCounter}')">
                        <button type="button" class="btn btn-outline-secondary" onclick="changeNewQuantity('new_${newItemCounter}', 1)">+</button>
                    </div>
                    <input type="hidden" name="new_items[new_${newItemCounter}][product_id]" value="${selectedProduct.id}">
                    <input type="hidden" name="new_items[new_${newItemCounter}][unit_price]" value="${selectedProduct.price}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label small">Holat</label>
                    <select class="form-control form-control-sm" name="new_items[new_${newItemCounter}][status]">
                        <option value="pending">Kutilmoqda</option>
                        <option value="preparing">Tayyorlanmoqda</option>
                        <option value="ready">Tayyor</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label small">Jami</label>
                    <div class="fw-bold item-total" id="new_item_total_new_${newItemCounter}">
                        ${totalPrice.toLocaleString()} so'm
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label small">Maxsus</label>
                    <input type="text" class="form-control form-control-sm" 
                           name="new_items[new_${newItemCounter}][special_instructions]" 
                           value="${instructions}" 
                           placeholder="Maxsus ko'rsatma">
                </div>
                
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeNewItem('new_${newItemCounter}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('newItems').insertAdjacentHTML('beforeend', newItemHtml);
    document.getElementById('emptyMessage')?.style.display = 'none';
    
    bootstrap.Modal.getInstance(document.getElementById('productCustomModal')).hide();
    calculateTotal();
}

// Quantity management
function changeQuantity(itemId, change) {
    const input = document.querySelector(`input[name="items[${itemId}][quantity]"]`);
    const newValue = parseInt(input.value) + change;
    if (newValue >= 1) {
        input.value = newValue;
        updateItemTotal(itemId);
    }
}

function changeNewQuantity(itemKey, change) {
    const input = document.querySelector(`input[name="new_items[${itemKey}][quantity]"]`);
    const newValue = parseInt(input.value) + change;
    if (newValue >= 1) {
        input.value = newValue;
        updateNewItemTotal(itemKey);
    }
}

// Update totals
function updateItemTotal(itemId) {
    const input = document.querySelector(`input[name="items[${itemId}][quantity]"]`);
    const price = parseFloat(input.dataset.price);
    const quantity = parseInt(input.value);
    const total = price * quantity;
    
    document.getElementById(`item_total_${itemId}`).textContent = total.toLocaleString() + ' so\'m';
    calculateTotal();
}

function updateNewItemTotal(itemKey) {
    const input = document.querySelector(`input[name="new_items[${itemKey}][quantity]"]`);
    const price = parseFloat(input.dataset.price);
    const quantity = parseInt(input.value);
    const total = price * quantity;
    
    document.getElementById(`new_item_total_${itemKey}`).textContent = total.toLocaleString() + ' so\'m';
    calculateTotal();
}

// Remove items
function removeItem(itemId) {
    if (confirm('Bu mahsulotni buyurtmadan olib tashlashni tasdiqlaysizmi?')) {
        document.querySelector(`[data-item-id="${itemId}"]`).style.display = 'none';
        document.getElementById(`remove_${itemId}`).value = '1';
        calculateTotal();
    }
}

function removeNewItem(itemKey) {
    document.querySelector(`[data-new-item="${itemKey}"]`).remove();
    calculateTotal();
}

// Calculate total
function calculateTotal() {
    let subtotal = 0;
    
    // Existing items
    document.querySelectorAll('.order-item[data-item-id]:not([style*="display: none"])').forEach(item => {
        const input = item.querySelector('.quantity-input');
        const price = parseFloat(input.dataset.price);
        const quantity = parseInt(input.value);
        subtotal += price * quantity;
    });
    
    // New items
    document.querySelectorAll('.order-item[data-new-item]').forEach(item => {
        const input = item.querySelector('.quantity-input');
        const price = parseFloat(input.dataset.price);
        const quantity = parseInt(input.value);
        subtotal += price * quantity;
    });
    
    const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
    const tax = subtotal * 0.12;
    const total = subtotal + tax - discount;
    
    // Update displays
    document.getElementById('subtotal_display').textContent = subtotal.toLocaleString() + ' so\'m';
    document.getElementById('tax_display').textContent = tax.toLocaleString() + ' so\'m';
    document.getElementById('discount_display').textContent = '-' + discount.toLocaleString() + ' so\'m';
    document.getElementById('total_display').textContent = total.toLocaleString() + ' so\'m';
    document.getElementById('total_summary').textContent = total.toLocaleString() + ' so\'m';
    
    // Update counters
    const itemCount = document.querySelectorAll('.order-item[data-item-id]:not([style*="display: none"]), .order-item[data-new-item]').length;
    document.getElementById('item_count').textContent = itemCount;
    
    let totalQuantity = 0;
    document.querySelectorAll('.quantity-input').forEach(input => {
        const parent = input.closest('.order-item');
        if (parent && !parent.style.display.includes('none')) {
            totalQuantity += parseInt(input.value);
        }
    });
    document.getElementById('total_quantity').textContent = totalQuantity;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Product card hover effects
    document.querySelectorAll('.product-select-card').forEach(card => {
        card.style.cursor = 'pointer';
        card.style.transition = 'transform 0.2s';
        
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
});
</script>

<style>
.product-select-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-select-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.sticky-top {
    top: 20px;
}

.order-item {
    transition: all 0.3s ease;
}

.border-success {
    border-color: #28a745 !important;
}

.quantity-input {
    max-width: 80px;
}
</style>
@endsection