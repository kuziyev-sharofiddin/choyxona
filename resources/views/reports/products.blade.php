<!-- resources/views/reports/products.blade.php -->
@extends('layouts.app')

@section('title', 'Mahsulotlar Hisoboti')
@section('page-title', 'Mahsulotlar Sotuv Hisoboti')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4>Mahsulotlar Statistikasi</h4>
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

<!-- Category Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Kategoriya bo'yicha Sotuv</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($categoryStats as $category)
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h5 class="text-primary">{{ $category->category_name }}</h5>
                                <h6>{{ $category->total_quantity }} dona</h6>
                                <p class="text-success mb-0">{{ number_format($category->total_revenue) }} so'm</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-trophy"></i> Eng Ko'p Sotilgan Mahsulotlar</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Reyting</th>
                        <th>Mahsulot</th>
                        <th>Kategoriya</th>
                        <th>Sotilgan</th>
                        <th>Daromad</th>
                        <th>O'rtacha Narx</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $index => $product)
                    <tr>
                        <td>
                            <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                #{{ $index + 1 }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="me-2 rounded" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                                <strong>{{ $product->name_uz }}</strong>
                            </div>
                        </td>
                        <td>{{ $product->category->name_uz }}</td>
                        <td><span class="badge bg-primary">{{ $product->total_quantity }} dona</span></td>
                        <td><strong>{{ number_format($product->total_revenue) }} so'm</strong></td>
                        <td>{{ number_format($product->total_revenue / $product->total_quantity) }} so'm</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Ma'lumotlar topilmadi</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $topProducts->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection