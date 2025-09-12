<!-- resources/views/products/show.blade.php -->
@extends('layouts.app')

@section('title', 'Mahsulot Ma\'lumotlari')
@section('page-title', $product->name_uz)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box"></i> Mahsulot Ma'lumotlari</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" class="img-fluid rounded">
                        @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h4>{{ $product->name_uz }}</h4>
                        <p class="text-muted">{{ $product->description_uz ?? $product->description }}</p>
                        
                        <table class="table table-borderless">
                            <tr>
                                <th>Kategoriya:</th>
                                <td><span class="badge bg-secondary">{{ $product->category->name_uz }}</span></td>
                            </tr>
                            <tr>
                                <th>Narx:</th>
                                <td><strong class="text-success">{{ number_format($product->price) }} so'm</strong></td>
                            </tr>
                            <tr>
                                <th>Tayyorlash vaqti:</th>
                                <td>~{{ $product->preparation_time }} daqiqa</td>
                            </tr>
                            <tr>
                                <th>Holat:</th>
                                <td>
                                    <span class="badge bg-{{ $product->is_available ? 'success' : 'danger' }}">
                                        {{ $product->is_available ? 'Mavjud' : 'Mavjud emas' }}
                                    </span>
                                    @if($product->is_popular)
                                        <span class="badge bg-warning"><i class="fas fa-star"></i> Mashhur</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        
                        @if($product->ingredients && count($product->ingredients) > 0)
                        <div class="mt-3">
                            <h6>Tarkibi:</h6>
                            <div>
                                @foreach($product->ingredients as $ingredient)
                                    <span class="badge bg-light text-dark me-1">{{ $ingredient }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        @if($recentOrders->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> So'nggi Buyurtmalar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sana</th>
                                <th>Mijoz</th>
                                <th>Xona</th>
                                <th>Miqdor</th>
                                <th>Summa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $item)
                            <tr>
                                <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
                                <td>{{ $item->order->customer->name }}</td>
                                <td>{{ $item->order->reservation->room->name_uz }}</td>
                                <td>{{ $item->quantity }} dona</td>
                                <td>{{ number_format($item->total_price) }} so'm</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                    <h3 class="text-primary">{{ $stats['total_orders'] }}</h3>
                    <p class="text-muted mb-0">Jami buyurtmalar</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-success">{{ $stats['total_quantity'] }}</h3>
                    <p class="text-muted mb-0">Sotilgan miqdor</p>
                </div>
                
                <div class="text-center mb-3">
                    <h3 class="text-warning">{{ number_format($stats['total_revenue']) }}</h3>
                    <p class="text-muted mb-0">Jami daromad (so'm)</p>
                </div>
                
                <div class="text-center">
                    <div class="mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $stats['avg_rating'] ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </div>
                    <p class="text-muted mb-0">O'rtacha baho</p>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    <form action="{{ route('products.toggle', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-{{ $product->is_available ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $product->is_available ? 'pause' : 'play' }}"></i>
                            {{ $product->is_available ? 'Nofaol qilish' : 'Faollashtirish' }}
                        </button>
                    </form>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection