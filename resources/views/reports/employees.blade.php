<!-- resources/views/reports/employees.blade.php -->
@extends('layouts.app')

@section('title', 'Xodimlar Hisoboti')
@section('page-title', 'Xodimlar Faoliyat Hisoboti')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4>Xodimlar Statistikasi</h4>
            <form method="GET" class="d-flex">
                <select name="period" class="form-control me-2">
                    <option value="7" {{ $period == 7 ? 'selected' : '' }}>So'nggi 7 kun</option>
                    <option value="30" {{ $period == 30 ? 'selected' : '' }}>So'nggi 30 kun</option>
                    <option value="90" {{ $period == 90 ? 'selected' : '' }}>So'nggi 90 kun</option>
                </select>
                <button type="submit" class="btn btn-primary">Yangilash</button>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-users"></i> Xodimlar Samaradorligi</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Xodim</th>
                        <th>Lavozim</th>
                        <th>Buyurtmalar</th>
                        <th>Jami Sotuv</th>
                        <th>O'rtacha Sotuv</th>
                        <th>Samaradorlik</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employeeStats as $employee)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </div>
                                <strong>{{ $employee->name }}</strong>
                            </div>
                        </td>
                        <td><span class="badge bg-info">{{ $employee->role->display_name }}</span></td>
                        <td><span class="badge bg-primary">{{ $employee->total_orders }}</span></td>
                        <td><strong>{{ number_format($employee->total_sales) }} so'm</strong></td>
                        <td>{{ $employee->total_orders > 0 ? number_format($employee->total_sales / $employee->total_orders) : 0 }} so'm</td>
                        <td>
                            @php
                                $performance = $employee->total_sales / ($period * 100000); // Performance metric
                                $performancePercent = min(100, $performance * 100);
                            @endphp
                            <div class="progress">
                                <div class="progress-bar bg-{{ $performancePercent > 80 ? 'success' : ($performancePercent > 50 ? 'warning' : 'danger') }}" 
                                     style="width: {{ $performancePercent }}%">
                                    {{ number_format($performancePercent, 1) }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-user-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Ma'lumotlar topilmadi</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection