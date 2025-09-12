<!-- resources/views/reservations/index.blade.php -->
@extends('layouts.app')

@section('title', 'Rezervatsiyalar')
@section('page-title', 'Rezervatsiyalar Boshqaruvi')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Barcha Rezervatsiyalar</h4>
                <p class="text-muted">Jami: {{ $reservations->total() }} ta rezervatsiya</p>
            </div>
            <a href="{{ route('reservations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Rezervatsiya
            </a>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reservations.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Qidirish</label>
                        <input type="text" class="form-control" name="search" 
                               value="{{ request('search') }}" placeholder="Mijoz nomi, telefon...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Holat</label>
                        <select class="form-control" name="status">
                            <option value="">Barcha</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Kutilayotgan</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Tasdiqlangan</option>
                            <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Faol</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tugallangan</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Bekor qilingan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Xona</label>
                        <select class="form-control" name="room_id">
                            <option value="">Barcha xonalar</option>
                            @foreach(\App\Models\Room::all() as $room)
                            <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name_uz }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sana</label>
                        <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i> Qidirish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Filter Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Kutilayotgan</h6>
                        <h4>{{ $reservations->where('status', 'pending')->count() }}</h4>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Tasdiqlangan</h6>
                        <h4>{{ $reservations->where('status', 'confirmed')->count() }}</h4>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Faol</h6>
                        <h4>{{ $reservations->where('status', 'checked_in')->count() }}</h4>
                    </div>
                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Tugallangan</h6>
                        <h4>{{ $reservations->where('status', 'completed')->count() }}</h4>
                    </div>
                    <i class="fas fa-flag-checkered fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Bekor qilingan</h6>
                        <h4>{{ $reservations->where('status', 'cancelled')->count() }}</h4>
                    </div>
                    <i class="fas fa-times-circle fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Jami Daromad</h6>
                        <h4>{{ number_format($reservations->sum(function($r) { return $r->getTotalAmount(); })) }}</h4>
                        <small>so'm</small>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reservations Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list"></i> Rezervatsiyalar Ro'yxati</h5>
        <div>
            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button class="btn btn-info btn-sm" onclick="printTable()">
                <i class="fas fa-print"></i> Chop etish
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="reservationsTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Rezervatsiya №</th>
                        <th>Mijoz</th>
                        <th>Xona</th>
                        <th>Vaqt</th>
                        <th>Mehmonlar</th>
                        <th>Summa</th>
                        <th>Holat</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                    <tr>
                        <td>
                            <input type="checkbox" class="reservation-checkbox" value="{{ $reservation->id }}">
                        </td>
                        <td>
                            <strong>{{ $reservation->reservation_number }}</strong>
                            <br><small class="text-muted">{{ $reservation->created_at->format('d.m.Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2">
                                    {{ strtoupper(substr($reservation->customer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <strong>{{ $reservation->customer->name }}</strong>
                                    <br><small class="text-muted">{{ $reservation->customer->phone }}</small>
                                    @if($reservation->customer->email)
                                        <br><small class="text-muted">{{ $reservation->customer->email }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>{{ $reservation->room->name_uz }}</strong>
                            <br><small class="text-muted">{{ $reservation->room->capacity }} kishi sig'imi</small>
                            <br><span class="badge bg-light text-dark">{{ number_format($reservation->room->hourly_rate) }} so'm/soat</span>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $reservation->start_time->format('d.m.Y') }}</strong>
                                <br>{{ $reservation->start_time->format('H:i') }} - {{ $reservation->end_time->format('H:i') }}
                                <br><span class="badge badge-sm bg-info">{{ $reservation->getDuration() }} soat</span>
                                @if($reservation->start_time->isToday())
                                    <br><span class="badge bg-success">Bugun</span>
                                @elseif($reservation->start_time->isTomorrow())
                                    <br><span class="badge bg-warning">Ertaga</span>
                                @elseif($reservation->start_time->isPast())
                                    <br><span class="badge bg-secondary">O'tgan</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $reservation->guest_count }} kishi</span>
                        </td>
                        <td>
                            <div>
                                <strong class="text-success">{{ number_format($reservation->getTotalAmount()) }} so'm</strong>
                                <br><small class="text-muted">Xona: {{ number_format($reservation->room_charge) }}</small>
                                @if($reservation->orders->count() > 0)
                                    <br><small class="text-muted">Buyurtma: {{ number_format($reservation->orders->sum('total_amount')) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($reservation->status === 'pending')
                                <span class="badge bg-warning">Kutilmoqda</span>
                            @elseif($reservation->status === 'confirmed')
                                <span class="badge bg-info">Tasdiqlangan</span>
                            @elseif($reservation->status === 'checked_in')
                                <span class="badge bg-success">Keldi</span>
                            @elseif($reservation->status === 'completed')
                                <span class="badge bg-secondary">Tugallangan</span>
                            @else
                                <span class="badge bg-danger">Bekor qilingan</span>
                            @endif
                            
                            @if($reservation->start_time->diffInHours(now()) < 2 && $reservation->start_time->isFuture())
                                <br><span class="badge bg-warning text-dark">Tez orada</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline-info" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if(in_array($reservation->status, ['pending', 'confirmed']))
                                <a href="{{ route('reservations.edit', $reservation) }}" class="btn btn-outline-primary" title="Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                
                                @if($reservation->status === 'confirmed')
                                <form action="{{ route('reservations.checkin', $reservation) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Keldi">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                </form>
                                @endif
                                
                                @if(in_array($reservation->status, ['confirmed', 'checked_in']))
                                <a href="{{ route('orders.create', ['reservation_id' => $reservation->id]) }}" class="btn btn-outline-warning" title="Buyurtma">
                                    <i class="fas fa-utensils"></i>
                                </a>
                                @endif
                                
                                @if($reservation->status === 'checked_in')
                                <form action="{{ route('reservations.complete', $reservation) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary" title="Tugallash">
                                        <i class="fas fa-flag-checkered"></i>
                                    </button>
                                </form>
                                @endif

                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('reservations.show', $reservation) }}">
                                            <i class="fas fa-eye me-2"></i>Batafsil ko'rish
                                        </a></li>
                                        @if($reservation->status === 'completed')
                                        <li><a class="dropdown-item" href="{{ route('payments.create', ['reservation_id' => $reservation->id]) }}">
                                            <i class="fas fa-credit-card me-2"></i>To'lov qabul qilish
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('reservations.receipt', $reservation) }}" target="_blank">
    <i class="fas fa-receipt me-2"></i>Chekni Chop Etishs
</a></li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-primary" href="tel:{{ $reservation->customer->phone }}">
                                            <i class="fas fa-phone me-2"></i>Qo'ng'iroq qilish
                                        </a></li>
                                        @if($reservation->customer->email)
                                        <li><a class="dropdown-item text-info" href="mailto:{{ $reservation->customer->email }}">
                                            <i class="fas fa-envelope me-2"></i>Email yuborish
                                        </a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Rezervatsiyalar topilmadi</h5>
                            <p class="text-muted">Yangi rezervatsiya yaratish uchun yuqoridagi tugmani bosing</p>
                            <a href="{{ route('reservations.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Yangi Rezervatsiya
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Bulk Actions -->
        @if($reservations->count() > 0)
        <div class="row mt-3">
            <div class="col-md-6">
                <div id="bulkActions" style="display: none;">
                    <div class="btn-group">
                        <button type="button" class="btn btn-warning" onclick="bulkAction('confirmed')">
                            <i class="fas fa-check"></i> Tasdiqlash
                        </button>
                        <button type="button" class="btn btn-danger" onclick="bulkAction('cancelled')">
                            <i class="fas fa-times"></i> Bekor qilish
                        </button>
                        <button type="button" class="btn btn-info" onclick="bulkExport()">
                            <i class="fas fa-download"></i> Eksport
                        </button>
                    </div>
                    <span id="selectedCount" class="ms-2 text-muted"></span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    {{ $reservations->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Quick Stats Modal -->
<div class="modal fade" id="statsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rezervatsiyalar Statistikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<div class="fab-container">
    <button class="btn btn-primary fab-main" type="button" data-bs-toggle="dropdown">
        <i class="fas fa-plus"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ route('reservations.create') }}">
            <i class="fas fa-calendar-plus me-2"></i>Yangi Rezervatsiya
        </a></li>
        <li><a class="dropdown-item" href="{{ route('customers.create') }}">
            <i class="fas fa-user-plus me-2"></i>Yangi Mijoz
        </a></li>
        <li><a class="dropdown-item" href="#" onclick="showStatsModal()">
            <i class="fas fa-chart-bar me-2"></i>Statistika
        </a></li>
    </ul>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Select All functionality
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.reservation-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

// Update bulk actions visibility
function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.reservation-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = `${checkedBoxes.length} ta tanlangan`;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Listen for checkbox changes
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.reservation-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Bulk actions
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.reservation-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('Hech qanday rezervatsiya tanlanmagan');
        return;
    }
    
    if (confirm(`${ids.length} ta rezervatsiyani ${action} qilishni tasdiqlaysizmi?`)) {
        // Send AJAX request
        fetch('/reservations/bulk-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                ids: ids,
                action: action
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Xatolik yuz berdi');
            }
        });
    }
}

// Export functions
function exportToExcel() {
    window.location.href = '/reservations/export?' + new URLSearchParams(window.location.search);
}

function bulkExport() {
    const checkedBoxes = document.querySelectorAll('.reservation-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('Hech qanday rezervatsiya tanlanmagan');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/reservations/bulk-export';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfInput);
    
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function printTable() {
    window.print();
}

// Show stats modal
function showStatsModal() {
    const modal = new bootstrap.Modal(document.getElementById('statsModal'));
    modal.show();
    
    // Load charts when modal is shown
    setTimeout(() => {
        loadStatusChart();
        loadMonthlyChart();
    }, 500);
}

function loadStatusChart() {
    const ctx = document.getElementById('statusChart').getContext('2d');
    const data = {
        labels: ['Kutilayotgan', 'Tasdiqlangan', 'Faol', 'Tugallangan', 'Bekor qilingan'],
        datasets: [{
            data: [
                {{ $reservations->where('status', 'pending')->count() }},
                {{ $reservations->where('status', 'confirmed')->count() }},
                {{ $reservations->where('status', 'checked_in')->count() }},
                {{ $reservations->where('status', 'completed')->count() }},
                {{ $reservations->where('status', 'cancelled')->count() }}
            ],
            backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#6c757d', '#dc3545']
        }]
    };
    
    new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Rezervatsiyalar Holati'
                }
            }
        }
    });
}

function loadMonthlyChart() {
    // This would typically load monthly data via AJAX
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Yan', 'Fev', 'Mar', 'Apr', 'May', 'Iyun'],
            datasets: [{
                label: 'Rezervatsiyalar',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#007bff',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Oylik Statistika'
                }
            }
        }
    });
}

// Auto-refresh functionality
let autoRefreshInterval;

function toggleAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    } else {
        autoRefreshInterval = setInterval(() => {
            location.reload();
        }, 60000); // Refresh every minute
    }
}

// Real-time updates (placeholder for WebSocket implementation)
function initializeRealTimeUpdates() {
    // This would connect to WebSocket for real-time updates
    console.log('Real-time updates initialized');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeRealTimeUpdates();
});
</script>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
}

.fab-container {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
}

.fab-main {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
}

.badge-sm {
    font-size: 0.7em;
}

@media print {
    .fab-container,
    .btn-group,
    .pagination {
        display: none !important;
    }
}

.table tbody tr:hover {
    background-color: rgba(0,123,255,0.1);
}

.status-pending { background-color: #fff3cd !important; }
.status-confirmed { background-color: #d1ecf1 !important; }
.status-checked_in { background-color: #d4edda !important; }
.status-completed { background-color: #e2e3e5 !important; }
.status-cancelled { background-color: #f8d7da !important; }
</style>
@endsection