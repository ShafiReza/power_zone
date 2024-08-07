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
use App\Models\PaymentHistory;
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
            $regularCustomer = RegularCustomer::find($customerId);
            if ($regularCustomer) {
                $bill->regular_customer_id = $customerId;
                $bill->customer_name = $regularCustomer->name;
            } else {
                // Handle the case where the regular customer is not found
                return response()->json(['error' => 'Regular customer not found'], 404);
            }
        } elseif ($customerType == 'irregularCustomer') {
            $irregularCustomer = IrregularCustomer::find($customerId);
            if ($irregularCustomer) {
                $bill->irregular_customer_id = $customerId;
                $bill->customer_name = $irregularCustomer->name;
            } else {
                // Handle the case where the irregular customer is not found
                return response()->json(['error' => 'Irregular customer not found'], 404);
            }
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
            $product = Product::where('name', $productName)->first();
            if ($product) {
                $billItem->product_id = $product->id;
            } else {
                // Handle the case where the product is not found
                return response()->json(['error' => 'Product not found'], 404);
            }
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

            $product = Product::where('name', $productName)->first();
            if ($product) {
                $product->quantity -= $quantity;
                $product->total_amount = $product->quantity * $product->purchase_price;
                $product->save();
            }
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

    // public function markAsPaid(Request $request)
    // {
    //     $bill = Bill::findOrFail($request->bill_id);

    //     // Check if this is the first payment
    //     if ($bill->status === 'paid') {
    //         $receivableAmount = $request->receivable_amount;
    //         $dueAmount = $bill->final_amount - $receivableAmount;
    //         $status = ($dueAmount <= 0) ? 'paid' : 'partial';
    //     } else {
    //         // This is not the first payment, update bill amount to due amount
    //         $billAmount = $bill->due_amount;
    //         $receivableAmount = $request->receivable_amount;
    //         $dueAmount = $billAmount - $receivableAmount;
    //         $status = ($dueAmount <= 0) ? 'paid' : 'partial';
    //     }

    //     // Update the bill
    //     $bill->due_amount = $dueAmount;
    //     $bill->status = $status;
    //     $bill->save();

    //     // Create a new payment record
    //     $paymentHistory = PaymentHistory::create([
    //         'bill_id' => $bill->id,
    //         'description' => $request->description,
    //         'bill_amount' => $dueAmount,
    //         'receivable_amount' => $receivableAmount,
    //         'due_amount' => $dueAmount,
    //     ]);

    //     return response()->json(['success' => true, 'dueAmount' => $paymentHistory->due_amount]);
    // }

    public function markPaid(Request $request)
    {
        $bill = Bill::findOrFail($request->bill_id);

        // Check if this is the first payment
        if ($bill->status === 'pending') {
            $billAmount = $bill->final_amount;
        } else {
            // This is not the first payment, update bill amount to due amount
            $billAmount = $bill->due_amount;
        }

        $receivableAmount = $request->receivable_amount;
        $dueAmount = $billAmount - $receivableAmount;
        $status = ($dueAmount <= 0) ? 'paid' : 'partial';

        // Update the bill
        $bill->due_amount = $dueAmount;
        $bill->status = $status;
        $bill->save();

        // Create a new payment record
        $paymentHistory = PaymentHistory::create([
            'bill_id' => $bill->id,
            'description' => $request->description,
            'bill_amount' => $billAmount,
            'receivable_amount' => $receivableAmount,
            'due_amount' => $dueAmount,
        ]);

        return response()->json(['success' => true, 'dueAmount' => $paymentHistory->due_amount]);
    }






    public function paymentHistory($billId)
    {
        $payments = PaymentHistory::where('bill_id', $billId)->latest()->get();
        return view('admin.bill.payment_history', compact('payments'));
    }

    public function PaymentDestroy($id)
    {
        $payment = PaymentHistory::findOrFail($id);
        $payment->delete();

        return redirect()->back()->with('success', 'Payment record deleted successfully.');
    }


}
