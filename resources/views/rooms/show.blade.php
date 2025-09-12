<!-- resources/views/rooms/show.blade.php -->
@extends('layouts.app')

@section('title', 'Xona Ma\'lumotlari')
@section('page-title', $room->name_uz)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-door-open"></i> Xona Ma'lumotlari</h5>
                <div>
                    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    @if($room->status === 'available')
                    <a href="{{ route('reservations.create', ['room_id' => $room->id]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-calendar-plus"></i> Rezerv
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($room->image)
                        <img src="{{ Storage::url($room->image) }}" class="img-fluid rounded">
                        @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-door-open fa-3x text-muted"></i>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <th>Nomi:</th>
                                <td><h5>{{ $room->name_uz }}</h5></td>
                            </tr>
                            <tr>
                                <th>Sig'imi:</th>
                                <td><span class="badge bg-info">{{ $room->capacity }} kishi</span></td>
                            </tr>
                            <tr>
                                <th>Soatlik narx:</th>
                                <td><strong class="text-success">{{ number_format($room->hourly_rate) }} so'm</strong></td>
                            </tr>
                            <tr>
                                <th>Holat:</th>
                                <td>
                                    @if($room->status === 'available')
                                        <span class="badge bg-success">Bo'sh</span>
                                    @elseif($room->status === 'occupied')
                                        <span class="badge bg-danger">Band</span>
                                    @else
                                        <span class="badge bg-warning">Ta'mir</span>
                                    @endif
                                </td>
                            </tr>
                            @if($room->description)
                            <tr>
                                <th>Tavsif:</th>
                                <td>{{ $room->description }}</td>
                            </tr>
                            @endif
                        </table>

                        @if($room->amenities && count($room->amenities) > 0)
                        <div class="mt-3">
                            <h6>Imkoniyatlar:</h6>
                            <div>
                                @foreach($room->amenities as $amenity)
                                    <span class="badge bg-light text-dark me-1">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($currentReservation)
                        <div class="alert alert-warning mt-3">
                            <h6><i class="fas fa-exclamation-triangle"></i> Joriy Rezervatsiya</h6>
                            <p><strong>Mijoz:</strong> {{ $currentReservation->customer->name }}</p>
                            <p><strong>Tugash:</strong> {{ $currentReservation->end_time->format('d.m.Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Reservations -->
        @if($todayReservations->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar"></i> Bugungi Rezervatsiyalar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Vaqt</th>
                                <th>Mijoz</th>
                                <th>Mehmonlar</th>
                                <th>Summa</th>
                                <th>Holat</th>
                                <th>Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayReservations as $reservation)
                            <tr>
                                <td>
                                    {{ $reservation->start_time->format('H:i') }} - 
                                    {{ $reservation->end_time->format('H:i') }}
                                </td>
                                <td>
                                    <strong>{{ $reservation->customer->name }}</strong>
                                    <br><small>{{ $reservation->customer->phone }}</small>
                                </td>
                                <td>{{ $reservation->guest_count }} kishi</td>
                                <td>{{ number_format($reservation->room_charge) }} so'm</td>
                                <td>
                                    @if($reservation->status === 'confirmed')
                                        <span class="badge bg-info">Tasdiqlangan</span>
                                    @elseif($reservation->status === 'checked_in')
                                        <span class="badge bg-success">Faol</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($reservation->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Room Statistics -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistika</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="text-primary">{{ $room->reservations()->count() }}</h3>
                    <p class="text-muted mb-0">Jami rezervatsiyalar</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-success">{{ $todayReservations->count() }}</h3>
                    <p class="text-muted mb-0">Bugungi rezervatsiyalar</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-warning">{{ number_format($room->reservations()->sum('room_charge')) }}</h3>
                    <p class="text-muted mb-0">Jami daromad (so'm)</p>
                </div>
                
                @php
                    $totalHours = $room->reservations()->sum(\DB::raw('TIMESTAMPDIFF(HOUR, start_time, end_time)'));
                    $occupancyRate = $totalHours > 0 ? min(100, ($totalHours / (30 * 12)) * 100) : 0; // 30 days * 12 hours average
                @endphp
                <div class="text-center">
                    <h3 class="text-info">{{ number_format($occupancyRate, 1) }}%</h3>
                    <p class="text-muted mb-0">Band bo'lish darajasi</p>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tools"></i> Tezkor Amallar</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($room->status === 'available')
                    <a href="{{ route('reservations.create', ['room_id' => $room->id]) }}" class="btn btn-success">
                        <i class="fas fa-calendar-plus"></i> Yangi Rezervatsiya
                    </a>
                    @endif
                    
                    <form action="{{ route('rooms.maintenance', $room) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-{{ $room->status === 'maintenance' ? 'success' : 'warning' }} w-100">
                            <i class="fas fa-{{ $room->status === 'maintenance' ? 'play' : 'tools' }}"></i>
                            {{ $room->status === 'maintenance' ? 'Faollashtirish' : 'Ta\'mirga Jo\'natish' }}
                        </button>
                    </form>
                    
                    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    
                    <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection