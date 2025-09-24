<!-- resources/views/quick-order/index.blade.php -->
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tezkor Buyurtma - {{ config('app.name') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container-fluid {
            padding: 20px;
        }

        .header-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .product-card.selected {
            border-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 3rem;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .product-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .product-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 12px;
            flex-grow: 1;
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: auto;
        }

        .quantity-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
        }

        .quantity-btn:hover {
            background: #667eea;
            color: white;
            transform: scale(1.1);
        }

        .quantity-display {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 10px;
        }

        .cart-summary {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            min-width: 320px;
            max-width: 400px;
            z-index: 1000;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
            max-height: 70vh;
            overflow-y: auto;
        }

        .cart-summary.show {
            transform: translateY(0);
            opacity: 1;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .cart-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: #28a745;
            text-align: center;
            margin: 15px 0;
            padding: 15px;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-radius: 10px;
        }

        .category-tabs {
            margin-bottom: 30px;
        }

        .nav-pills .nav-link {
            border-radius: 25px;
            margin-right: 10px;
            margin-bottom: 10px;
            background: rgba(255, 255, 255, 0.8);
            color: #667eea;
            border: 2px solid transparent;
            transition: all 0.3s;
            font-weight: 500;
        }

        .nav-pills .nav-link.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .nav-pills .nav-link:hover {
            background: rgba(102, 126, 234, 0.1);
            border-color: #667eea;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            border-radius: 25px;
            padding: 10px 25px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }

        .back-btn:hover {
            background: white;
            color: #667eea;
            text-decoration: none;
        }

        .order-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 12px 25px;
            font-weight: 600;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s;
        }

        .order-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .order-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .empty-cart {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
        }

        .loading-spinner {
            display: none;
            text-align: center;
        }

        .badge-category {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(102, 126, 234, 0.9);
            color: white;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 10px;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 15px;
            }
            
            .cart-summary {
                position: static;
                width: 100%;
                margin-top: 20px;
                transform: none;
                opacity: 1;
                border-radius: 15px;
                min-width: auto;
                max-width: none;
            }
            
            .nav-pills .nav-link {
                font-size: 0.9rem;
                padding: 8px 15px;
                margin-right: 5px;
            }

            .product-image {
                height: 160px;
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="header-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0"><i class="fas fa-bolt text-warning"></i> Tezkor Buyurtma</h2>
                        <p class="text-muted mb-0">
                            @if($reservation)
                                {{ $reservation->room->name_uz }} - {{ $reservation->customer->name }}
                            @else
                                Mahsulotlarni tanlang va buyurtma bering
                            @endif
                        </p>
                    </div>
                    <a href="{{ $reservation ? route('reservations.show', $reservation) : route('dashboard') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                </div>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <ul class="nav nav-pills justify-content-center flex-wrap" id="categoryTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-category="all" href="#">
                        <i class="fas fa-star me-2"></i> Barchasi
                    </a>
                </li>
                @foreach($categories as $category)
                <li class="nav-item">
                    <a class="nav-link" data-category="category-{{ $category->id }}" href="#">
                        <i class="fas fa-folder me-2"></i> {{ $category->name_uz }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Products Grid -->
        <div class="products-grid" id="productsGrid">
            @foreach($categories as $category)
                @foreach($category->products as $product)
                <div class="product-card" 
                     data-category="category-{{ $category->id }}" 
                     data-product-id="{{ $product->id }}"
                     data-name="{{ $product->name_uz }}" 
                     data-price="{{ $product->price }}"
                     style="display: flex; flex-direction: column;">
                    
                    <div class="product-image" style="position: relative;">
                        @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name_uz }}">
                        @else
                            <i class="fas fa-utensils"></i>
                        @endif
                        <span class="badge-category">{{ $category->name_uz }}</span>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-title">{{ $product->name_uz }}</div>
                        <div class="product-description">
                            {{ Str::limit($product->description_uz ?? $product->description, 80) }}
                        </div>
                        <div class="product-price">{{ number_format($product->price) }} so'm</div>
                        <div class="quantity-controls">
                            <div class="quantity-btn" onclick="updateQuantity({{ $product->id }}, -1)">-</div>
                            <div class="quantity-display" id="qty-{{ $product->id }}">0</div>
                            <div class="quantity-btn" onclick="updateQuantity({{ $product->id }}, 1)">+</div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endforeach
        </div>

        <!-- Cart Summary (Mobile view) -->
        <div class="cart-summary d-md-none" id="mobileCart">
            <h5><i class="fas fa-shopping-cart"></i> Buyurtma Savati</h5>
            <div id="mobileCartItems" class="empty-cart">
                Hali hech narsa tanlanmagan
            </div>
            <div class="cart-total" id="mobileCartTotal">
                Jami: 0 so'm
            </div>
            
            <div class="mb-3">
                <label for="mobileTableNumber" class="form-label">Stol raqami (ixtiyoriy)</label>
                <input type="text" class="form-control" id="mobileTableNumber" placeholder="Masalan: 5">
            </div>
            
            <div class="mb-3">
                <label for="mobileNotes" class="form-label">Qo'shimcha izoh</label>
                <textarea class="form-control" id="mobileNotes" rows="2" placeholder="Maxsus talablar..."></textarea>
            </div>
            
            <div class="loading-spinner">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Yuborilmoqda...</span>
                </div>
                <p class="mt-2">Buyurtma yuborilmoqda...</p>
            </div>
            
            <button class="order-btn" onclick="placeOrder()">
                <i class="fas fa-check"></i> Buyurtma Berish
            </button>
        </div>
    </div>

    <!-- Cart Summary (Desktop) -->
    <div class="cart-summary d-none d-md-block" id="desktopCart">
        <h5><i class="fas fa-shopping-cart"></i> Buyurtma Savati</h5>
        <div id="desktopCartItems" class="empty-cart">
            Hali hech narsa tanlanmagan
        </div>
        <div class="cart-total" id="desktopCartTotal">
            Jami: 0 so'm
        </div>
        
        <div class="mb-3">
            <label for="desktopTableNumber" class="form-label">Stol raqami (ixtiyoriy)</label>
            <input type="text" class="form-control" id="desktopTableNumber" placeholder="Masalan: 5">
        </div>
        
        <div class="mb-3">
            <label for="desktopNotes" class="form-label">Qo'shimcha izoh</label>
            <textarea class="form-control" id="desktopNotes" rows="2" placeholder="Maxsus talablar..."></textarea>
        </div>
        
        <div class="loading-spinner">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Yuborilmoqda...</span>
            </div>
            <p class="mt-2">Buyurtma yuborilmoqda...</p>
        </div>
        
        <button class="order-btn" onclick="placeOrder()">
            <i class="fas fa-check"></i> Buyurtma Berish
        </button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = {};
        let products = {};

        // Initialize products data
        document.querySelectorAll('.product-card').forEach(card => {
            const id = card.dataset.productId;
            products[id] = {
                name: card.dataset.name,
                price: parseInt(card.dataset.price),
                category: card.dataset.category
            };
        });

        // Category filtering
        document.querySelectorAll('#categoryTabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const category = this.dataset.category;
                
                // Update active tab
                document.querySelectorAll('#categoryTabs .nav-link').forEach(link => {
                    link.classList.remove('active');
                });
                this.classList.add('active');
                
                // Filter products
                document.querySelectorAll('.product-card').forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        function updateQuantity(productId, change) {
            if (!cart[productId]) {
                cart[productId] = 0;
            }
            
            cart[productId] += change;
            
            if (cart[productId] <= 0) {
                delete cart[productId];
                cart[productId] = 0;
            }
            
            // Update display
            document.getElementById(`qty-${productId}`).textContent = cart[productId] || 0;
            
            // Update product card appearance
            const card = document.querySelector(`[data-product-id="${productId}"]`);
            if (cart[productId] > 0) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
            
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartItems = Object.keys(cart).filter(id => cart[id] > 0);
            let subtotal = 0;
            let itemsHtml = '';
            
            if (cartItems.length === 0) {
                itemsHtml = '<div class="empty-cart">Hali hech narsa tanlanmagan</div>';
                document.getElementById('desktopCart').classList.remove('show');
                document.getElementById('mobileCart').classList.remove('show');
            } else {
                cartItems.forEach(id => {
                    const product = products[id];
                    const quantity = cart[id];
                    const itemTotal = product.price * quantity;
                    subtotal += itemTotal;
                    
                    itemsHtml += `
                        <div class="cart-item">
                            <div>
                                <strong>${product.name}</strong><br>
                                <small>${quantity} x ${product.price.toLocaleString()}</small>
                            </div>
                            <div class="text-end">
                                <strong>${itemTotal.toLocaleString()}</strong>
                                <button class="btn btn-sm btn-outline-danger ms-2" onclick="updateQuantity(${id}, -${quantity})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                document.getElementById('desktopCart').classList.add('show');
                document.getElementById('mobileCart').classList.add('show');
            }
            
            const tax = subtotal * 0.12;
            const total = subtotal + tax;
            
            const totalHtml = `
                <div>Jami mahsulotlar: ${subtotal.toLocaleString()} so'm</div>
                <div>Soliq (12%): ${tax.toLocaleString()} so'm</div>
                <hr>
                <div><strong>Umumiy summa: ${total.toLocaleString()} so'm</strong></div>
            `;
            
            // Update both desktop and mobile cart displays
            document.getElementById('desktopCartItems').innerHTML = itemsHtml;
            document.getElementById('mobileCartItems').innerHTML = itemsHtml;
            document.getElementById('desktopCartTotal').innerHTML = totalHtml;
            document.getElementById('mobileCartTotal').innerHTML = totalHtml;
        }

        function placeOrder() {
            const cartItems = Object.keys(cart).filter(id => cart[id] > 0);
            
            if (cartItems.length === 0) {
                alert('Buyurtma berish uchun kamida bitta mahsulot tanlang!');
                return;
            }
            
            const tableNumber = document.getElementById('desktopTableNumber').value || 
                              document.getElementById('mobileTableNumber').value;
            const notes = document.getElementById('desktopNotes').value || 
                         document.getElementById('mobileNotes').value;
            
            const orderData = {
                items: cartItems.map(id => ({
                    product_id: parseInt(id),
                    quantity: cart[id]
                })),
                table_number: tableNumber,
                notes: notes,
                @if($reservation)
                reservation_id: {{ $reservation->id }}
                @endif
            };
            
            // Show loading
            document.querySelectorAll('.loading-spinner').forEach(el => el.style.display = 'block');
            document.querySelectorAll('.order-btn').forEach(btn => btn.disabled = true);
            
            fetch('{{ route("quick-order.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect_url;
                } else {
                    throw new Error('Buyurtma yuborishda xatolik');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Buyurtma yuborishda xatolik yuz berdi. Iltimos, qayta urinib ko\'ring.');
            })
            .finally(() => {
                // Hide loading
                document.querySelectorAll('.loading-spinner').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.order-btn').forEach(btn => btn.disabled = false);
            });
        }

        // Initialize cart display
        updateCartDisplay();
    </script>
</body>
</html>