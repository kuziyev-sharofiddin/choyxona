<!-- resources/views/products/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Mahsulotni Tahrirlash')
@section('page-title', 'Mahsulotni Tahrirlash')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Mahsulotni Tahrirlash</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nomi (Inglizcha) *</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name_uz" class="form-label">Nomi (O'zbekcha) *</label>
                                <input type="text" class="form-control" id="name_uz" name="name_uz" value="{{ old('name_uz', $product->name_uz) }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategoriya *</label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <option value="">Kategorialerni tanlang</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_uz }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Narx (so'm) *</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="100" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Tavsif (Inglizcha)</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description_uz" class="form-label">Tavsif (O'zbekcha)</label>
                        <textarea class="form-control" id="description_uz" name="description_uz" rows="3">{{ old('description_uz', $product->description_uz) }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="preparation_time" class="form-label">Tayyorlash Vaqti (daqiqa) *</label>
                                <input type="number" class="form-control" id="preparation_time" name="preparation_time" value="{{ old('preparation_time', $product->preparation_time) }}" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Rasm</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                @if($product->image)
                                <small class="text-muted">Hozirgi rasm: <a href="{{ Storage::url($product->image) }}" target="_blank">Ko'rish</a></small>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ingredients" class="form-label">Tarkibi (vergul bilan ajrating)</label>
                        <input type="text" class="form-control" id="ingredients" name="ingredients" 
                               value="{{ old('ingredients', $product->ingredients ? implode(', ', $product->ingredients) : '') }}" 
                               placeholder="go'sht, sabzavot, ziravorlar">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" 
                                       {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available">
                                    Mavjud
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" value="1" 
                                       {{ old('is_popular', $product->is_popular) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_popular">
                                    Mashhur mahsulot
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Saqlash
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection