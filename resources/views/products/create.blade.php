@extends('layouts.app')

@section('title', 'Yangi Mahsulot')
@section('page-title', 'Yangi Mahsulot Yaratish')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus"></i> Yangi Mahsulot</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nomi (Inglizcha) *</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name_uz" class="form-label">Nomi (O'zbekcha) *</label>
                                <input type="text" class="form-control" id="name_uz" name="name_uz" value="{{ old('name_uz') }}" required>
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
                                    <option value="{{ $category->id }}" {{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_uz }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Narx (so'm) *</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}" min="0" step="100" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Tavsif (Inglizcha)</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description_uz" class="form-label">Tavsif (O'zbekcha)</label>
                        <textarea class="form-control" id="description_uz" name="description_uz" rows="3">{{ old('description_uz') }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="preparation_time" class="form-label">Tayyorlash Vaqti (daqiqa) *</label>
                                <input type="number" class="form-control" id="preparation_time" name="preparation_time" value="{{ old('preparation_time', 15) }}" min="1" required>
                                <small class="text-muted">Taxminiy tayyorlash vaqti daqiqalarda</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Mahsulot Rasmi</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Maksimal 2MB, JPEG, PNG, JPG, GIF formatida</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ingredients" class="form-label">Tarkibi (vergul bilan ajrating)</label>
                        <input type="text" class="form-control" id="ingredients" name="ingredients" 
                               value="{{ old('ingredients') }}" 
                               placeholder="Masalan: go'sht, sabzavot, ziravorlar, tuz">
                        <small class="text-muted">Har bir tarkibiy qismni vergul bilan ajrating</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available">
                                    Mavjud
                                </label>
                                <small class="text-muted d-block">Mahsulot sotuvga tayyormi?</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_popular">
                                    Mashhur mahsulot
                                </label>
                                <small class="text-muted d-block">Ommabop mahsulotlar ro'yxatiga qo'shish</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                        <div>
                            <button type="submit" name="action" value="save" class="btn btn-primary me-2">
                                <i class="fas fa-save"></i> Saqlash
                            </button>
                            <button type="submit" name="action" value="save_and_new" class="btn btn-success">
                                <i class="fas fa-plus"></i> Saqlash va Yangi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Real-time price formatting
document.getElementById('price').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    if (value) {
        this.value = parseInt(value).toLocaleString();
    }
});

// Auto-generate English name from Uzbek
document.getElementById('name_uz').addEventListener('blur', function() {
    const nameEn = document.getElementById('name');
    if (!nameEn.value && this.value) {
        // Simple transliteration - you can improve this
        let transliterated = this.value
            .replace(/sh/g, 'sh')
            .replace(/ch/g, 'ch')
            .replace(/'/g, "'");
        nameEn.value = transliterated;
    }
});

// Image preview
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Show file size
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const small = this.parentNode.querySelector('small');
        if (fileSize > 2) {
            small.textContent = `Fayl o'lchami juda katta: ${fileSize}MB. Maksimal 2MB`;
            small.className = 'text-danger';
        } else {
            small.textContent = `Tanlangan: ${file.name} (${fileSize}MB)`;
            small.className = 'text-success';
        }
    }
});
</script>
@endsection