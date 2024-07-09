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
use Carbon\Carbon;


class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with(['regularCustomer', 'irregularCustomer'])->get();

        return view('admin.bill.index', compact('bills'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('admin.bill.create', ['customers' => $customers, 'products' => $products]);
    }


    public function invoice($id)
    {
        // Fetch bill items related to the bill
        $products = BillItem::where('bill_id', $id)->get();
        $billItems2 = BillItem2::where('bill_id', $id)->get();
        // Fetch the bill details
        $bill = Bill::find($id);

        // Determine whether the customer is regular or irregular and fetch customer details accordingly
        $customer = $bill->regular_customer_id ? RegularCustomer::find($bill->regular_customer_id) : IrregularCustomer::find($bill->irregular_customer_id);

        return view('admin.bill.invoice', compact('customer', 'products', 'bill', 'billItems2'));
    }
    public function quotation($id)
    {
        // Fetch bill items related to the bill
        $products = BillItem::where('bill_id', $id)->get();
        $billItems2 = BillItem2::where('bill_id', $id)->get();
        // Fetch the bill details
        $bill = Bill::find($id);

        // Determine whether the customer is regular or irregular and fetch customer details accordingly
        $customer = $bill->regular_customer_id ? RegularCustomer::find($bill->regular_customer_id) : IrregularCustomer::find($bill->irregular_customer_id);

        return view('admin.bill.quotation', compact('customer', 'products', 'bill', 'billItems2'));
    }

    public function challan($id)
    {
        // Fetch bill items related to the bill
        $products = BillItem::where('bill_id', $id)->get();
        $billItems2 = BillItem2::where('bill_id', $id)->get();
        // Fetch the bill details
        $bill = Bill::find($id);

        // Determine whether the customer is regular or irregular and fetch customer details accordingly
        $customer = $bill->regular_customer_id ? RegularCustomer::find($bill->regular_customer_id) : IrregularCustomer::find($bill->irregular_customer_id);

        return view('admin.bill.challan', compact('customer', 'products', 'bill', 'billItems2'));
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

        //dd($request->all());
        // Create a new bill instance
        $customerType = $request->input('customerType');
        $customerId = $request->input('customerName');

        $bill = new Bill();
        if ($customerType == 'regularCustomer') {
            $bill->regular_customer_id = $customerId;
            $bill->customer_name = RegularCustomer::find($customerId)->name;
            // dd(RegularCustomer::find($customerId)->name);
        } elseif ($customerType == 'irregularCustomer') {
            $bill->irregular_customer_id = $customerId;
            $bill->customer_name = IrregularCustomer::find($customerId)->name;
        }


        $bill->bill_type = $request->input('billType');
        $bill->bill_date = $request->input('billDate');
        $bill->final_amount = 0;

        // Save the bill to generate an ID
        $bill->save();

        // Create bill items
        $productNames = $request->input('product_name', []);
        $descriptions = $request->input('description', []);
        $quantities = $request->input('quantity', []);
        $unitPrices = $request->input('unitPrice', []);
        $discounts = $request->input('discount', []);
        $discountTypes = $request->input('discountType', []);

        foreach ($productNames as $index => $productName) {
            $quantity = $quantities[$index];
            $unitPrice = $unitPrices[$index];
            $discount = $discounts[$index];
            $discountType = $discountTypes[$index];

            $totalAmount = $quantity * $unitPrice;
            if ($discountType === 'Percentage') {
                $totalAmount -= $totalAmount * ($discount / 100);
            } else {
                $totalAmount -= $discount;
            }

            $billItem = new BillItem();
            $billItem->bill_id = $bill->id;
            $billItem->product_name = $productName;
            $billItem->description = $descriptions[$index];
            $billItem->quantity = $quantity;
            $billItem->unit_price = $unitPrice;
            $billItem->discount = $discount;
            $billItem->discount_type = $discountType;
            $billItem->total_amount = $totalAmount;
            $billItem->save();

            // Update final amount
            $bill->final_amount += $totalAmount;
        }

        // Create bill items 2
        $billItems2 = $request->input('bill_items2', []);
        foreach ($billItems2 as $billItem2Data) {
            $billItem2 = new BillItem2();
            $billItem2->bill_id = $bill->id;
            $billItem2->discount_type = $billItem2Data['discount_type'];
            $billItem2->discount = $billItem2Data['discount'];
            $billItem2->vat = $billItem2Data['vat'];
            $billItem2->final_amount = $billItem2Data['final_amount'];
            $billItem2->save();

            // Update final amount
            if ($billItem2Data['discount_type'] === 'Percentage') {
                $bill->final_amount -= $bill->final_amount * ($billItem2Data['discount'] / 100);
            } else {
                $bill->final_amount -= $billItem2Data['discount'];
            }
            $bill->final_amount += $billItem2Data['vat'];
        }

        // Save the final bill amount
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


    public function destroy(Bill $bill)
    {
        $bill->delete();
        return redirect()->route('admin.bill.index')
            ->with('success', 'Bill deleted successfully.');
    }




}
