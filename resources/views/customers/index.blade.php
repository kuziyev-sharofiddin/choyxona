@extends('layouts.app')

@section('title', 'Mijozlar')
@section('page-title', 'Mijozlar Boshqaruvi')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Mijozlar</h4>
                <p class="text-muted">Jami: {{ $customers->total() }} ta mijoz</p>
            </div>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Mijoz
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
                        <h6>Jami Mijozlar</h6>
                        <h4>{{ \App\Models\Customer::count() }}</h4>
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
                        <h6>Faol Mijozlar</h6>
                        <h4>{{ \App\Models\Customer::where('last_visit', '>=', now()->subDays(30))->count() }}</h4>
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
                        <h6>O'rtacha Xarajat</h6>
                        <h4>{{ number_format(\App\Models\Customer::avg('total_spent')) }}</h4>
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
                        <h6>VIP Mijozlar</h6>
                        <h4>{{ \App\Models\Customer::where('total_spent', '>', 500000)->count() }}</h4>
                    </div>
                    <i class="fas fa-crown fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('customers.index') }}">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search" 
                           value="{{ request('search') }}" placeholder="Ism, telefon yoki email bo'yicha qidiring...">
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="sort">
                        <option value="last_visit" {{ request('sort') == 'last_visit' ? 'selected' : '' }}>So'nggi tashrif</option>
                        <option value="total_spent" {{ request('sort') == 'total_spent' ? 'selected' : '' }}>Eng ko'p xarajat</option>
                        <option value="visit_count" {{ request('sort') == 'visit_count' ? 'selected' : '' }}>Eng ko'p tashrif</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Ism bo'yicha</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i> Qidirish
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list"></i> Mijozlar Ro'yxati</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mijoz</th>
                        <th>Aloqa</th>
                        <th>Tashrif Soni</th>
                        <th>Jami Xarajat</th>
                        <th>O'rtacha Xarajat</th>
                        <th>So'nggi Tashrif</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <strong>{{ $customer->name }}</strong>
                                    @if($customer->total_spent > 500000)
                                        <i class="fas fa-crown text-warning ms-1" title="VIP Mijoz"></i>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <i class="fas fa-phone text-muted"></i> {{ $customer->phone }}
                                @if($customer->email)
                                    <br><i class="fas fa-envelope text-muted"></i> {{ $customer->email }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $customer->visit_count }} marta</span>
                        </td>
                        <td>
                            <strong>{{ number_format($customer->total_spent) }} so'm</strong>
                        </td>
                        <td>
                            @if($customer->visit_count > 0)
                                {{ number_format($customer->total_spent / $customer->visit_count) }} so'm
                            @else
                                0 so'm
                            @endif
                        </td>
                        <td>
                            @if($customer->last_visit)
                                {{ $customer->last_visit->format('d.m.Y') }}
                                <br><small class="text-muted">{{ $customer->last_visit->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Hali kelmagan</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('reservations.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline-success">
                                    <i class="fas fa-calendar-plus"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Mijozlar topilmadi</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $customers->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.product-card {
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endsection