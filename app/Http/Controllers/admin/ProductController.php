<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\BillItem2;

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

    public function updateQuantity(Request $request, Product $product)
{
    $product->update([
        'quantity' => $request->quantity,
        'total_amount' => $request->total_amount
    ]);

    return response()->json(['success' => true]);
}public function sales($id)
{

    $bill = Bill::with(['billItems', 'billItems2'])->find($id);
    //dd($bill);
    $billItems = BillItem::where('bill_id', $id)->get();
   // dd($billItems);
    $billItem2 = BillItem2::where('bill_id', $id)->get();
   // dd($billItem2);

    return view('admin.product.sales', compact('bill', 'billItems', 'billItem2'));
}


}

