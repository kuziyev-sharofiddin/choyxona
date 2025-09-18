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
                                <td>{{ $order->reservation->room->name_uz  ?? "Ma'lumot yo'q"  }}</td>
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
                                    @elseif($order->status === 'completed')
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
                    @if($order->order_type === 'takeaway')
                    <tr>
                        <td>Ofitsiant komissiyasi (10%):</td>
                        <td class="text-end">{{ number_format($order->waiter_commission ?? $order->subtotal * 0.10) }} so'm</td>
                    </tr>
                    @elseif ($order->order_type === 'delivery')
                    <tr>
                        <td>Yetkazib berish xizmati:</td>
                        <td class="text-end">{{ number_format($order->delivery_fee) }} so'm</td>
                    </tr>
                    @endif
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
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Buyurtma Holatini O'zgartirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    @csrf
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">Yangi Holat</label>
                        <select class="form-select" id="newStatus" required>
                            <option value="">Holatni tanlang...</option>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Tugallandi</option>
                        </select>
                    </div>
                    <div id="loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Yuklanmoqda...</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" onclick="saveStatus()" id="saveBtn">Saqlash</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function updateOrderStatus() {
        // Modal ko'rsatish
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        statusModal.show();
    }

    function saveStatus() {
    const status = document.getElementById('newStatus').value;
    const saveBtn = document.getElementById('saveBtn');
    const loading = document.getElementById('loading');
    
    // Debug: consolega log yozish
    console.log('Selected status:', status);
    console.log('Order ID:', {{ $order->id }});
    console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Validatsiya
    if (!status) {
        alert('Iltimos, yangi holatni tanlang!');
        return;
    }
    
    // Loading ko'rsatish
    saveBtn.disabled = true;
    if (loading) loading.style.display = 'block';
    
    const url = `/orders/{{ $order->id }}/status`;
    console.log('Request URL:', url);
    
    const requestData = {
        status: status
    };
    console.log('Request data:', requestData);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Response text-ini olish (debug uchun)
        return response.text().then(text => {
            console.log('Raw response:', text);
            
            try {
                const data = JSON.parse(text);
                return { data, status: response.status, ok: response.ok };
            } catch (e) {
                console.error('JSON parse error:', e);
                throw new Error('Server JSON emas, HTML qaytardi: ' + text.substring(0, 200));
            }
        });
    })
    .then(({ data, status, ok }) => {
        console.log('Parsed response data:', data);
        
        if (!ok) {
            throw new Error(`HTTP error! status: ${status}`);
        }
        
        if (data.success) {
            alert('Holat muvaffaqiyatli o\'zgartirildi!');
            
            // Modal yopish
            const modal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
            if (modal) modal.hide();
            
            // Sahifani yangilash
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            throw new Error(data.message || 'Noma\'lum xatolik');
        }
    })
    .catch(error => {
        console.error('Full error:', error);
        alert('Xatolik yuz berdi: ' + error.message);
    })
    .finally(() => {
        // Loading yashirish
        saveBtn.disabled = false;
        if (loading) loading.style.display = 'none';
    });
}
    
    // Enter tugmasini bosish orqali saqlash
    document.getElementById('statusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveStatus();
    });
</script>
@endsection