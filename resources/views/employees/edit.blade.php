<!-- resources/views/employees/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Xodimni Tahrirlash')
@section('page-title', 'Xodimni Tahrirlash')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-edit"></i> Xodimni Tahrirlash</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('employees.update', $employee) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">To'liq Ism *</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefon *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Lavozim *</label>
                                <select class="form-control" id="role_id" name="role_id" required>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="salary" class="form-label">Ish Haqi (so'm) *</label>
                                <input type="number" class="form-control" id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hire_date" class="form-label">Ish Boshlagan Sana *</label>
                                <input type="date" class="form-control" id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Yangi Parol (ixtiyoriy)</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-muted">Parolni o'zgartirish uchun kiriting</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Parolni Takrorlang</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-secondary">
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