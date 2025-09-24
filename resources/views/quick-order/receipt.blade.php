
<!-- resources/views/quick-order/receipt.blade.php -->
@extends('layouts.app')

@section('title', 'Buyurtma Cheki')
@section('page-title', 'Buyurtma Muvaffaqiyatli Yaratildi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Success Message -->
        <div class="alert alert-success text-center">
            <i class="fas fa-check-circle fa-3x mb-3"></i>
            <h4>Buyurtma Muvaffaqiyatli Qabul Qilindi!</h4>
            <p class="mb-0">Buyurtma raqami: <strong>{{ $order->order_number }}</strong></p>
        </div>

        <!-- Receipt Card -->
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h5 class="mb-0"><i class="fas fa-receipt"></i> Buyurtma Cheki</h5>
            </div>
            <div class="card-body" id="receiptContent">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h3>CHOYXONA BOSHQARUV TIZIMI</h3>
                    <p class="text-muted">Tezkor Buyurtma Cheki</p>
                    <hr>
                </div>

                <!-- Order Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <th>Buyurtma №:</th>
                                <td>{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <th>Sana:</th>
                                <td>{{ $order->order_time->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Mijoz:</th>
                                <td>{{ $order->customer->name }}</td>
                            </tr>
                            @if($order->table_number)
                            <tr>
                                <th>Stol:</th>
                                <td>{{ $order->table_number }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            @if($order->reservation)
                            <tr>
                                <th>Xona:</th>
                                <td>{{ $order->reservation->room->name_uz }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Ofitsiant:</th>
                                <td>{{ $order->waiter->name }}</td>
                            </tr>
                            <tr>
                                <th>Holat:</th>
                                <td>
                                    <span class="badge bg-warning">Kutilmoqda</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Taxminiy vaqt:</th>
                                <td>15-20 daqiqa</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="table-responsive mb-4">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Mahsulot</th>
                                <th>Kategoriya</th>
                                <th class="text-center">Miqdor</th>
                                <th class="text-end">Narx</th>
                                <th class="text-end">Jami</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product->name_uz }}</strong>
                                    @if($item->special_instructions)
                                        <br><small class="text-danger">{{ $item->special_instructions }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->product->category->name_uz }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unit_price) }}</td>
                                <td class="text-end">{{ number_format($item->total_price) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end"><strong>Jami mahsulotlar:</strong></td>
                                <td class="text-end"><strong>{{ number_format($order->subtotal) }} so'm</strong></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Soliq (12%):</strong></td>
                                <td class="text-end"><strong>{{ number_format($order->tax_amount) }} so'm</strong></td>
                            </tr>
                            <tr class="table-success">
                                <td colspan="5" class="text-end"><strong>UMUMIY SUMMA:</strong></td>
                                <td class="text-end"><strong>{{ number_format($order->total_amount) }} so'm</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($order->notes)
                <div class="alert alert-info">
                    <strong><i class="fas fa-comment"></i> Qo'shimcha izoh:</strong><br>
                    {{ $order->notes }}
                </div>
                @endif

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-muted">Xizmatimizdan foydalanganingiz uchun rahmat!</p>
                    <p class="small">Savollar bo'yicha: +998 90 123 45 67</p>
                </div>
            </div>

            <div class="card-footer text-center">
                <div class="btn-group">
                    <a href="{{ route('quick-order.print', $order) }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-print"></i> Chop Etish
                    </a>
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="fas fa-download"></i> Saqlash
                    </button>
                    @if($order->reservation)
                        <a href="{{ route('reservations.show', $order->reservation) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Rezervatsiyaga Qaytish
                        </a>
                    @endif
                    <a href="{{ route('quick-order.index') }}" class="btn btn-warning">
                        <i class="fas fa-plus"></i> Yangi Buyurtma
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-home"></i> Bosh Sahifa
                    </a>
                </div>
            </div>
        </div>

        <!-- Order Status Timeline -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Buyurtma Holati</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item completed">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6>Buyurtma Qabul Qilindi</h6>
                            <small>{{ $order->order_time->format('d.m.Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6>Tayyorlanmoqda</h6>
                            <small>Jarayonda...</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-light"></div>
                        <div class="timeline-content">
                            <h6>Tayyor</h6>
                            <small>Kutilmoqda</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-light"></div>
                        <div class="timeline-content">
                            <h6>Yetkazildi</h6>
                            <small>Kutilmoqda</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    height: 100%;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-item.completed .timeline-content h6 {
    color: #28a745;
}

.timeline-marker {
    position: absolute;
    left: -24px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

@media print {
    .card-footer, .btn-group, .timeline {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<script>
// Auto refresh order status every 30 seconds
setInterval(() => {
    // Here you could add AJAX call to check order status
    console.log('Checking order status...');
}, 30000);
</script>
@endsection