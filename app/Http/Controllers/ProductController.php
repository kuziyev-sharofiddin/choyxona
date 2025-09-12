<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->paginate(20);

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_uz' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preparation_time' => 'required|integer|min:1',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->ingredients) {
            $data['ingredients'] = explode(',', $request->ingredients);
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Mahsulot muvaffaqiyatli qo\'shildi!');
    }

    public function show(Product $product)
{
    $product->load(['category', 'orderItems.order.customer']);
    
    $stats = [
        'total_orders' => $product->orderItems()->count(),
        'total_quantity' => $product->orderItems()->sum('quantity'),
        'total_revenue' => $product->orderItems()->sum('total_price'),
        'avg_rating' => 4.5, // placeholder for future rating system
    ];

    $recentOrders = $product->orderItems()
                           ->with(['order.customer', 'order.reservation.room'])
                           ->orderBy('created_at', 'desc')
                           ->limit(10)
                           ->get();

    return view('products.show', compact('product', 'stats', 'recentOrders'));
}

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_uz' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preparation_time' => 'required|integer|min:1',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->ingredients) {
            $data['ingredients'] = explode(',', $request->ingredients);
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Mahsulot muvaffaqiyatli yangilandi!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Mahsulot o\'chirildi!');
    }

    public function toggleAvailability(Product $product)
    {
        $product->update(['is_available' => !$product->is_available]);

        $status = $product->is_available ? 'mavjud' : 'mavjud emas';
        return back()->with('success', "Mahsulot {$status} deb belgilandi!");
    }
    public function search(Request $request)
    {
        $query = $request->get('q');

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('name_uz', 'LIKE', "%{$query}%")
            ->where('is_available', true)
            ->with('category')
            ->limit(10)
            ->get();

        return response()->json($products);
    }
}
