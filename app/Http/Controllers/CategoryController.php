<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
                            ->orderBy('sort_order')
                            ->paginate(20);
                            // dd($categories);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_uz' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'required|integer|min:0',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('categories.index')
                        ->with('success', 'Kategoriya muvaffaqiyatli qo\'shildi!');
    }

    public function show(Category $category)
    {
        $category->load(['products' => function($query) {
            $query->where('is_available', true)->orderBy('name_uz');
        }]);

        $stats = [
            'total_products' => $category->products()->count(),
            'available_products' => $category->products()->where('is_available', true)->count(),
            'avg_price' => $category->products()->avg('price'),
            'popular_products' => $category->products()->where('is_popular', true)->count(),
        ];

        return view('categories.show', compact('category', 'stats'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_uz' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sort_order' => 'required|integer|min:0',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('categories.index')
                        ->with('success', 'Kategoriya muvaffaqiyatli yangilandi!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->withErrors(['error' => 'Ushbu kategoriyada mahsulotlar mavjud, o\'chirib bo\'lmaydi!']);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success', 'Kategoriya o\'chirildi!');
    }
}
