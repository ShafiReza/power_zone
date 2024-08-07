<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\BillItem2;
use App\Models\StockEntry;
class ProductController extends Controller
{
    public function index(Request $request)
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
            'origin' => 'required',
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

    public function updateQuantity(Request $request, Product $product)
{
    $product->update([
        'quantity' => $request->quantity,
        'total_amount' => $request->total_amount
    ]);

    return response()->json(['success' => true]);
}

public function sales($id)
{
    // Fetch bills with billItems containing the specified product ID
    $bills = Bill::whereHas('billItems', function ($query) use ($id) {
        $query->where('product_id', $id); // Assuming 'product_id' is the correct field
    })->with(['billItems' => function ($query) use ($id) {
        $query->where('product_id', $id); // Assuming 'product_id' is the correct field
    }, 'billItems2'])->get();

    // Pass the product ID to the view
    return view('admin.product.sales', compact('bills', 'id'));
}


public function addProduct(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'entry_date' => 'required|date',
        'description' => 'required|string',
        'quantity' => 'required|integer|min:1',
    ]);

    $product = Product::findOrFail($request->product_id);
    $product->quantity += $request->quantity;
    $product->save();

    StockEntry::create([
        'product_id' => $request->product_id,
        'entry_date' => $request->entry_date,
        'description' => $request->description,
        'quantity' => $request->quantity,
    ]);

    return redirect()->route('admin.product.index')->with('success', 'Product quantity updated successfully.');
}

public function stockList($id)
{
    $product = Product::findOrFail($id);
    $stockEntries = StockEntry::where('product_id', $id)->get();

    return view('admin.product.stockList', compact('product', 'stockEntries'));
}

}

