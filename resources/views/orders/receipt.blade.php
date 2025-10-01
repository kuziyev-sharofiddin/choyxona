<!-- resources/views/orders/receipt.blade.php -->
<div class="receipt">
    <div class="text-center mb-3">
        <h4 class="fw-bold">CHOYXONA</h4>
        <p class="mb-1">Boshqaruv Tizimi</p>
        <p class="mb-1">Tel: +998 90 123 45 67</p>
        <small class="text-muted">{{ now()->format('d.m.Y H:i:s') }}</small>
    </div>
    
    <hr>
    
    <div class="mb-2">
        <strong>BUYURTMA: {{ $order->order_number }}</strong>
    </div>
    
    <!-- Order Type Badge -->
    <div class="mb-2">
        @if($order->order_type === 'dine_in')
            <span class="badge bg-primary">ICHKARIDA OVQATLANISH</span>
        @elseif($order->order_type === 'takeaway')
            <span class="badge bg-success">OLIB KETISH</span>
        @else
            <span class="badge bg-warning">YETKAZIB BERISH</span>
        @endif
    </div>
    
    <!-- Customer Info -->
    <div class="mb-2">
        @if($order->order_type === 'dine_in' && $order->reservation)
            <strong>Mijoz:</strong> {{ $order->customer->name ?? "Mavjud emas" }}<br>
            <strong>Xona:</strong> {{ $order->reservation->room->name_uz  ?? "Mavjud emas" }}<br>
            <strong>Telefon:</strong> {{ $order->customer->phone  ?? "Mavjud emas" }}
        @else
            <strong>Mijoz:</strong> {{ $order->customer_name ?? $order->customer->name  ?? "Mavjud emas" }}<br>
            <strong>Telefon:</strong> {{ $order->customer_phone ?? $order->customer->phone  ?? "Mavjud emas" }}
            @if($order->order_type === 'delivery' && $order->delivery_address)
                <br><strong>Manzil:</strong> {{ $order->delivery_address  ?? "Mavjud emas" }}
            @endif
        @endif
    </div>
    
    <div class="mb-2">
        <strong>Ofitsiant:</strong> {{ $order->waiter->name }}<br>
        <strong>Vaqt:</strong> {{ $order->order_time->format('d.m.Y H:i') }}
        @if($order->served_time)
            <br><strong>Berilgan:</strong> {{ $order->served_time->format('d.m.Y H:i') }}
        @endif
    </div>
    
    <hr>
    
    <!-- Order Items -->
    <div class="mb-3">
        <table class="w-100">
            <thead>
                <tr>
                    <th class="text-start">Mahsulot</th>
                    <th class="text-center">Miqdor</th>
                    <th class="text-end">Summa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="text-start">
                        {{ $item->product->name_uz }}
                        @if($item->special_instructions)
                            <br><small class="text-muted">{{ $item->special_instructions }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $item->quantity }} x {{ number_format($item->unit_price) }}
                    </td>
                    <td class="text-end">{{ number_format($item->total_price) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <hr>
    
    <!-- Totals -->
    <div class="mb-3">
        <div class="d-flex justify-content-between">
            <span>Mahsulotlar:</span>
            <span>{{ number_format($order->subtotal) }} so'm</span>
        </div>
        
        @if($order->order_type === 'dine_in' && $order->waiter_commission > 0)
        <div class="d-flex justify-content-between">
            <span>Xizmat (10%):</span>
            <span>{{ number_format($order->waiter_commission) }} so'm</span>
        </div>
        @endif
        
        @if($order->order_type === 'delivery' && $order->delivery_fee > 0)
        <div class="d-flex justify-content-between">
            <span>Yetkazib berish:</span>
            <span>{{ number_format($order->delivery_fee) }} so'm</span>
        </div>
        @endif
        
        @if($order->discount_amount > 0)
        <div class="d-flex justify-content-between">
            <span>Chegirma:</span>
            <span>-{{ number_format($order->discount_amount) }} so'm</span>
        </div>
        @endif
        
        <div class="border-top pt-2 mt-2">
            <div class="d-flex justify-content-between fw-bold">
                <span>JAMI:</span>
                <span>{{ number_format($order->total_amount) }} so'm</span>
            </div>
        </div>
    </div>
    
    <!-- Payment Info -->
    @if($order->payments->count() > 0)
    <hr>
    <div class="mb-3">
        <strong>TO'LOV MA'LUMOTLARI:</strong>
        @foreach($order->payments as $payment)
        <div class="d-flex justify-content-between">
            <span>
                {{ ucfirst($payment->payment_method) }}
                <small>({{ $payment->payment_time->format('d.m.Y H:i') }})</small>
            </span>
            <span>{{ number_format($payment->amount) }} so'm</span>
        </div>
        @endforeach
        
        @php
            $totalPaid = $order->getTotalPaid();
            $remaining = $order->getRemainingAmount();
        @endphp
        
        <div class="border-top pt-2 mt-2">
            <div class="d-flex justify-content-between">
                <span>To'langan:</span>
                <span class="text-success">{{ number_format($totalPaid) }} so'm</span>
            </div>
            @if($remaining > 0)
            <div class="d-flex justify-content-between">
                <span>Qoldiq:</span>
                <span class="text-danger">{{ number_format($remaining) }} so'm</span>
            </div>
            @else
            <div class="d-flex justify-content-between text-success">
                <span><strong>TO'LANGAN</strong></span>
                <span><strong>✓</strong></span>
            </div>
            @endif
        </div>
    </div>
    @endif
    
    <hr>
    
    <!-- Footer -->
    <div class="text-center">
        <p class="mb-1"><strong>RAHMAT!</strong></p>
        <p class="mb-1">Bizni tanlaganingiz uchun tashakkur</p>
        <small class="text-muted">Yana tashrif buyuring</small>
    </div>
    
    @if($order->notes)
    <hr>
    <div class="mb-2">
        <strong>Izoh:</strong> {{ $order->notes }}
    </div>
    @endif
    
    <div class="text-center mt-3">
        <small class="text-muted">
            Powered by Choyxona Management System<br>
            {{ now()->format('Y') }}
        </small>
    </div>
</div>

<style>
.receipt {
    font-family: 'Courier New', monospace;
    max-width: 300px;
    margin: 0 auto;
    padding: 20px;
    background: white;
    font-size: 12px;
    line-height: 1.4;
}

.receipt table {
    width: 100%;
    font-size: 11px;
}

.receipt th,
.receipt td {
    padding: 2px;
    vertical-align: top;
}

.receipt hr {
    border: none;
    border-top: 1px dashed #333;
    margin: 10px 0;
}

.receipt .border-top {
    border-top: 1px dashed #333 !important;
}

.receipt .badge {
    display: inline-block;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    border-radius: 3px;
    color: white;
}

.receipt .badge.bg-primary { background-color: #0066cc !important; }
.receipt .badge.bg-success { background-color: #28a745 !important; }
.receipt .badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }

.receipt .text-success { color: #28a745 !important; }
.receipt .text-danger { color: #dc3545 !important; }
.receipt .text-muted { color: #666 !important; }

.receipt .fw-bold { font-weight: bold; }
.receipt .text-center { text-align: center; }
.receipt .text-start { text-align: left; }
.receipt .text-end { text-align: right; }

.receipt .d-flex {
    display: flex;
    align-items: center;
}

.receipt .justify-content-between {
    justify-content: space-between;
}

.receipt .mb-1 { margin-bottom: 5px; }
.receipt .mb-2 { margin-bottom: 10px; }
.receipt .mb-3 { margin-bottom: 15px; }
.receipt .mt-2 { margin-top: 10px; }
.receipt .mt-3 { margin-top: 15px; }
.receipt .pt-2 { padding-top: 10px; }

/* Print specific styles */
@media print {
    .receipt {
        font-size: 10px;
        max-width: 280px;
        padding: 10px;
        margin: 0;
    }
    
    body {
        margin: 0;
        padding: 0;
    }
}

/* Thermal printer optimization */
@media print {
    .receipt {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}
</style>