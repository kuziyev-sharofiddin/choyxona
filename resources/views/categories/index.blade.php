<!-- resources/views/categories/index.blade.php -->
@extends('layouts.app')

@section('title', 'Kategoriyalar')
@section('page-title', 'Kategoriyalar Boshqaruvi')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Mahsulot Kategoriyalari</h4>
                <p class="text-muted">Jami: {{ $categories->total() }} ta kategoriya</p>
            </div>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Kategoriya
            </a>
        </div>
    </div>
</div>

<!-- Categories Grid -->
<div class="row">
    @foreach($categories as $category)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            @if($category->image)
            <img src="{{ Storage::url($category->image) }}" class="card-img-top" 
                 style="height: 200px; object-fit: cover;">
            @else
            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                 style="height: 200px;">
                <i class="fas fa-folder fa-3x text-muted"></i>
            </div>
            @endif
            
            <div class="card-body">
                <h5 class="card-title">{{ $category->name_uz }}</h5>
                <p class="card-text text-muted">
                    {{ Str::limit($category->description, 100) }}
                </p>
                
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="text-primary">{{ $category->products_count }}</h6>
                        <small class="text-muted">Mahsulotlar</small>
                    </div>
                    <div class="col-6">
                        <h6 class="text-success">{{ $category->sort_order }}</h6>
                        <small class="text-muted">Tartib</small>
                    </div>
                </div>
                
                <div class="mt-3">
                    <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                        {{ $category->is_active ? 'Faol' : 'Nofaol' }}
                    </span>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="btn-group w-100">
                    <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye"></i> Ko'rish
                    </a>
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                    @if($category->products_count == 0)
                    <button class="btn btn-outline-danger btn-sm" onclick="if(confirmDelete()) document.getElementById('delete-{{ $category->id }}').submit();">
                        <i class="fas fa-trash"></i> O'chirish
                    </button>
                    <form id="delete-{{ $category->id }}" action="{{ route('categories.destroy', $category) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $categories->links() }}
</div>
@endsection