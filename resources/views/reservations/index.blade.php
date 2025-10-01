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
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Qidirish</label>
                        <input type="text" class="form-control" name="search" id="searchInput"
                            value="{{ request('search') }}" placeholder="Mijoz nomi, telefon...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Holat</label>
                        <select class="form-control auto-submit" name="status">
                            <option value="">Barcha</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Tasdiqlangan</option>
                            <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Faol</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tugallangan</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Bekor qilingan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Xona</label>
                        <select class="form-control auto-submit" name="room_id">
                            <option value="">Barcha xonalar</option>
                            @foreach($rooms as $room)
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
                        <input type="date" class="form-control auto-submit" name="date" value="{{ request('date') }}">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reservations Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> Rezervatsiyalar Ro'yxati</h5>
    </div>
    <div class="card-body">
        @if($reservations->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover" id="reservationsTable">
                <thead>
                    <tr>
                        <th>Rezervatsiya №</th>
                        <th>Mijoz</th>
                        <th>Xona</th>
                        <th>Boshlanish Sanasi</th>
                        <th>Tugash Sanasi</th>
                        <th>Kunlar</th>
                        <th>Mehmonlar</th>
                        <th>Summa</th>
                        <th>Holat</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reservations as $reservation)
                    <tr>
                        <td>
                            <strong>{{ $reservation->reservation_number }}</strong>
                            <br><small class="text-muted">{{ $reservation->created_at->format('d.m.Y H:i') }}</small>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $reservation->customer->name }}</strong>
                                <br><small class="text-muted">{{ $reservation->customer->phone }}</small>
                                @if($reservation->customer->email)
                                <br><small class="text-muted">{{ $reservation->customer->email }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong>{{ $reservation->room->name_uz }}</strong>
                            <br><small class="text-muted">{{ $reservation->room->capacity }} kishi sig'imi</small>
                            <br><span class="badge bg-info">{{ number_format($reservation->room->daily_rate) }} so'm/kun</span>
                        </td>
                        <td>
                            <strong>{{ $reservation->reservation_date->format('d.m.Y') }}</strong>
                            <br><small class="text-muted">{{ getUzbekWeekday($reservation->reservation_date) }}</small>
                            @if($reservation->reservation_date->isToday())
                            <br><span class="badge bg-success">Bugun</span>
                            @elseif($reservation->reservation_date->isTomorrow())
                            <br><span class="badge bg-warning">Ertaga</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $reservation->end_date->format('d.m.Y') }}</strong>
                            <br><small class="text-muted">{{ getUzbekWeekday($reservation->end_date) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $reservation->days_count }} kun</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $reservation->guest_count }} kishi</span>
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
                            @if($reservation->status === 'confirmed')
                            <span class="badge bg-info">Tasdiqlangan</span>
                            @elseif($reservation->status === 'checked_in')
                            <span class="badge bg-success">Keldi</span>
                            @elseif($reservation->status === 'completed')
                            <span class="badge bg-secondary">Tugallangan</span>
                            @else
                            <span class="badge bg-danger">Bekor qilingan</span>
                            @endif

                            @if($reservation->is_active)
                            <br><span class="badge bg-warning text-dark">Aktiv</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline-info" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if(in_array($reservation->status, ['confirmed']))
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
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <small class="text-muted">
                    Jami {{ $reservations->total() }} ta rezervatsiya,
                    {{ $reservations->firstItem() }}-{{ $reservations->lastItem() }} ko'rsatilmoqda
                </small>
            </div>
            <div>
                {{ $reservations->links() }}
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Rezervatsiyalar topilmadi</h5>
            <p class="text-muted">Filterni o'zgartiring yoki yangi rezervatsiya yarating</p>
            <a href="{{ route('reservations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Rezervatsiya
            </a>
        </div>
        @endif
    </div>
</div>

<style>
    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }

    .badge {
        font-size: 0.75em;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit filter form on change for select and date inputs
        const autoSubmitInputs = document.querySelectorAll('.auto-submit');
        autoSubmitInputs.forEach(input => {
            input.addEventListener('change', function() {
                this.form.submit();
            });
        });

        // Auto-submit search input with debounce
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const cursorPosition = this.selectionStart;
                const inputValue = this.value;

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    // Save scroll position
                    const scrollPos = window.scrollY;

                    // Submit form
                    this.form.submit();

                    // Restore cursor position and scroll after page reload
                    sessionStorage.setItem('searchCursorPosition', cursorPosition);
                    sessionStorage.setItem('searchInputValue', inputValue);
                    sessionStorage.setItem('scrollPosition', scrollPos);
                }, 500); // 500ms debounce
            });

            // Restore cursor position after page load
            const savedCursorPosition = sessionStorage.getItem('searchCursorPosition');
            const savedInputValue = sessionStorage.getItem('searchInputValue');
            const savedScrollPos = sessionStorage.getItem('scrollPosition');

            if (savedCursorPosition && savedInputValue === searchInput.value) {
                searchInput.focus();
                searchInput.setSelectionRange(savedCursorPosition, savedCursorPosition);
                sessionStorage.removeItem('searchCursorPosition');
                sessionStorage.removeItem('searchInputValue');
            }

            if (savedScrollPos) {
                window.scrollTo(0, parseInt(savedScrollPos));
                sessionStorage.removeItem('scrollPosition');
            }
        }
    });
</script>
@endsection