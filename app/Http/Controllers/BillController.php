<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\RegularCustomer;
use App\Models\IrregularCustomer;
use App\Models\Product;
use App\Models\Bill;
use App\Models\MonthlyBill;
use App\Models\BillItem;
use App\Models\BillItem2;
use App\Models\PaymentHistory;

use Carbon\Carbon;


class BillController extends Controller
{
    public function index(Request $request)
    {

        $bills = Bill::with(['regularCustomer', 'irregularCustomer'])->get();
        $paymentHistories = PaymentHistory::whereIn('bill_id', $bills->pluck('id'))->get();

        // Determine if any payment history has a due_amount > 0
        $hasPartial = $paymentHistories->contains(function ($paymentHistory) {
            return $paymentHistory->due_amount != 0;
        });
        $query = Bill::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bills = $query->with(['regularCustomer', 'irregularCustomer'])->get();

        // dd($bills);


        return view('admin.bill.index', compact('bills', 'hasPartial'));
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
             // Fetch the latest bill of the customer that is marked as partial
             $previousBills = Bill::where('regular_customer_id', $id)
             ->where('status', 'partial')
             ->get();

         // Fetch customer details, products, etc.
         $customer = RegularCustomer::find($id);
         //$products = Product::all();

        // Determine whether the customer is regular or irregular and fetch customer details accordingly
        $customer = $bill->regular_customer_id ? RegularCustomer::find($bill->regular_customer_id) : IrregularCustomer::find($bill->irregular_customer_id);

        $bill = Bill::findOrFail($id);
        $finalAmount = $bill->billItems2->first()->final_amount ?? 'N/A';
        $previousBills = PaymentHistory::where('bill_id', $id)->get();

        // dd($previousBills);



        return view('admin.bill.invoice', compact('customer', 'products', 'bill', 'billItems2', 'previousBills'));
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
        if ($product) {
            return response()->json([
                'name' => $product->name,
                'sell_price' => $product->sell_price,
                'quantity' => $product->quantity // Add quantity here
            ]);
        }
        return response()->json($product);
    }



    // BillController.php

    // public function store(Request $request)
    // {

    //     //dd($request->all());
    //     // Create a new bill instance
    //     $customerType = $request->input('customerType');
    //     $customerId = $request->input('customerName');

    //     $bill = new Bill();
    //     if ($customerType == 'regularCustomer') {
    //         $regularCustomer = RegularCustomer::find($customerId);
    //         if ($regularCustomer) {
    //             $bill->regular_customer_id = $customerId;
    //             $bill->customer_name = $regularCustomer->name;
    //         } else {
    //             // Handle the case where the regular customer is not found
    //             return response()->json(['error' => 'Regular customer not found'], 404);
    //         }
    //     } elseif ($customerType == 'irregularCustomer') {
    //         $irregularCustomer = IrregularCustomer::find($customerId);
    //         if ($irregularCustomer) {
    //             $bill->irregular_customer_id = $customerId;
    //             $bill->customer_name = $irregularCustomer->name;
    //         } else {
    //             // Handle the case where the irregular customer is not found
    //             return response()->json(['error' => 'Irregular customer not found'], 404);
    //         }
    //     }


    //     $bill->bill_type = $request->input('billType');
    //     $bill->bill_date = $request->input('billDate');
    //     $bill->final_amount = 0;

    //     // Save the bill to generate an ID
    //     $bill->save();

    //     // Create bill items
    //     $productNames = $request->input('product_name', []);
    //     $descriptions = $request->input('description', []);
    //     $quantities = $request->input('quantity', []);
    //     $unitPrices = $request->input('unitPrice', []);
    //     $discounts = $request->input('discount', []);
    //     $discountTypes = $request->input('discountType', []);

    //     foreach ($productNames as $index => $productName) {
    //         $quantity = $quantities[$index];
    //         $unitPrice = $unitPrices[$index];
    //         $discount = $discounts[$index];
    //         $discountType = $discountTypes[$index];

    //         $totalAmount = $quantity * $unitPrice;
    //         if ($discountType === 'Percentage') {
    //             $totalAmount -= $totalAmount * ($discount / 100);
    //         } else {
    //             $totalAmount -= $discount;
    //         }

    //         $billItem = new BillItem();
    //         $billItem->bill_id = $bill->id;
    //         $product = Product::where('name', $productName)->first();
    //         if ($product) {
    //             $billItem->product_id = $product->id;
    //         } else {
    //             // Handle the case where the product is not found
    //             return response()->json(['error' => 'Product not found'], 404);
    //         }
    //         $billItem->product_name = $productName;
    //         $billItem->description = $descriptions[$index];
    //         $billItem->quantity = $quantity;
    //         $billItem->unit_price = $unitPrice;
    //         $billItem->discount = $discount;
    //         $billItem->discount_type = $discountType;
    //         $billItem->total_amount = $totalAmount;
    //         $billItem->save();

    //         // Update final amount
    //         $bill->final_amount += $totalAmount;

    //         $product = Product::where('name', $productName)->first();
    //         if ($product) {
    //             $product->quantity -= $quantity;
    //             $product->total_amount = $product->quantity * $product->purchase_price;
    //             $product->save();
    //         }
    //     }

    //     // Create bill items 2
    //     $billItems2 = $request->input('bill_items2', []);
    //     foreach ($billItems2 as $billItem2Data) {
    //         $billItem2 = new BillItem2();
    //         $billItem2->bill_id = $bill->id;
    //         $billItem2->discount_type = $billItem2Data['discount_type'];
    //         $billItem2->discount = $billItem2Data['discount'];
    //         $billItem2->vat = $billItem2Data['vat'];
    //         $billItem2->receivable_amount = $billItem2Data['receivable_amount'];
    //         $billItem2->due_amount = $billItem2Data['due_amount'];
    //         $billItem2->final_amount = $billItem2Data['final_amount'];
    //         $billItem2->save();

    //         // Update final amount
    //         if ($billItem2Data['discount_type'] === 'Percentage') {
    //             $bill->final_amount -= $bill->final_amount * ($billItem2Data['discount'] / 100);
    //         } else {
    //             $bill->final_amount -= $billItem2Data['discount'];
    //         }
    //         $bill->final_amount += $billItem2Data['vat'];
    //     }

    //     // Save the final bill amount
    //     $bill->save();

    //     // Return a success response
    //     return redirect()->route('admin.bill.index')->with('success', 'Bill created successfully!');
    // }

    public function store(Request $request)
    {
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
                return response()->json(['error' => 'Regular customer not found'], 404);
            }
        } elseif ($customerType == 'irregularCustomer') {
            $irregularCustomer = IrregularCustomer::find($customerId);
            if ($irregularCustomer) {
                $bill->irregular_customer_id = $customerId;
                $bill->customer_name = $irregularCustomer->name;
            } else {
                return response()->json(['error' => 'Irregular customer not found'], 404);
            }
        }

        $bill->bill_type = $request->input('billType');
        $bill->bill_date = $request->input('billDate');
        $bill->type = 'Ongoing';
        $bill->final_amount = 0; // Initialize final amount

        $bill->save(); // Save the bill to generate an ID

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
            $billItem2->receivable_amount = $billItem2Data['receivable_amount'];
            $billItem2->due_amount = $billItem2Data['due_amount'];
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

                // Create a payment history entry
                PaymentHistory::create([
                    'bill_id' => $bill->id,
                    'receive_date' => $request->input('billDate'),
                    'description' => 'Initial Payment',
                    'bill_amount' => $billItem2Data['final_amount'],
                    'receivable_amount' =>$billItem2Data['receivable_amount'],
                    'paid_amount' => $billItem2Data['final_amount'],
                    'due_amount' =>$billItem2Data['final_amount'] - $billItem2Data['receivable_amount'],
                ]);


        return redirect()->route('admin.bill.index')->with('success', 'Bill created successfully.');
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
        foreach ($bill->billItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->quantity += $item->quantity; // Restore quantity
                $product->total_amount = $product->quantity * $product->purchase_price; // Update total_amount
                $product->save();
            }
        }

        $bill->delete();
        return redirect()->route('admin.bill.index')
            ->with('success', 'Bill deleted successfully.');
    }


    // public function markPaid(Request $request)
    // {
    //     //dd($request->all());
    //     $bill = Bill::findOrFail($request->bill_id);

    //     if ($bill->status === 'pending') {
    //         $billAmount = $request->bill_amount; // Make sure this field exists and has a value
    //     } else {

    //         $billAmount = $request->due_amount;
    //     }
    //     //$billAmount = $request->bill_amount;
    //     //dd($billAmount);

    //     $receivableAmount = $request->receivable_amount;
    //     $dueAmount = $billAmount - $receivableAmount;

    //     // Check if due amount is greater than 0 and status should be paid
    //     // if ($dueAmount > 0) {
    //     //     return response()->json(['success' => false, 'error' => 'Due amount must be zero to mark as Paid.'], 400);
    //     // }

    //     $paidAmount = $receivableAmount; // Set paid amount to the receivable amount

    //     $bill->type = 'Ongoing';
    //     $bill->due_amount = $dueAmount;
    //     $bill->paid_amount = $billAmount;
    //     $bill->status = 'paid';

    //     // Create a payment history entry
    //     PaymentHistory::create([
    //         'bill_id' => $bill->id,
    //         'receive_date' => now(),
    //         'description' => $request->input('description'),
    //         'bill_amount' => $billAmount, // Ensure this is the correct amount field
    //         'receivable_amount' => $receivableAmount,
    //         'paid_amount' => $paidAmount,
    //         'due_amount' => $dueAmount,
    //     ]);

    //     // Update bill status based on the new due amount
    //     if ($dueAmount > 0) {
    //         $bill->status = 'Partial';
    //     } else {
    //         $bill->status = 'Paid';
    //     }

    //     $bill->save();
    //     return response()->json([
    //         'success' => true,
    //         'due_amount' => $dueAmount,
    //         'bill_id' => $bill->id,
    //     ]);
    // }
    public function markPaid(Request $request)
    {
        $bill = Bill::findOrFail($request->bill_id);

        $billAmount = $request->bill_amount;
        $receivableAmount = $request->receivable_amount;
        $dueAmount = $billAmount - $receivableAmount;

        // Update bill's due amount and status
        $bill->due_amount = $dueAmount;
        $bill->status = ($dueAmount > 0) ? 'Partial' : 'Paid';
        $bill->save();

        // Create a payment history entry
        PaymentHistory::create([
            'bill_id' => $bill->id,
            'receive_date' => $request->input('receive_date'),
            'description' => $request->input('description'),
            'bill_amount' => $billAmount,
            'receivable_amount' => $receivableAmount,
            'paid_amount' => $request->input('paid_amount'),
            'due_amount' =>$request->input('due_amount'),
        ]);

        BillItem2::where('bill_id',$bill->id)->update(['due_amount' =>$request->input('due_amount')]);
        Bill::where('id',$bill->id)->update(['due_amount' =>$request->input('due_amount')]);


        return redirect()->route('admin.bill.index')->with('success', 'Bill marked as paid successfully.');

    }



    public function paymentHistory($billId)
    {
        $bill = Bill::findOrFail($billId);
        $finalAmount = $bill->billItems2->first()->final_amount ?? 'N/A';
        $payments = PaymentHistory::where('bill_id', $billId)->latest()->get();

        $hasPartial = $payments->where('due_amount', '>', 0)->isNotEmpty();
        return view('admin.bill.payment_history', compact('payments', 'bill', 'finalAmount', 'hasPartial'));
    }


    public function PaymentDestroy($id)
    {
        $payment = PaymentHistory::findOrFail($id);
        $payment->delete();

        return redirect()->back()->with('success', 'Payment record deleted successfully.');
    }
}
