<!-- resources/views/payments/show.blade.php -->
@extends('layouts.app')

@section('title', 'To\'lov Ma\'lumotlari')
@section('page-title', 'To\'lov #' . $payment->payment_number)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-credit-card"></i> To'lov Ma'lumotlari</h5>
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
                                    <strong>{{ $payment->reservation->customer->name }}</strong><br>
                                    <small>{{ $payment->reservation->customer->phone }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Rezervatsiya:</th>
                                <td>
                                    <a href="{{ route('reservations.show', $payment->reservation) }}">
                                        {{ $payment->reservation->reservation_number }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Xona:</th>
                                <td>{{ $payment->reservation->room->name_uz }}</td>
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
                                        <span class="badge bg-success">Tugallangan</span>
                                    @elseif($payment->status === 'pending')
                                        <span class="badge bg-warning">Kutilmoqda</span>
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
            </div>
        </div>
    </div>

    <div class="col-md-4">
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
                <p><strong>Mijoz:</strong> {{ $payment->reservation->customer->name }}</p>
                <p><strong>Xona:</strong> {{ $payment->reservation->room->name_uz }}</p>
                <hr>
                
                <div class="d-flex justify-content-between">
                    <strong>Jami summa:</strong>
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
                <p class="text-center small text-muted">Rahmat!</p>
                
                <div class="d-grid gap-2 mt-3">
                    <button onclick="window.print()" class="btn btn-success">
                        <i class="fas fa-print"></i> Chekni Chop Etish
                    </button><li><a class="dropdown-item" href="{{ route('reservations.receipt', $payment->reservation) }}" target="_blank">
    <i class="fas fa-receipt me-2"></i>Chekni Chop Etishs
</a></li>
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