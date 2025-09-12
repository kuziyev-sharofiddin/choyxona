@extends('layouts.app')

@section('title', 'Xodimlar')
@section('page-title', 'Xodimlar Boshqaruvi')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Xodimlar</h4>
                <p class="text-muted">Jami: {{ $employees->total() }} ta xodim</p>
            </div>
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Xodim
            </a>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Jami Xodimlar</h6>
                        <h4>{{ $stats['total_employees'] }}</h4>
                    </div>
                    <i class="fas fa-users fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Faol Xodimlar</h6>
                        <h4>{{ $stats['active_employees'] }}</h4>
                    </div>
                    <i class="fas fa-user-check fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>Jami Maosh</h6>
                        <h4>{{ number_format($stats['total_salary']) }}</h4>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6>O'rtacha Maosh</h6>
                        <h4>{{ number_format($stats['avg_salary']) }}</h4>
                    </div>
                    <i class="fas fa-calculator fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employees Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> Xodimlar Ro'yxati</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Xodim</th>
                        <th>Aloqa</th>
                        <th>Lavozim</th>
                        <th>Ish Haqi</th>
                        <th>Ish Boshlagan</th>
                        <th>Holat</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-3">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </div>
                                <div>
                                    <strong>{{ $employee->name }}</strong>
                                    <br><small class="text-muted">ID: #{{ $employee->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <i class="fas fa-envelope text-muted"></i> {{ $employee->email }}
                                <br><i class="fas fa-phone text-muted"></i> {{ $employee->phone }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $employee->role->display_name }}</span>
                        </td>
                        <td>
                            <strong>{{ number_format($employee->salary) }} so'm</strong>
                        </td>
                        <td>
                            {{ $employee->hire_date ? $employee->hire_date->format('d.m.Y') : '-' }}
                            @if($employee->hire_date)
                                <br><small class="text-muted">{{ $employee->hire_date->diffForHumans() }}</small>
                            @endif
                        </td>
                        <td>
                            @if($employee->is_active)
                                <span class="badge bg-success">Faol</span>
                            @else
                                <span class="badge bg-secondary">Nofaol</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Haqiqatan ham o‘chirishni xohlaysizmi?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Xodimlar topilmadi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
