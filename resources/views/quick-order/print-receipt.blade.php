<!-- resources/views/quick-order/print-receipt.blade.php -->
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buyurtma Cheki #{{ $order->order_number }}</title>
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
            background: white;
            width: 300px;
            margin: 0 auto;
        }

        .receipt {
            padding: 10px;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10px;
            margin-bottom: 2px;
        }

        .info-section {
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .info-row strong {
            min-width: 120px;
        }

        .items-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .items-header {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            font-weight: bold;
        }

        .item-row {
            padding: 3px 0;
            border-bottom: 1px dotted #666;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .totals-section {
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-bottom: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .barcode {
            text-align: center;
            font-family: 'Libre Barcode 128', cursive;
            font-size: 24px;
            margin: 10px 0;
        }

        .notes {
            background: #f9f9f9;
            padding: 8px;
            margin: 10px 0;
            border: 1px dashed #666;
        }

        @media print {
            body {
                width: 80mm;
                margin: 0;
            }
            
            .receipt {
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>CHOYXONA</h1>
            <p>Boshqaruv Tizimi</p>
            <p>Tel: +998 90 123 45 67</p>
            <p>Manzil: Toshkent sh., Chilonzor t.</p>
        </div>

        <!-- Order Info -->
        <div class="info-section">
            <div class="info-row">
                <strong>BUYURTMA:</strong>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <strong>SANA:</strong>
                <span>{{ $order->order_time->format('d.m.Y H:i') }}</span>
            </div>
            <div class="info-row">
                <strong>MIJOZ:</strong>
                <span>{{ $order->customer->name }}</span>
            </div>
            @if($order->reservation)
            <div class="info-row">
                <strong>XONA:</strong>
                <span>{{ $order->reservation->room->name_uz }}</span>
            </div>
            @endif
            @if($order->table_number)
            <div class="info-row">
                <strong>STOL:</strong>
                <span>{{ $order->table_number }}</span>
            </div>
            @endif
            <div class="info-row">
                <strong>OFITSIANT:</strong>
                <span>{{ $order->waiter->name }}</span>
            </div>
        </div>

        <!-- Items -->
        <div class="items-table">
            <div class="items-header">
                BUYURTMA TAFSILOTLARI
            </div>
            
            @foreach($order->items as $item)
            <div class="item-row">
                <div class="item-name">{{ $item->product->name_uz }}</div>
                <div class="item-details">
                    <span>{{ $item->quantity }} x {{ number_format($item->unit_price) }}</span>
                    <span>{{ number_format($item->total_price) }} so'm</span>
                </div>
                @if($item->special_instructions)
                <div style="font-size: 9px; color: #666; margin-top: 2px;">
                    * {{ $item->special_instructions }}
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <span>Jami mahsulotlar:</span>
                <span>{{ number_format($order->subtotal) }} so'm</span>
            </div>
            <div class="total-row">
                <span>Soliq (12%):</span>
                <span>{{ number_format($order->tax_amount) }} so'm</span>
            </div>
            <div class="total-row final">
                <span>UMUMIY SUMMA:</span>
                <span>{{ number_format($order->total_amount) }} so'm</span>
            </div>
        </div>

        @if($order->notes)
        <div class="notes">
            <strong>IZOH:</strong><br>
            {{ $order->notes }}
        </div>
        @endif

        <!-- Barcode -->
        <div class="barcode">
            {{ $order->order_number }}
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>XIZMATIMIZDAN FOYDALANGANINGIZ UCHUN RAHMAT!</p>
            <p>Tayyorlash vaqti: 15-20 daqiqa</p>
            <p>Savollar: +998 90 123 45 67</p>
            <p style="margin-top: 10px;">{{ now()->format('d.m.Y H:i:s') }}</p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
        
        // Close window after printing
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>