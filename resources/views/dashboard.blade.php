{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Asosiy boshqaruv paneli - real vaqt statistikasi')

@section('content')
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Bugungi Daromad</h6>
                        <h3 class="mb-0">{{ number_format($todayRevenue) }} so'm</h3>
                        <small class="opacity-75">
                            @if($yesterdayRevenue > 0)
                                @php $change = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100; @endphp
                                @if($change > 0)
                                    <i class="fas fa-arrow-up"></i> +{{ number_format($change, 1) }}%
                                @elseif($change < 0)
                                    <i class="fas fa-arrow-down"></i> {{ number_format($change, 1) }}%
                                @else
                                    <i class="fas fa-minus"></i> 0%
                                @endif
                            @endif
                        </small>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Faol Rezervatsiyalar</h6>
                        <h3 class="mb-0">{{ $activeReservations }}</h3>
                        <small class="opacity-75">Hozirda faol</small>
                    </div>
                    <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Bugungi Buyurtmalar</h6>
                        <h3 class="mb-0">{{ $todayOrders }}</h3>
                        <small class="opacity-75">Jami: {{ $todayOrderItems }} ta mahsulot</small>
                    </div>
                    <i class="fas fa-utensils fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Bo'sh Xonalar</h6>
                        <h3 class="mb-0">{{$availableRooms }}</h3>
                        <small class="opacity-75">
                            {{ round(($availableRooms / $totalRooms) * 100) }}% bo'sh
                        </small>
                    </div>
                    <i class="fas fa-door-open fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Tezkor Amallar</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <a href="{{ route('reservations.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Yangi Rezervatsiya
                        </a>
                    </div>
                    <div class="col-md-6 mb-2">
                        <a href="{{ route('create_order_by_type') }}" class="btn btn-success w-100">
                            <i class="fas fa-utensils"></i> Yangi Buyurtma
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Room Status -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-door-open"></i> Xonalar Holati</h5>
                <small class="text-white">
                    <i class="fas fa-sync"></i> Real vaqt yangilanish
                </small>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($rooms as $room)
                    <div class="col-md-4 col-lg-3 mb-3">
                        <div class="card room-card room-{{ $room->status }}" 
                             onclick="openRoomModal('{{ $room->id }}', '{{ $room->name_uz }}', '{{ $room->status }}')">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="card-title mb-1">{{ $room->name_uz }}</h6>
                                        <p class="card-text small text-muted mb-2">
                                            <i class="fas fa-users"></i> {{ $room->capacity }} kishi<br>
                                            <i class="fas fa-money-bill"></i> {{ number_format($room->daily_rate) }} so'm/kun
                                        </p>
                                        @if($room->reservations->count() > 0)
                                            @php $currentRes = $room->reservations->first(); @endphp
                                            <small class="text-primary">
                                                <i class="fas fa-calendar"></i> 
                                                {{ $currentRes->end_date->format('d.m.Y') }} gacha band
                                                <br><strong>{{ $currentRes->customer->name }}</strong>
                                            </small>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        @if($room->status == 'available')
                                            <span class="badge bg-success status-badge">Bo'sh</span>
                                        @elseif($room->status == 'occupied')
                                            <span class="badge bg-danger status-badge">Band</span>
                                        @else
                                            <span class="badge bg-warning status-badge">Ta'mir</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($room->amenities && count($room->amenities) > 0)
                                <div class="mt-2">
                                    @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                                        <span class="badge bg-light text-dark me-1" style="font-size: 0.7em;">{{ $amenity }}</span>
                                    @endforeach
                                    @if(count($room->amenities) > 3)
                                        <span class="badge bg-light text-dark" style="font-size: 0.7em;">+{{ count($room->amenities) - 3 }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock"></i> So'nggi Buyurtmalar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Buyurtma №</th>
                                <th>Mijoz</th>
                                <th>Summa</th>
                                <th>Holat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr onclick="window.location.href='{{ route('orders.show', $order) }}'" style="cursor: pointer;">
                                <td>
                                    <strong>{{ $order->order_number ?? '#' . $order->id }}</strong>
                                    <br><small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    {{ $order->reservation->customer->name ?? 'N/A' }}
                                    <br><small class="text-muted">{{ $order->reservation->room->name_uz ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <strong>{{ number_format($order->total_amount) }} so'm</strong>
                                    <br><small class="text-muted">{{ $order->items->count() ?? 0 }} mahsulot</small>
                                </td>
                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning">Kutilmoqda</span>
                                    @elseif($order->status == 'preparing')
                                        <span class="badge bg-info">Tayyorlanmoqda</span>
                                    @elseif($order->status == 'ready')
                                        <span class="badge bg-success">Tayyor</span>
                                    @elseif($order->status == 'served')
                                        <span class="badge bg-primary">Berildi</span>
                                    @else
                                        <span class="badge bg-secondary">Tugallangan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">
                                    <i class="fas fa-utensils mb-2"></i><br>
                                    Hozircha buyurtmalar yo'q
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($recentOrders->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-sm">
                        Barchasi <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar"></i> Bugungi Rezervatsiyalar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Sana</th>
                                <th>Mijoz</th>
                                <th>Xona</th>
                                <th>Holat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayReservations as $reservation)
                            <tr onclick="window.location.href='{{ route('reservations.show', $reservation) }}'" style="cursor: pointer;">
                                <td>
                                    <strong>{{ $reservation->reservation_date->format('d.m') }}</strong>
                                    <br><small class="text-muted">{{ $reservation->days_count }} kun</small>
                                </td>
                                <td>
                                    {{ $reservation->customer->name }}
                                    <br><small class="text-muted">{{ $reservation->guest_count }} kishi</small>
                                </td>
                                <td>
                                    <strong>{{ $reservation->room->name_uz }}</strong>
                                    <br><small class="text-muted">{{ number_format($reservation->room_charge) }} so'm</small>
                                </td>
                                <td>
                                    @if($reservation->status == 'confirmed')
                                        <span class="badge bg-info">Tasdiqlangan</span>
                                    @elseif($reservation->status == 'checked_in')
                                        <span class="badge bg-success">Keldi</span>
                                    @elseif($reservation->status == 'completed')
                                        <span class="badge bg-secondary">Tugallangan</span>
                                    @else
                                        <span class="badge bg-warning">Kutilmoqda</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">
                                    <i class="fas fa-calendar mb-2"></i><br>
                                    Bugun rezervatsiyalar yo'q
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($todayReservations->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-primary btn-sm">
                        Barchasi <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Today's Schedule -->
@if(isset($todayStarting) && $todayStarting->count() > 0 || isset($todayEnding) && $todayEnding->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Bugungi Jadval</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(isset($todayStarting) && $todayStarting->count() > 0)
                    <div class="col-md-6">
                        <h6 class="text-success mb-2">Boshlanadigan Rezervatsiyalar</h6>
                        @foreach($todayStarting as $reservation)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <strong>{{ $reservation->customer->name }}</strong>
                                <br><small class="text-muted">{{ $reservation->room->name_uz }} - {{ $reservation->days_count }} kun</small>
                            </div>
                            <span class="badge bg-success">Yangi</span>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if(isset($todayEnding) && $todayEnding->count() > 0)
                    <div class="col-md-6">
                        <h6 class="text-warning mb-2">Tugaydigan Rezervatsiyalar</h6>
                        @foreach($todayEnding as $reservation)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div>
                                <strong>{{ $reservation->customer->name }}</strong>
                                <br><small class="text-muted">{{ $reservation->room->name_uz }} - Check-out</small>
                            </div>
                            <span class="badge bg-warning">Tugaydi</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Room Quick Action Modal -->
<div class="modal fade" id="roomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomModalTitle">Xona Amalları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-success" onclick="createReservation()">
                        <i class="fas fa-plus"></i> Yangi Rezervatsiya
                    </button>
                    <button class="btn btn-info" onclick="viewRoomDetails()">
                        <i class="fas fa-eye"></i> Xona Ma'lumotlari
                    </button>
                    <button class="btn btn-warning" onclick="setMaintenance()">
                        <i class="fas fa-tools"></i> Ta'mirga Jo'natish
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let selectedRoomId = null;

function openRoomModal(roomId, roomName, status) {
    selectedRoomId = roomId;
    document.getElementById('roomModalTitle').textContent = roomName + ' - Amallar';
    new bootstrap.Modal(document.getElementById('roomModal')).show();
}

function createReservation() {
    window.location.href = `/reservations/create?room_id=${selectedRoomId}`;
}

function viewRoomDetails() {
    window.location.href = `/rooms/${selectedRoomId}`;
}

function setMaintenance() {
    if(confirm('Xonani ta\'mirga jo\'natishni xohlaysizmi?')) {
        fetch(`/rooms/${selectedRoomId}/maintenance`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.message) {
                alert(data.message);
            }
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Xatolik yuz berdi');
        });
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('roomModal')).hide();
    }
}

// Auto refresh every 60 seconds
setInterval(() => {
    // Only refresh if user is still active (not clicked anything in last 5 minutes)
    if (Date.now() - window.lastActivity < 300000) {
        location.reload();
    }
}, 60000);

// Track user activity
window.lastActivity = Date.now();
document.addEventListener('click', () => {
    window.lastActivity = Date.now();
});

// Real-time clock update
function updateClock() {
    const now = new Date();
    const timeString = now.toLocaleString('uz-UZ', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    const clockElement = document.querySelector('.me-3.text-muted');
    if (clockElement) {
        clockElement.innerHTML = '<i class="fas fa-clock me-1"></i>' + timeString;
    }
}

// Update clock every minute
setInterval(updateClock, 60000);

// Animate counter numbers on page load
document.addEventListener('DOMContentLoaded', function() {
    const statsCards = document.querySelectorAll('.stats-card h3');
    statsCards.forEach(function(element) {
        const text = element.textContent;
        const number = parseInt(text.replace(/[^\d]/g, ''));
        
        if (!isNaN(number) && number > 0) {
            let current = 0;
            const increment = number / 50;
            const timer = setInterval(function() {
                current += increment;
                if (current >= number) {
                    current = number;
                    clearInterval(timer);
                }
                element.textContent = Math.ceil(current).toLocaleString() + (text.includes('so\'m') ? ' so\'m' : '');
            }, 40);
        }
    });
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
            this.classList.add('shadow-lg');
        });
        card.addEventListener('mouseleave', function() {
            this.classList.remove('shadow-lg');
        });
    });
});

// Notification system
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const notification = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', notification);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.opacity = '0';
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        });
    }, 5000);
}
</script>

<style>
/* Additional custom styles */
.room-card {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.room-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    overflow: hidden;
    position: relative;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.1);
    cursor: pointer;
}

.badge {
    font-weight: 500;
}

/* Loading spinner styles */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .stats-card h3 {
        font-size: 1.5rem;
    }
    
    .room-card {
        margin-bottom: 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}

/* Status badge animations */
.badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Card loading animation */
.card {
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection