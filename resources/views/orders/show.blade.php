<!-- resources/views/orders/show.blade.php -->
@extends('layouts.app')

@section('title', 'Buyurtma Ma\'lumotlari')
@section('page-title', 'Buyurtma #' . $order->order_number)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-utensils"></i> Buyurtma Ma'lumotlari</h5>
                <div>
                    @if($order->status !== 'completed')
                    <button class="btn btn-primary btn-sm" onclick="updateOrderStatus()">
                        <i class="fas fa-edit"></i> Holatni O'zgartirish
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Buyurtma №:</th>
                                <td>{{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <th>Mijoz:</th>
                                <td>
                                    <strong>{{ $order->customer->name }}</strong><br>
                                    <small>{{ $order->customer->phone }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Xona:</th>
                                <td>{{ $order->reservation->room->name_uz }}</td>
                            </tr>
                            <tr>
                                <th>Ofitsiant:</th>
                                <td>{{ $order->waiter->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Buyurtma vaqti:</th>
                                <td>{{ $order->order_time->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Holat:</th>
                                <td>
                                    @if($order->status === 'pending')
                                        <span class="badge bg-warning">Kutilmoqda</span>
                                    @elseif($order->status === 'preparing')
                                        <span class="badge bg-info">Tayyorlanmoqda</span>
                                    @elseif($order->status === 'ready')
                                        <span class="badge bg-success">Tayyor</span>
                                    @elseif($order->status === 'served')
                                        <span class="badge bg-primary">Berildi</span>
                                    @else
                                        <span class="badge bg-secondary">Tugallangan</span>
                                    @endif
                                </td>
                            </tr>
                            @if($order->served_time)
                            <tr>
                                <th>Berilgan vaqt:</th>
                                <td>{{ $order->served_time->format('d.m.Y H:i') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Jami summa:</th>
                                <td><strong class="text-success">{{ number_format($order->total_amount) }} so'm</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($order->notes)
                <div class="alert alert-info">
                    <strong><i class="fas fa-comment"></i> Izoh:</strong><br>
                    {{ $order->notes }}
                </div>
                @endif

                <!-- Order Items -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mahsulot</th>
                                <th>Kategoriya</th>
                                <th>Miqdor</th>
                                <th>Narx</th>
                                <th>Jami</th>
                                <th>Holat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name_uz }}</strong>
                                    @if($item->special_instructions)
                                        <br><small class="text-danger">{{ $item->special_instructions }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->product->category->name_uz }}</td>
                                <td>{{ $item->quantity }} dona</td>
                                <td>{{ number_format($item->unit_price) }} so'm</td>
                                <td><strong>{{ number_format($item->total_price) }} so'm</strong></td>
                                <td>
                                    <span class="badge bg-{{ $item->status === 'ready' ? 'success' : 'warning' }}">
                                        {{ $item->status === 'ready' ? 'Tayyor' : 'Tayyorlanmoqda' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calculator"></i> Hisob-kitob</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td>Mahsulotlar:</td>
                        <td class="text-end">{{ number_format($order->subtotal) }} so'm</td>
                    </tr>
                    <tr>
                        <td>Soliq (12%):</td>
                        <td class="text-end">{{ number_format($order->tax_amount) }} so'm</td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <td>Chegirma:</td>
                        <td class="text-end text-success">-{{ number_format($order->discount_amount) }} so'm</td>
                    </tr>
                    @endif
                    <tr class="border-top">
                        <th>Jami:</th>
                        <th class="text-end">{{ number_format($order->total_amount) }} so'm</th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info"></i> Qo'shimcha Ma'lumot</h5>
            </div>
            <div class="card-body">
                <p><strong>Mahsulotlar soni:</strong> {{ $order->items->count() }}</p>
                <p><strong>Jami miqdor:</strong> {{ $order->items->sum('quantity') }} dona</p>
                <p><strong>O'rtacha narx:</strong> {{ number_format($order->total_amount / $order->items->sum('quantity')) }} so'm</p>
                
                @php
                    $totalPrepTime = $order->items->sum(function($item) {
                        return $item->product->preparation_time * $item->quantity;
                    });
                @endphp
                <p><strong>Taxminiy vaqt:</strong> ~{{ $totalPrepTime }} daqiqa</p>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buyurtma Holatini O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <div class="mb-3">
                        <label class="form-label">Yangi Holat</label>
                        <select class="form-control" id="newStatus" required>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Tayyorlanmoqda</option>
                            <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Tayyor</option>
                            <option value="served" {{ $order->status === 'served' ? 'selected' : '' }}>Berildi</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Tugallandi</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" onclick="saveStatus()">Saqlash</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateOrderStatus() {
    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

function saveStatus() {
    const status = document.getElementById('newStatus').value;
    
    fetch(`/orders/{{ $order->id }}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi');
    });
}
</script>
@endsection