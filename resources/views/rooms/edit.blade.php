<!-- resources/views/rooms/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Xonani Tahrirlash')
@section('page-title', 'Xonani Tahrirlash')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Xonani Tahrirlash</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('rooms.update', $room) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nomi (Inglizcha) *</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $room->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name_uz" class="form-label">Nomi (O'zbekcha) *</label>
                                <input type="text" class="form-control" id="name_uz" name="name_uz" value="{{ old('name_uz', $room->name_uz) }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Sig'imi (kishi) *</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="daily_rate" class="form-label">Kunlik Narx (so'm) *</label>
                                <input type="number" class="form-control" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', $room->daily_rate) }}" min="0" step="1000" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Tavsif</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $room->description) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Xona Rasmi</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        @if($room->image)
                        <small class="text-muted">Hozirgi rasm: <a href="{{ Storage::url($room->image) }}" target="_blank">Ko'rish</a></small>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Xona Imkoniyatlari</label>
                        @php $roomAmenities = $room->amenities ?? []; @endphp
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="TV" id="tv" {{ in_array('TV', $roomAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tv">TV</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="AC" id="ac" {{ in_array('AC', $roomAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ac">Konditsioner</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="WiFi" id="wifi" {{ in_array('WiFi', $roomAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="wifi">WiFi</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Sound System" id="sound" {{ in_array('Sound System', $roomAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sound">Audio Tizim</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Microphone" id="mic" {{ in_array('Microphone', $roomAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mic">Mikrofon</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Projector" id="projector" {{ in_array('Projector', $roomAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="projector">Proyektor</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Mini Bar" id="minibar" {{ in_array('Mini Bar', $roomAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="minibar">Mini Bar</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Private Bathroom" id="bathroom" {{ in_array('Private Bathroom', $roomAmenities) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bathroom">Alohida Hojatxona</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Xona Holati</label>
                        <select class="form-control" id="status" name="status">
                            <option value="available" {{ old('status', $room->status) === 'available' ? 'selected' : '' }}>Mavjud</option>
                            <option value="occupied" {{ old('status', $room->status) === 'occupied' ? 'selected' : '' }}>Band</option>
                            <option value="maintenance" {{ old('status', $room->status) === 'maintenance' ? 'selected' : '' }}>Ta'mir</option>
                        </select>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rooms.show', $room) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Yangilash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection