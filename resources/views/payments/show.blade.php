@extends('layouts.app')

@section('title', 'To\'lov Ma\'lumotlari')
@section('page-title', 'To\'lov #' . $payment->payment_number)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card"></i> To'lov Ma'lumotlari
                    @if($payment->order_id)
                    <span class="badge bg-success">Buyurtma To'lovi</span>
                    @else
                    <span class="badge bg-info">Rezervatsiya To'lovi</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>To'lov №:</th>
                                <td>{{ $payment->payment_number }}</td>
                            </tr>
                            <tr>
                                <th>Mijoz:</th>
                                <td>
                                    <strong>{{ $payment->getCustomer()->name }}</strong><br>
                                    <small>{{ $payment->getCustomer()->phone }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>To'lov manbai:</th>
                                <td>
                                    @if($payment->reservation_id)
                                    <a href="{{ route('reservations.show', $payment->reservation) }}">
                                        {{ $payment->getSourceDescription() }}
                                    </a>
                                    <br><small class="text-muted">Xona: {{ $payment->reservation->room->name_uz }}</small>
                                    @elseif($payment->order_id)
                                    <a href="{{ route('orders.show', $payment->order) }}">
                                        {{ $payment->getSourceDescription() }}
                                    </a>
                                    <br><small class="text-muted">Xona mavjud emas</small>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Summa:</th>
                                <td><strong class="text-success">{{ number_format($payment->amount) }} so'm</strong></td>
                            </tr>
                            <tr>
                                <th>To'lov usuli:</th>
                                <td>
                                    @if($payment->payment_method === 'cash')
                                    <span class="badge bg-success">Naqd pul</span>
                                    @elseif($payment->payment_method === 'card')
                                    <span class="badge bg-info">Plastik karta</span>
                                    @else
                                    <span class="badge bg-warning">Bank o'tkazmasi</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kassir:</th>
                                <td>{{ $payment->cashier->name }}</td>
                            </tr>
                            <tr>
                                <th>To'lov vaqti:</th>
                                <td>{{ $payment->payment_time->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Holat:</th>
                                <td>
                                    @if($payment->status === 'completed')
                                    <span class="badge bg-secondary">Tugallangan</span>
                                    @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning">Jarayonda</span>
                                    @else
                                    <span class="badge bg-danger">Xato</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($payment->notes)
                <div class="alert alert-info mt-3">
                    <strong><i class="fas fa-comment"></i> Izoh:</strong><br>
                    {{ $payment->notes }}
                </div>
                @endif

                <!-- Order Details if it's an order payment -->
                @if($payment->order_id && $payment->order)
                <div class="mt-4">
                    <h6>Buyurtma Tafsilotlari:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mahsulot</th>
                                    <th>Miqdor</th>
                                    <th>Narx</th>
                                    <th>Jami</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name_uz }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price) }} so'm</td>
                                    <td>{{ number_format($item->total_price) }} so'm</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="3">Jami buyurtma:</th>
                                    <th>{{ number_format($payment->order->total_amount) }} so'm</th>
                                </tr>
                                @if($payment->order->getTotalPaid() != $payment->order->total_amount)
                                <tr class="table-warning">
                                    <th colspan="3">Jami to'langan:</th>
                                    <th>{{ number_format($payment->order->getTotalPaid()) }} so'm</th>
                                </tr>
                                <tr class="table-danger">
                                    <th colspan="3">Qoldiq:</th>
                                    <th>{{ number_format($payment->order->getRemainingAmount()) }} so'm</th>
                                </tr>
                                @endif
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Receipt -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-receipt"></i> Chek</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h4>CHOYXONA</h4>
                    <p class="text-muted">To'lov Cheki</p>
                </div>

                <hr>
                <p><strong>Chek №:</strong> {{ $payment->payment_number }}</p>
                <p><strong>Sana:</strong> {{ $payment->payment_time->format('d.m.Y H:i') }}</p>
                <p><strong>Mijoz:</strong> {{ $payment->getCustomer()->name }}</p>
                @if (isset($payment->order))
                <p><strong>Buyurtma:</strong> {{ $payment->order->order_number }}</p>
                @endif
                @if(isset($payment->reservation_id))
                <p><strong>Xona:</strong> {{ $payment->reservation->room->name_uz }}</p>
                @else
                <p>Xona mavjud emas</p>
                @endif
                <hr>

                <div class="d-flex justify-content-between">
                    <strong>To'lov summasi:</strong>
                    <strong>{{ number_format($payment->amount) }} so'm</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>To'lov usuli:</span>
                    <span>
                        @if($payment->payment_method === 'cash') Naqd
                        @elseif($payment->payment_method === 'card') Karta
                        @else O'tkazma @endif
                    </span>
                </div>

                <hr>
                <p class="text-center small text-muted">Xaridingiz uchun rahmat!</p>

                <div class="d-grid gap-2 mt-3">
                    @php
                    $receiptRoute = isset($payment->reservation)
                    ? route('reservations.receipt', $payment->reservation)
                    : (isset($payment->order) ? route('orders.receipt', $payment->order) : null);
                    @endphp

                    @if($receiptRoute)
                    <a href="{{ $receiptRoute }}"
                        target="_blank"
                        class="btn btn-success">
                        <i class="fas fa-receipt me-2"></i>Chekni Chop Etish
                    </a>
                    @endif

                    @if($payment->status === 'pending')
                    <form action="{{ route('payments.process', $payment) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-check"></i> To'lovni Tasdiqlash
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection