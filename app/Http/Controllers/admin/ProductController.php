<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category; // Import the Category model if not already imported

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->get(); // Fetch only active categories
        return view('admin.product.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'details' => 'nullable',
            'brand_name' => 'required',
            'category_id' => 'required',
            'purchase_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'wholesale_price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);
        $total_amount = $request->quantity * $request->purchase_price;

        // Create the product with the total_amount
        Product::create(array_merge($request->all(), ['total_amount' => $total_amount]));

        return redirect()->route('admin.product.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->get(); // Fetch only active categories
        return view('admin.product.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'details' => 'nullable',
            'brand_name' => 'required',
            'category_id' => 'required',
            'purchase_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'wholesale_price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $product->update($request->all());
        return redirect()->route('admin.product.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.product.index')->with('success', 'Product deleted successfully.');
    }
    public function toggleStatus(Product $product)
    {
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();

        return redirect()->route('admin.product.index')->with('success', 'Product status updated successfully.');
    }
}
