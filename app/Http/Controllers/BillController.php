<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\RegularCustomer;
use App\Models\IrregularCustomer;
use App\Models\Product;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\BillItem2;
use App\Models\BillDiscount;
use App\Models\BillVat;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::all();
        return view('admin.bill.index', compact('bills'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('admin.bill.create', compact('customers', 'products'));
    }

    public function getCustomers(Request $request)
    {
        $customerType = $request->input('customerType');
        if ($customerType == 'regularCustomer') {
            $customers = RegularCustomer::all();
        } elseif ($customerType == 'irregularCustomer') {
            $customers = IrregularCustomer::all();
        } else {
            $customers = [];
        }
        return response()->json($customers);
    }
    public function getProduct(Request $request)
    {
        $productId = $request->input('productId');
        $product = Product::find($productId);
        return response()->json($product);
    }




   // BillController.php

   public function store(Request $request)
   {
   // dd($request->all(), $request->input('bill_items2'));
    //dd($request->all());

       // Validate the request data
       $request->validate([
           'customer_id' => 'required',
           'bill_type' => 'required',
           'bill_date' => 'required',
           'products' => 'required|array',
           'products.*.product_name' => 'required',
           'products.*.description' => 'nullable',
           'products.*.quantity' => 'required|numeric',
           'products.*.unit_price' => 'required|numeric',
           'products.*.discount' => 'nullable|numeric',
           'products.*.discount_type' => 'nullable',
           'bill_items2' => 'required|array',
           'bill_items2.*.discount_type' => 'required',
           'bill_items2.*.discount' => 'required|numeric',
           'bill_items2.*.vat' => 'required|numeric',
       ]);

       // Create a new bill instance
       $bill = new Bill();
       $bill->customer_id = $request->input('customer_id');
       $bill->bill_type = $request->input('bill_type');
       $bill->bill_date = $request->input('bill_date');
       $bill->final_amount = 0; // Initialize final amount to 0
       $bill->save();

       // Create bill items
       foreach ($request->input('products') as $product) {
           $billItem = new BillItem();
           $billItem->bill_id = $bill->id;
           $billItem->product_name = $product['product_name'];
           $billItem->description = $product['description'];
           $billItem->quantity = $product['quantity'];
           $billItem->unit_price = $product['unit_price'];
           $billItem->discount = $product['discount'];
           $billItem->discount_type = $product['discount_type'];
           $billItem->total_amount = $product['quantity'] * $product['unit_price'];
           $billItem->save();

           // Update final amount
           $bill->final_amount += $billItem->total_amount;

       }

       // Create bill items 2
       foreach ($request->input('bill_items2') as $billItem2) {
           $billItem2Model = new BillItem2();
           $billItem2Model->bill_id = $bill->id;
           $billItem2Model->discount_type = $billItem2['discount_type'];
           $billItem2Model->discount = $billItem2['discount'];
           $billItem2Model->vat = $billItem2['vat'];
           $billItem2Model->save();

           // Update final amount
           if ($billItem2['discount_type'] == 'Percentage') {
               $bill->final_amount -= $bill->final_amount * ($billItem2['discount'] / 100);
           } else {
               $bill->final_amount -= $billItem2['discount'];
           }
           $bill->final_amount += $billItem2['vat'];

       }

       $bill->save();


       // Return a success response
       return redirect()->route('admin.bill.index')->with('success', 'Bill created successfully!');
   }

    public function edit($id)
    {
        $bill = Bill::find($id);
        $customers = Customer::all();
        $products = Product::all();
        return view('admin.bill.edit', compact('bill', 'customers', 'products'));
    }


    public function destroy($id)
    {
        $bill = Bill::find($id);
        $bill->delete();
        return redirect()->route('bill.index');
    }
}
