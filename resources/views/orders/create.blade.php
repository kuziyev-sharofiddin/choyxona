@extends('layouts.app')

@section('title', 'Yangi Buyurtma')
@section('page-title', 'Yangi Buyurtma Yaratish')

@section('content')
<div class="row">
    <!-- Reservation Info -->
    @if($reservation)
    <div class="col-12 mb-3">
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
                    <input type="hidden" name="reservation_id" value="{{ $reservation->id ?? '' }}">
                    
                    <div id="orderItems">
                        <p class="text-muted text-center">Mahsulot tanlanmagan</p>
                    </div>

                    <hr>

                    <!-- Order Totals -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Mahsulotlar:</span>
                            <span id="subtotal">0 so'm</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Xizmat haqi (10%):</span>
                            <span id="serviceCharge">0 so'm</span>
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
                        <a href="{{ route('reservations.show', $reservation ?? '') }}" class="btn btn-secondary">
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
const SERVICE_CHARGE_RATE = 0.10; // 10% xizmat haqi

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
    const serviceCharge = subtotal * SERVICE_CHARGE_RATE; // 10% xizmat haqi
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const total = subtotal + serviceCharge - discount;
    
    document.getElementById('subtotal').textContent = subtotal.toLocaleString() + ' so\'m';
    document.getElementById('serviceCharge').textContent = serviceCharge.toLocaleString() + ' so\'m';
    document.getElementById('total').textContent = total.toLocaleString() + ' so\'m';
}

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
</style>
@endsection