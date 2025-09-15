@extends('layouts.app')

@section('title', 'Yangi Buyurtma')
@section('page-title', 'Yangi Buyurtma Yaratish')

@section('content')
<div class="row">
    <!-- Order Type Selection -->
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Buyurtma Turi</h6>
            </div>
            <div class="card-body">
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="order_type" id="dine_in" value="dine_in" {{ $orderType === 'dine_in' ? 'checked' : '' }}>
                    <label class="btn btn-outline-primary" for="dine_in">
                        <i class="fas fa-utensils"></i> Ichkarida Ovqatlanish
                    </label>

                    <input type="radio" class="btn-check" name="order_type" id="takeaway" value="takeaway" {{ $orderType === 'takeaway' ? 'checked' : '' }}>
                    <label class="btn btn-outline-success" for="takeaway">
                        <i class="fas fa-shopping-bag"></i> Olib Ketish
                    </label>

                    <input type="radio" class="btn-check" name="order_type" id="delivery" value="delivery" {{ $orderType === 'delivery' ? 'checked' : '' }}>
                    <label class="btn btn-outline-warning" for="delivery">
                        <i class="fas fa-truck"></i> Yetkazib Berish
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Information (for takeaway/delivery) -->
    <div class="col-12 mb-3" id="customerInfo" style="display: {{ in_array($orderType, ['takeaway', 'delivery']) ? 'block' : 'none' }};">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Mijoz Ma'lumotlari</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Mijoz Ismi *</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Telefon *</label>
                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="customer_email" class="form-label">Email (ixtiyoriy)</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email">
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Address (only for delivery) -->
                <div id="deliveryInfo" style="display: {{ $orderType === 'delivery' ? 'block' : 'none' }};">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="delivery_address" class="form-label">Yetkazib Berish Manzili *</label>
                                <textarea class="form-control" id="delivery_address" name="delivery_address" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="delivery_fee" class="form-label">Yetkazib Berish Haqi (so'm)</label>
                                <input type="number" class="form-control" id="delivery_fee" name="delivery_fee" value="10000" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Info (for dine-in only) -->
    @if($reservation && $orderType === 'dine_in')
    <div class="col-12 mb-3" id="reservationInfo">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Rezervatsiya Ma'lumotlari</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Rezervatsiya №:</strong> {{ $reservation->reservation_number }}
                    </div>
                    <div class="col-md-3">
                        <strong>Mijoz:</strong> {{ $reservation->customer->name }}
                    </div>
                    <div class="col-md-3">
                        <strong>Xona:</strong> {{ $reservation->room->name_uz }}
                    </div>
                    <div class="col-md-3">
                        <strong>Mehmonlar:</strong> {{ $reservation->guest_count }} kishi
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Product Selection -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-utensils"></i> Mahsulotlarni Tanlang</h5>
            </div>
            <div class="card-body">
                <!-- Category Tabs -->
                <ul class="nav nav-tabs" id="categoryTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#popular">
                            <i class="fas fa-star"></i> Mashhur
                        </a>
                    </li>
                    @foreach($categories as $category)
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#category-{{ $category->id }}">
                            {{ $category->name_uz }}
                        </a>
                    </li>
                    @endforeach
                </ul>

                <div class="tab-content mt-3">
                    <!-- Popular Products -->
                    <div class="tab-pane fade show active" id="popular">
                        <div class="row">
                            @foreach($popularProducts as $product)
                            <div class="col-md-6 mb-3">
                                <div class="card product-card" onclick="addToOrder({{ $product->id }}, '{{ $product->name_uz }}', {{ $product->price }})">
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

                    <!-- Category Products -->
                    @foreach($categories as $category)
                    <div class="tab-pane fade" id="category-{{ $category->id }}">
                        <div class="row">
                            @foreach($category->products as $product)
                            <div class="col-md-6 mb-3">
                                <div class="card product-card" onclick="addToOrder({{ $product->id }}, '{{ $product->name_uz }}', {{ $product->price }})">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">{{ $product->name_uz }}</h6>
                                                <p class="card-text small text-muted">
                                                    {{ Str::limit($product->description_uz ?? $product->description, 50) }}
                                                </p>
                                                <span class="badge bg-primary">{{ number_format($product->price) }} so'm</span>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-clock"></i> ~{{ $product->preparation_time }} daqiqa
                                                </small>
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

    <!-- Order Summary -->
    <div class="col-md-4">
        <div class="card sticky-top">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Buyurtma</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                    @csrf
                    <input type="hidden" name="order_type" id="selected_order_type" value="{{ $orderType }}">
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id ?? '' }}">
                    
                    <div id="orderItems">
                        <p class="text-muted text-center">Mahsulot tanlanmagan</p>
                    </div>

                    <hr>

                    <!-- Order Totals -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Jami:</span>
                            <span id="subtotal">0 so'm</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Ofitsiant (10%):</span>
                            <span id="commission">0 so'm</span>
                        </div>
                        <div class="d-flex justify-content-between" id="deliveryFeeRow" style="display: none;">
                            <span>Yetkazib berish:</span>
                            <span id="deliveryFeeDisplay">0 so'm</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Chegirma:</span>
                            <input type="number" class="form-control form-control-sm d-inline" style="width: 100px;" 
                                   name="discount_amount" id="discount" value="0" min="0" onchange="calculateTotal()">
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Umumiy summa:</span>
                            <span id="total">0 so'm</span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Izoh</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Maxsus talablar..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i class="fas fa-check"></i> Buyurtma Berish
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal for Customization -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle">Mahsulot Sozlamalari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Miqdor</label>
                    <div class="input-group">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)">-</button>
                        <input type="number" class="form-control text-center" id="modalQuantity" value="1" min="1">
                        <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">+</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Maxsus ko'rsatmalar</label>
                    <textarea class="form-control" id="modalInstructions" rows="2" 
                              placeholder="Masalan: achchiq bo'lmasin, kam tuzli..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" onclick="confirmAddToOrder()">Qo'shish</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let orderItems = [];
let currentProduct = null;

// Order type change handler
document.querySelectorAll('input[name="order_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const orderType = this.value;
        document.getElementById('selected_order_type').value = orderType;
        
        // Show/hide customer info
        const customerInfo = document.getElementById('customerInfo');
        const deliveryInfo = document.getElementById('deliveryInfo');
        const deliveryFeeRow = document.getElementById('deliveryFeeRow');
        
        if (orderType === 'dine_in') {
            customerInfo.style.display = 'none';
            deliveryInfo.style.display = 'none';
            deliveryFeeRow.style.display = 'none';
        } else {
            customerInfo.style.display = 'block';
            deliveryInfo.style.display = orderType === 'delivery' ? 'block' : 'none';
            deliveryFeeRow.style.display = orderType === 'delivery' ? 'flex' : 'none';
        }
        
        calculateTotal();
    });
});

function addToOrder(productId, productName, productPrice) {
    currentProduct = {id: productId, name: productName, price: productPrice};
    document.getElementById('productModalTitle').textContent = productName;
    document.getElementById('modalQuantity').value = 1;
    document.getElementById('modalInstructions').value = '';
    new bootstrap.Modal(document.getElementById('productModal')).show();
}

function changeQuantity(change) {
    const quantityInput = document.getElementById('modalQuantity');
    const newValue = parseInt(quantityInput.value) + change;
    if (newValue >= 1) {
        quantityInput.value = newValue;
    }
}

function confirmAddToOrder() {
    const quantity = parseInt(document.getElementById('modalQuantity').value);
    const instructions = document.getElementById('modalInstructions').value;
    
    const existingIndex = orderItems.findIndex(item => 
        item.id === currentProduct.id && item.instructions === instructions
    );
    
    if (existingIndex >= 0) {
        orderItems[existingIndex].quantity += quantity;
    } else {
        orderItems.push({
            id: currentProduct.id,
            name: currentProduct.name,
            price: currentProduct.price,
            quantity: quantity,
            instructions: instructions
        });
    }
    
    updateOrderDisplay();
    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
}

function removeFromOrder(index) {
    orderItems.splice(index, 1);
    updateOrderDisplay();
}

function updateQuantity(index, change) {
    const newQuantity = orderItems[index].quantity + change;
    if (newQuantity <= 0) {
        removeFromOrder(index);
    } else {
        orderItems[index].quantity = newQuantity;
        updateOrderDisplay();
    }
}

function updateOrderDisplay() {
    const container = document.getElementById('orderItems');
    
    if (orderItems.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Mahsulot tanlanmagan</p>';
        document.getElementById('submitBtn').disabled = true;
    } else {
        let html = '';
        orderItems.forEach((item, index) => {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                    <div class="flex-grow-1">
                        <h6 class="mb-0">${item.name}</h6>
                        ${item.instructions ? '<small class="text-muted">' + item.instructions + '</small>' : ''}
                        <div class="text-primary fw-bold">${(item.price * item.quantity).toLocaleString()} so'm</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="updateQuantity(${index}, -1)">-</button>
                        <span class="mx-2">${item.quantity}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="updateQuantity(${index}, 1)">+</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFromOrder(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="products[${index}][id]" value="${item.id}">
                <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
                <input type="hidden" name="products[${index}][instructions]" value="${item.instructions}">
            `;
        });
        container.innerHTML = html;
        document.getElementById('submitBtn').disabled = false;
    }
    
    calculateTotal();
}

function calculateTotal() {
    const subtotal = orderItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const commission = subtotal * 0.10; // 10% waiter commission
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    
    // Add delivery fee if it's a delivery order
    let deliveryFee = 0;
    const orderType = document.getElementById('selected_order_type').value;
    if (orderType === 'delivery') {
        deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;
        document.getElementById('deliveryFeeDisplay').textContent = deliveryFee.toLocaleString() + ' so\'m';
    }
    
    const total = subtotal + commission + deliveryFee - discount;
    
    document.getElementById('subtotal').textContent = subtotal.toLocaleString() + ' so\'m';
    document.getElementById('commission').textContent = commission.toLocaleString() + ' so\'m';
    document.getElementById('total').textContent = total.toLocaleString() + ' so\'m';
}

// Delivery fee change handler
document.getElementById('delivery_fee').addEventListener('input', calculateTotal);

// Form validation
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const orderType = document.getElementById('selected_order_type').value;
    
    if (orderType !== 'dine_in') {
        const customerName = document.getElementById('customer_name').value;
        const customerPhone = document.getElementById('customer_phone').value;
        
        if (!customerName || !customerPhone) {
            e.preventDefault();
            alert('Mijoz nomi va telefon raqami kiritilishi shart!');
            return false;
        }
        
        if (orderType === 'delivery') {
            const deliveryAddress = document.getElementById('delivery_address').value;
            if (!deliveryAddress) {
                e.preventDefault();
                alert('Yetkazib berish manzili kiritilishi shart!');
                return false;
            }
        }
    }
    
    if (orderItems.length === 0) {
        e.preventDefault();
        alert('Kamida bitta mahsulot tanlang!');
        return false;
    }
});

// Product card hover effects
document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
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
    
    // Initialize order type
    const selectedOrderType = document.querySelector('input[name="order_type"]:checked').value;
    if (selectedOrderType !== 'dine_in') {
        document.getElementById('customerInfo').style.display = 'block';
        if (selectedOrderType === 'delivery') {
            document.getElementById('deliveryInfo').style.display = 'block';
            document.getElementById('deliveryFeeRow').style.display = 'flex';
        }
    }
});
</script>

<style>
.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.sticky-top {
    top: 20px;
}

.btn-check:checked + .btn {
    background-color: var(--bs-primary);
    color: white;
}

.order-type-indicator {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 12px;
}
</style>
@endsection