@extends('layouts.app')

@section('title', 'Mahsulotlar')
@section('page-title', 'Mahsulotlar Boshqaruvi')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Mahsulotlar</h4>
                <p class="text-muted">Jami: {{ $products->total() }} ta mahsulot</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Mahsulot
            </a>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('products.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Qidirish</label>
                        <input type="text" class="form-control" name="search" 
                               value="{{ request('search') }}" placeholder="Mahsulot nomi...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Kategoriya</label>
                        <select class="form-control" name="category">
                            <option value="">Barcha kategoriyalar</option>
                            @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name_uz }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Holat</label>
                        <select class="form-control" name="status">
                            <option value="">Barcha</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Mavjud</option>
                            <option value="unavailable" {{ request('status') == 'unavailable' ? 'selected' : '' }}>Mavjud emas</option>
                        </select>
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

<!-- Products Grid -->
<div class="row">
    @forelse($products as $product)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card product-card h-100">
            @if($product->image)
            <img src="{{ Storage::url($product->image) }}" class="card-img-top" 
                 style="height: 200px; object-fit: cover;">
            @else
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                 style="height: 200px;">
                <i class="fas fa-image fa-3x text-muted"></i>
            </div>
            @endif
            
            <div class="card-body">
                <h5 class="card-title">{{ $product->name_uz }}</h5>
                <p class="card-text text-muted">
                    {{ Str::limit($product->description_uz ?? $product->description, 100) }}
                </p>
                
                <div class="mb-2">
                    <span class="badge bg-secondary">{{ $product->category->name_uz }}</span>
                    @if($product->is_popular)
                        <span class="badge bg-warning"><i class="fas fa-star"></i> Mashhur</span>
                    @endif
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="text-primary mb-0">{{ number_format($product->price) }} so'm</h6>
                        <small class="text-muted">~{{ $product->preparation_time }} daqiqa</small>
                    </div>
                    <div>
                        @if($product->is_available)
                            <span class="badge bg-success">Mavjud</span>
                        @else
                            <span class="badge bg-danger">Mavjud emas</span>
                        @endif
                    </div>
                </div>
                
                @if($product->ingredients && count($product->ingredients) > 0)
                <div class="mb-2">
                    <small class="text-muted">
                        <strong>Tarkib:</strong> {{ implode(', ', array_slice($product->ingredients, 0, 3)) }}
                        @if(count($product->ingredients) > 3) ... @endif
                    </small>
                </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div class="btn-group w-100">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye"></i> Ko'rish
                    </a>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    <form action="{{ route('products.toggle', $product) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline-{{ $product->is_available ? 'warning' : 'success' }} btn-sm">
                            <i class="fas fa-{{ $product->is_available ? 'pause' : 'play' }}"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="fas fa-box fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Mahsulotlar topilmadi</h5>
            <p class="text-muted">Yangi mahsulot qo'shish uchun yuqoridagi tugmani bosing</p>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $products->appends(request()->query())->links() }}
</div>
@endsection