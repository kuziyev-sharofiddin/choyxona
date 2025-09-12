@extends('layouts.app')

@section('title', 'Xonalar')
@section('page-title', 'Xonalar Boshqaruvi')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Xonalar</h4>
                <p class="text-muted">Jami: {{ $rooms->count() }} ta xona</p>
            </div>
            <a href="{{ route('rooms.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Xona
            </a>
        </div>
    </div>
</div>

<!-- Room Status Overview -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Bo'sh Xonalar</h6>
                        <h4>{{ $rooms->where('status', 'available')->count() }}</h4>
                    </div>
                    <i class="fas fa-door-open fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Band Xonalar</h6>
                        <h4>{{ $rooms->where('status', 'occupied')->count() }}</h4>
                    </div>
                    <i class="fas fa-door-closed fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Ta'mirda</h6>
                        <h4>{{ $rooms->where('status', 'maintenance')->count() }}</h4>
                    </div>
                    <i class="fas fa-tools fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rooms Grid -->
<div class="row">
    @foreach($rooms as $room)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card room-card h-100 room-{{ $room->status }}">
            @if($room->image)
            <img src="{{ Storage::url($room->image) }}" class="card-img-top" 
                 style="height: 200px; object-fit: cover;">
            @else
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                 style="height: 200px;">
                <i class="fas fa-door-open fa-3x text-muted"></i>
            </div>
            @endif
            
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title">{{ $room->name_uz }}</h5>
                    @if($room->status === 'available')
                        <span class="badge bg-success">Bo'sh</span>
                    @elseif($room->status === 'occupied')
                        <span class="badge bg-danger">Band</span>
                    @else
                        <span class="badge bg-warning">Ta'mir</span>
                    @endif
                </div>
                
                <p class="card-text text-muted">{{ $room->description }}</p>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Sig'im:</small>
                        <br><strong>{{ $room->capacity }} kishi</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Soatlik narx:</small>
                        <br><strong>{{ number_format($room->hourly_rate) }} so'm</strong>
                    </div>
                </div>
                
                @if($room->amenities && count($room->amenities) > 0)
                <div class="mb-2">
                    <small class="text-muted">Imkoniyatlar:</small>
                    <div class="mt-1">
                        @foreach($room->amenities as $amenity)
                            <span class="badge bg-light text-dark me-1">{{ $amenity }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="mb-2">
                    <small class="text-muted">Bugungi rezervatsiyalar:</small>
                    <br><span class="badge bg-info">{{ $room->reservations_count }} ta</span>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="btn-group w-100">
                    <a href="{{ route('rooms.show', $room) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye"></i> Ko'rish
                    </a>
                    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    @if($room->status === 'available')
                        <a href="{{ route('reservations.create', ['room_id' => $room->id]) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-calendar-plus"></i> Rezerv
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection