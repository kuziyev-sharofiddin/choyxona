<!-- resources/views/categories/show.blade.php -->
@extends('layouts.app')

@section('title', 'Kategoriya Ma\'lumotlari')
@section('page-title', $category->name_uz)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-folder"></i> Kategoriya Ma'lumotlari</h5>
                <div>
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($category->image)
                        <img src="{{ Storage::url($category->image) }}" class="img-fluid rounded">
                        @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-folder fa-3x text-muted"></i>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless">
                            <tr>
                                <th>Nomi (O'zbekcha):</th>
                                <td><h5>{{ $category->name_uz }}</h5></td>
                            </tr>
                            <tr>
                                <th>Nomi (Inglizcha):</th>
                                <td>{{ $category->name }}</td>
                            </tr>
                            <tr>
                                <th>Tavsif:</th>
                                <td>{{ $category->description ?: 'Tavsif kiritilmagan' }}</td>
                            </tr>
                            <tr>
                                <th>Tartib raqami:</th>
                                <td><span class="badge bg-info">{{ $category->sort_order }}</span></td>
                            </tr>
                            <tr>
                                <th>Holat:</th>
                                <td>
                                    <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                        {{ $category->is_active ? 'Faol' : 'Nofaol' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Yaratilgan:</th>
                                <td>{{ $category->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products in Category -->
        @if($category->products->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-boxes"></i> Kategoriya Mahsulotlari</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($category->products as $product)
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6>{{ $product->name_uz }}</h6>
                                        <p class="text-success mb-1"><strong>{{ number_format($product->price) }} so'm</strong></p>
                                        <small class="text-muted">~{{ $product->preparation_time }} daqiqa</small>
                                    </div>
                                    <div>
                                        @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" class="rounded" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div class="mt-1">
                                            <span class="badge bg-{{ $product->is_available ? 'success' : 'danger' }}">
                                                {{ $product->is_available ? 'Mavjud' : 'Yo\'q' }}
                                            </span>
                                            @if($product->is_popular)
                                                <span class="badge bg-warning"><i class="fas fa-star"></i></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm">
                                        Ko'rish
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Statistics -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistika</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="text-primary">{{ $stats['total_products'] }}</h3>
                    <p class="text-muted mb-0">Jami mahsulotlar</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-success">{{ $stats['available_products'] }}</h3>
                    <p class="text-muted mb-0">Mavjud mahsulotlar</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-warning">{{ number_format($stats['avg_price']) }}</h3>
                    <p class="text-muted mb-0">O'rtacha narx (so'm)</p>
                </div>
                
                <div class="text-center">
                    <h3 class="text-info">{{ $stats['popular_products'] }}</h3>
                    <p class="text-muted mb-0">Mashhur mahsulotlar</p>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('products.create', ['category_id' => $category->id]) }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Yangi Mahsulot
                    </a>
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Kategoriyani Tahrirlash
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection