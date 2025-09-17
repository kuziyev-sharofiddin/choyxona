@extends('layouts.app')

@section('title', 'Yangi Buyurtma')
@section('page-title', 'Yangi Buyurtma Yaratish')

@section('content')
<!-- Formni butun content atrofida ochish -->
<form action="{{ route('store_order_by_type') }}" method="POST" id="orderForm">
    @csrf
    <input type="hidden" name="reservation_id" id="selected_reservation_id" value="{{ $reservation->id ?? '' }}">
    
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

        <!-- Reservation Selection (for dine-in only) -->
        <div class="col-12 mb-3" id="reservationSelection" style="display: {{ $orderType === 'dine_in' ? 'block' : 'none' }};">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Rezervatsiya Tanlash</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="reservation_select" class="form-label">Rezervatsiyani tanlang *</label>
                        <select class="form-control" id="reservation_select" name="reservation_id">
                            <option value="">-- Rezervatsiyani tanlang --</option>
                            @foreach(\App\Models\Reservation::whereIn('status', ['checked_in', 'confirmed'])->with(['customer', 'room'])->get() as $res)
                                <option value="{{ $res->id }}" {{ (isset($reservation) && $reservation->id == $res->id) ? 'selected' : '' }}>
                                    № {{ $res->reservation_number }} - {{ $res->customer->name }} - {{ $res->room->name_uz }} ({{ $res->guest_count }} kishi)
                                </option>
                                @endforeach
                        </select>
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
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">Telefon *</label>
                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
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

        <!-- Selected Reservation Info (for dine-in only) -->
        <div class="col-12 mb-3" id="selectedReservationInfo" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Tanlangan Rezervatsiya Ma'lumotlari</h6>
                </div>
                <div class="card-body" id="selectedReservationDetails">
                    <!-- Selected reservation details will be populated here -->
                </div>
            </div>
        </div>

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
                            <span id="commissionLabel">Ofitsiant (0%):</span>
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
                </div>
            </div>
        </div>
    </div>
</form>

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

// Reservation data for displaying details
const reservationsData = @json($reservations ?? []);

// Order type change handler
// Order type change handler - tuzatilgan versiya
document.querySelectorAll('input[name="order_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const orderType = this.value;
        console.log('Order type changed to:', orderType);
        
        // Elements
        const customerInfo = document.getElementById('customerInfo');
        const deliveryInfo = document.getElementById('deliveryInfo');
        const deliveryFeeRow = document.getElementById('deliveryFeeRow');
        const reservationSelection = document.getElementById('reservationSelection');
        const selectedReservationInfo = document.getElementById('selectedReservationInfo');
        
        // Form elements
        const customerName = document.getElementById('customer_name');
        const customerPhone = document.getElementById('customer_phone');
        const deliveryAddress = document.getElementById('delivery_address');
        
        // Reset delivery fee input
        document.getElementById('delivery_fee').value = '10000';
        
        if (orderType === 'dine_in') {
            // Dine-in: faqat rezervatsiya kerak
            customerInfo.style.display = 'none';
            deliveryInfo.style.display = 'none';
            deliveryFeeRow.style.display = 'none';
            reservationSelection.style.display = 'block';
            
            // Remove required attributes
            customerName.removeAttribute('required');
            customerPhone.removeAttribute('required');
            deliveryAddress.removeAttribute('required');
            
            const currentReservation = document.getElementById('reservation_select').value;
            document.getElementById('selected_reservation_id').value = currentReservation;
            
        } else if (orderType === 'takeaway') {
            // Takeaway: mijoz ma'lumotlari kerak, delivery fee yo'q
            customerInfo.style.display = 'block';
            deliveryInfo.style.display = 'none';
            deliveryFeeRow.style.display = 'none'; // Takeaway uchun delivery fee qatori yashirin
            reservationSelection.style.display = 'none';
            selectedReservationInfo.style.display = 'none';
            
            // Customer info required
            customerName.setAttribute('required', 'required');
            customerPhone.setAttribute('required', 'required');
            deliveryAddress.removeAttribute('required');
            
        } else if (orderType === 'delivery') {
            // Delivery: mijoz ma'lumotlari va manzil kerak, delivery fee ko'rinadi
            customerInfo.style.display = 'block';
            deliveryInfo.style.display = 'block';
            deliveryFeeRow.style.display = 'flex'; // Delivery uchun delivery fee qatori ko'rinadi
            reservationSelection.style.display = 'none';
            selectedReservationInfo.style.display = 'none';
            
            // All customer info required
            customerName.setAttribute('required', 'required');
            customerPhone.setAttribute('required', 'required');
            deliveryAddress.setAttribute('required', 'required');
        }
        
        // Update total calculation
        calculateTotal();
    });
});

// Reservation selection change handler
document.getElementById('reservation_select').addEventListener('change', function() {
    const reservationId = this.value;
    document.getElementById('selected_reservation_id').value = reservationId;
    
    const selectedReservationInfo = document.getElementById('selectedReservationInfo');
    const selectedReservationDetails = document.getElementById('selectedReservationDetails');
    
    if (reservationId) {
        // Find reservation data
        const reservation = reservationsData.find(r => r.id == reservationId);
        if (reservation) {
            selectedReservationDetails.innerHTML = `
                <div class="row">
                    <div class="col-md-3">
                        <strong>Rezervatsiya №:</strong> ${reservation.reservation_number}
                    </div>
                    <div class="col-md-3">
                        <strong>Mijoz:</strong> ${reservation.customer.name}
                    </div>
                    <div class="col-md-3">
                        <strong>Xona:</strong> ${reservation.room.name_uz}
                    </div>
                    <div class="col-md-3">
                        <strong>Mehmonlar:</strong> ${reservation.guest_count} kishi
                    </div>
                </div>
            `;
            selectedReservationInfo.style.display = 'block';
        }
    } else {
        selectedReservationInfo.style.display = 'none';
    }
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
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    
    // Get current order type
    const orderTypeRadio = document.querySelector('input[name="order_type"]:checked');
    const orderType = orderTypeRadio ? orderTypeRadio.value : 'dine_in';
    
    // Commission calculation
    let commission = 0;
    let commissionPercentage = '0%';
    
    if (orderType === 'dine_in') {
        commission = subtotal * 0.10; // 10% for dine-in
        commissionPercentage = '10%';
    } else if (orderType === 'takeaway') {
        commission = 0; // 0% waiter commission for takeaway
        commissionPercentage = '0%';
    } else if (orderType === 'delivery') {
        commission = 0; // 0% commission for delivery
        commissionPercentage = '0%';
    }
    
    // Delivery fee calculation
    let deliveryFee = 0;
    if (orderType === 'delivery') {
        deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;
        document.getElementById('deliveryFeeDisplay').textContent = deliveryFee.toLocaleString() + ' so\'m';
    }
    
    // Calculate total
    const total = subtotal + commission + deliveryFee - discount;
    
    // Update display
    document.getElementById('commissionLabel').textContent = `Ofitsiant (${commissionPercentage}):`;
    document.getElementById('subtotal').textContent = subtotal.toLocaleString() + ' so\'m';
    document.getElementById('commission').textContent = commission.toLocaleString() + ' so\'m';
    document.getElementById('total').textContent = total.toLocaleString() + ' so\'m';
}

// Delivery fee change handler
document.getElementById('delivery_fee').addEventListener('input', calculateTotal);

// Form validation - removed since HTML5 validation will handle it now
document.getElementById('orderForm').addEventListener('submit', function(e) {
    const orderTypeRadio = document.querySelector('input[name="order_type"]:checked');
    const orderType = orderTypeRadio ? orderTypeRadio.value : 'dine_in';
    
    // Only validate reservation for dine-in orders
    if (orderType === 'dine_in') {
        const reservationId = document.getElementById('selected_reservation_id').value;
        if (!reservationId) {
            e.preventDefault();
            alert('Rezervatsiya tanlanishi shart!');
            return false;
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
    
    // Initialize order type and trigger change event
    const checkedRadio = document.querySelector('input[name="order_type"]:checked');
    if (checkedRadio) {
        checkedRadio.dispatchEvent(new Event('change'));
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