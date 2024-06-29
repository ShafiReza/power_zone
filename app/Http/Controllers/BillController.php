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
        return view('admin.bill.create', ['customers' => $customers, 'products' => $products]);
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
     //dd($request->all(), $request->input('bill_items2'));
       // dd($request->all());

       // Validate the request data
    //    $request->validate([
    //        'customerType' => 'required',
    //        'customerName' => 'required|exists:customers,id',
    //        'billType' => 'required',
    //        'billDate' => 'required|date',
    //        'products' => 'required|array',
    //        'products.*.product_name' => 'required|string',
    //        'products.*.description' => 'nullable|string',
    //        'products.*.quantity' => 'required|integer|min:1',
    //        'products.*.unit_price' => 'required|numeric|min:0',
    //        'products.*.discount' => 'nullable|numeric|min:0',
    //        'products.*.discount_type' => 'nullable|string|in:Percentage,Flat',
    //        'bill_items2' => 'required|array',
    //        'bill_items2.*.discount_type' => 'required|string|in:Percentage,Flat',
    //        'bill_items2.*.discount' => 'required|numeric|min:0',
    //        'bill_items2.*.vat' => 'required|numeric|min:0',
    //    ]);

       // Create a new bill instance
       $customerType = $request->input('customerType');
       $customerId = $request->input('customerName');

       $bill = new Bill;
       if ($customerType == 'regularCustomer') {
           $bill->regular_customer_id = $customerId;
           $bill->customer_name = RegularCustomer::find($customerId)->name;
       } else if ($customerType == 'irregularCustomer') {
           $bill->irregular_customer_id = $customerId;
           $bill->customer_name = IrregularCustomer::find($customerId)->name;
       }

       $bill->bill_type = $request->input('billType');
       $bill->bill_date = $request->input('billDate');
       $bill->final_amount = 0; // Update this based on your calculations
       $bill->amount = 0; // Update this based on your calculations

       $bill->save();

       $products = $request->input('products', []);
       dd($products );
       // Create bill items
       foreach ($products as $product) {
           if (!is_null($product)) {
               $billItem = new BillItem();
               $billItem->bill_id = $bill->id;
               $billItem->product_name = $product['product_name'];
               $billItem->description = $product['description'] ?? '';
               $billItem->quantity = $product['quantity'];
               $billItem->unit_price = $product['unit_price'];
               $billItem->discount = $product['discount'] ?? 0;
               $billItem->discount_type = $product['discount_type'] ?? 'Flat';
               $billItem->total_amount = $product['quantity'] * $product['unit_price'];
               $billItem->save();

               // Update final amount
               $bill->final_amount += $billItem->total_amount;
           }
       }

       // Create bill items 2
       $billItem2 = new BillItem2();
       $billItem2->bill_id = $bill->id;

       foreach ($request->input('bill_items2', []) as $billItem2Data) {
           if (!is_null($billItem2Data)) {
               $billItem2->discount_type = $billItem2Data['discount_type'];
               $billItem2->discount = $billItem2Data['discount'];
               $billItem2->vat = $billItem2Data['vat'];
               $billItem2->save();

               // Update final amount
               if ($billItem2Data['discount_type'] == 'Percentage') {
                   $bill->final_amount -= $bill->final_amount * ($billItem2Data['discount'] / 100);
               } else {
                   $bill->final_amount -= $billItem2Data['discount'];
               }
               $bill->final_amount += $billItem2Data['vat'];
           }
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
