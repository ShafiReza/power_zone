<?php

namespace App\Http\Controllers;

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
        $title = "Product";
        $query = Product::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category . '%');
            });
        }

        $products = $query->get();

        return view('admin.product.index', compact('products','title'));
    }

    public function create()
    {
        $title = "Product";

        $categories = Category::where('status', 'active')->get(); // Fetch only active categories
        return view('admin.product.create', compact('categories','title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'details' => 'nullable',
            'category_id' => 'required',
            'purchase_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'wholesale_price' => 'required|numeric',
            'part_no' => 'nullable|string',

        ]);

        $total_amount = $request->quantity * $request->purchase_price;

        // Create the product with the total_amount
        Product::create(array_merge($request->all(), ['total_amount' => $total_amount]));

        return redirect()->route('admin.product.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $title = "Product";
        $categories = Category::where('status', 'active')->get(); // Fetch only active categories
        return view('admin.product.edit', compact('product', 'categories','title'));
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
        $quantity = $request->quantity;
        $purchasePrice = $product->purchase_price; // Make sure this field is available

        $totalAmount = $quantity * $purchasePrice;

        $product->update([
            'quantity' => $quantity,
            'total_amount' => $totalAmount,
        ]);

        return response()->json(['success' => true]);
    }

    public function sales($id)
    {

        $title = "Sales List";
        // Fetch bills with billItems containing the specified product ID
        $bills = Bill::whereHas('billItems', function ($query) use ($id) {
            $query->where('product_id', $id); // Assuming 'product_id' is the correct field
        })->with(['billItems' => function ($query) use ($id) {
            $query->where('product_id', $id); // Assuming 'product_id' is the correct field
        }, 'billItems2'])->get();

        // Pass the product ID to the view
        return view('admin.product.sales', compact('bills', 'id','title'));
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
        $product->total_amount = $product->quantity * $product->purchase_price;
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
        $title = "StocK List";
        $product = Product::findOrFail($id);
        $stockEntries = StockEntry::where('product_id', $id)->get();

        return view('admin.product.stockList', compact('product', 'stockEntries','title'));
    }

}
