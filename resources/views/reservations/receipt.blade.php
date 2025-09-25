<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chek - {{ $reservation->reservation_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            padding: 10px;
        }

        .receipt {
            max-width: 300px;
            margin: 0 auto;
            background: white;
            padding: 15px;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            margin: 2px 0;
        }

        .section {
            margin-bottom: 12px;
            border-bottom: 1px dashed #666;
            padding-bottom: 8px;
        }

        .section:last-child {
            border-bottom: none;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .row.bold {
            font-weight: bold;
        }

        .row.total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 8px;
        }

        .item {
            margin: 5px 0;
        }

        .item-name {
            font-weight: bold;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .center {
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            border-top: 2px solid #000;
            padding-top: 10px;
            font-size: 10px;
        }

        .thank-you {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }

        @media print {
            body {
                padding: 0;
            }

            .receipt {
                border: none;
                box-shadow: none;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>AKA-UKALAR</h1>
            <p>Boshqaruv Tizimi</p>
            <p>Tel: +998 91 656 75 80</p>
            <p>Tel: +998 91 656 75 84</p>
            <p>Farg'ona, Boltako'l</p>
        </div>

        <!-- Receipt Info -->
        <div class="section">
            <div class="row">
                <span>Chek №:</span>
                <span>{{ $reservation->reservation_number }}</span>
            </div>
            <div class="row">
                <span>Sana:</span>
                <span>{{ $reservation->created_at->format('d.m.Y H:i') }}</span>
            </div>
            <div class="row">
                <span>Kassir:</span>
                <span>{{ $reservation->waiter->name }}</span>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="section">
            <div class="row bold">
                <span>MIJOZ MA'LUMOTLARI</span>
            </div>
            <div class="row">
                <span>Ism:</span>
                <span>{{ $reservation->customer->name }}</span>
            </div>
            <div class="row">
                <span>Tel:</span>
                <span>{{ $reservation->customer->phone }}</span>
            </div>
        </div>

        <!-- Reservation Info -->
        <div class="section">
            <div class="row bold">
                <span>REZERVATSIYA MA'LUMOTLARI</span>
            </div>
            <div class="row">
                <span>Xona:</span>
                <span>{{ $reservation->room->name_uz }}</span>
            </div>
            <div class="row">
                <span>Vaqt:</span>
                <span>{{ $reservation->reservation_date->format('d.m.Y H:i') }}</span>
            </div>
            <div class="row">
                <span>Muddat:</span>
                <span>{{ $reservation->getDuration() }} soat</span>
            </div>
            <div class="row">
                <span>Mehmonlar:</span>
                <span>{{ $reservation->guest_count }} kishi</span>
            </div>
            <div class="row">
                <span>Xona narxi:</span>
                <span>{{ number_format($reservation->room_charge) }} so'm</span>
            </div>
        </div>

        <!-- Orders -->
        @if($reservation->orders->count() > 0)
        <div class="section">
            <div class="row bold">
                <span>BUYURTMA MAHSULOTLARI</span>
            </div>

            @foreach($reservation->orders as $order)
            @foreach($order->items as $item)
            <div class="item">
                <div class="item-name">{{ $item->product->name_uz }}</div>
                <div class="item-details">
                    <span>{{ $item->quantity }} x {{ number_format($item->unit_price) }}</span>
                    <span>{{ number_format($item->total_price) }} so'm</span>
                </div>
                @if($item->special_instructions)
                <div style="font-size: 10px; font-style: italic; color: #666;">
                    * {{ $item->special_instructions }}
                </div>
                @endif
            </div>
            @endforeach

            <!-- Order Summary -->
            <div style="margin: 8px 0; font-size: 11px;">
                <div class="row">
                    <span>Mahsulotlar:</span>
                    <span>{{ number_format($order->subtotal) }} so'm</span>
                </div>
                <div class="row">
                    <span>Xizmat xaqi (10%):</span>
                    <span>{{ number_format($order->waiter_commission ?? $order->subtotal * 0.10) }} so'm</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="row">
                    <span>Chegirma:</span>
                    <span>-{{ number_format($order->discount_amount) }} so'm</span>
                </div>
                @endif
                <div class="row bold">
                    <span>Buyurtma jami:</span>
                    <span>{{ number_format($order->total_amount) }} so'm</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Total -->
        <div class="section">
            <div class="row total">
                <span>UMUMIY SUMMA:</span>
                <span>{{ number_format($reservation->getTotalAmount()) }} so'm</span>
            </div>
        </div>

        <!-- Payment Info -->
        @if($reservation->payments->count() > 0)
        <div class="section">
            <div class="row bold">
                <span>TO'LOV MA'LUMOTLARI</span>
            </div>
            @foreach($reservation->payments as $payment)
            <div class="row">
                <span>{{ $payment->payment_method === 'cash' ? 'Naqd' : ($payment->payment_method === 'card' ? 'Karta' : 'O\'tkazma') }}:</span>
                <span>{{ number_format($payment->amount) }} so'm</span>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="thank-you">RAHMAT!</div>
            <p>Bizni tanlaganingiz uchun tashakkur</p>
            <p>Yana tashrif buyuring!</p>
            <p style="margin-top: 10px; font-size: 9px;">
                Chek raqami: {{ $reservation->id }}<br>
                Chop etilgan: {{ now()->format('d.m.Y H:i:s') }}
            </p>
        </div>
    </div>

    <!-- Print Buttons (hidden when printing) -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> Chop Etish
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin: 5px; cursor: pointer;">
            <i class="fas fa-times"></i> Yopish
        </button>
        <button onclick="downloadPDF()" style="background: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 5px; margin: 5px; cursor: pointer;">
            <i class="fas fa-download"></i> PDF Yuklab Olish
        </button>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }

        function downloadPDF() {
            // This would integrate with a PDF library
            alert('PDF funksiyasi keyingi versiyada qo\'shiladi');
        }

        // Prevent accidental navigation away
        window.addEventListener('beforeunload', function(e) {
            if (!window.printed) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Track if printed
        window.addEventListener('afterprint', function() {
            window.printed = true;
        });
    </script>
</body>

</html>