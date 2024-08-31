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
use App\Models\NonInventory;

use Carbon\Carbon;


class BillController extends Controller
{
    public function index(Request $request)
{
    $title = "Bill";
    // Initialize the query with relationships
    $query = Bill::with(['regularCustomer', 'irregularCustomer']);

    // Filter by status if provided
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter by client name if provided
    if ($request->filled('client_name')) {
        $query->whereHas('regularCustomer', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->client_name . '%');
        })->orWhereHas('irregularCustomer', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->client_name . '%');
        });
    }

    $bills = $query->get();

    // Fetch payment histories
    $paymentHistories = PaymentHistory::whereIn('bill_id', $bills->pluck('id'))->get();

    // Determine if any payment history has a due_amount > 0
    $hasPartial = $paymentHistories->contains(function ($paymentHistory) {
        return $paymentHistory->due_amount != 0;
    });

    return view('admin.bill.index', compact('bills', 'hasPartial','title'));
}

    public function create()
    {
        $title = "Bill";
        $customers = Customer::all();
        $products = Product::all();
        return view('admin.bill.create', ['customers' => $customers, 'products' => $products,'title'=>$title]);
    }


    public function invoice($id)
    {
        $title = "Invoice";
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
         $product = Product::all();

        // Determine whether the customer is regular or irregular and fetch customer details accordingly
        $customer = $bill->regular_customer_id ? RegularCustomer::find($bill->regular_customer_id) : IrregularCustomer::find($bill->irregular_customer_id);

        $bill = Bill::findOrFail($id);
        $finalAmount = $bill->billItems2->first()->final_amount ?? 'N/A';
        $previousBills = PaymentHistory::where('bill_id', $id)->get();

        // dd($previousBills);



        return view('admin.bill.invoice', compact('customer', 'products', 'bill', 'billItems2', 'previousBills', 'product','title'));
    }



    public function challan($id)
    {
        $title = "Challan";
        // Fetch bill items related to the bill
        $products = BillItem::where('bill_id', $id)->get();
        $billItems2 = BillItem2::where('bill_id', $id)->get();
        // Fetch the bill details
        $bill = Bill::find($id);

        // Determine whether the customer is regular or irregular and fetch customer details accordingly
        $customer = $bill->regular_customer_id ? RegularCustomer::find($bill->regular_customer_id) : IrregularCustomer::find($bill->irregular_customer_id);

        return view('admin.bill.challan', compact('customer', 'products', 'bill', 'billItems2','title'));
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
    public function getProductsByCategory(Request $request)
    {
        $productType = $request->input('productType');
        // Assuming you have a Product model
        if ($productType === 'inventory') {
            $products = Product::all();
        } elseif($productType === 'noninventory') {
            $products = NonInventory::all();
        }else{
            $products = [];
        }

        return response()->json($products);
    }
    public function getProduct(Request $request)
    {
        $productId = $request->input('productId');
        $productType = $request->input('productType');

        if ($productType === 'inventory') {
            $product = Product::find($productId);
        } else {
            $product = NonInventory::find($productId);
        }

        if ($product) {
            return response()->json([
                'name' => $product->name,
                'sell_price' => $product->sell_price,
                'quantity' => $product->quantity
            ]);
        }

        return response()->json($product);
    }

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
        $category = $request->input('category', ''); // Assuming category is passed with the request

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

            // Determine if the product is from the inventory or non-inventory list
            if ($category === 'inventory') {
                $product = Product::where('name', $productName)->first();
            } else {
                $product = NonInventory::where('name', $productName)->first();
            }

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
    $bill = Bill::with('billItems.product')->findOrFail($id);
    $products = Product::all();
    $customers = Customer::all(); // Assuming you have a Customer model

    return view('admin.bill.edit', compact('bill', 'products', 'customers'));
}

public function update(Request $request, $id)
{
    $bill = Bill::findOrFail($id);
    $bill->update($request->only('customerType', 'customerName', 'billType', 'billDate'));

    // Clear existing bill items
    $bill->billItems()->delete();

    // Recreate bill items
    foreach ($request->product_name as $index => $productName) {
        $bill->billItems()->create([
            'product_id' => $request->product_id[$index],
            'description' => $request->description[$index],
            'quantity' => $request->quantity[$index],
            'unit_price' => $request->unitPrice[$index],
            'discount' => $request->discount[$index],
            'discount_type' => $request->discountType[$index],
            'total_amount' => $request->total_amount[$index],
        ]);
    }

    // Update additional fields
    $bill->update([
        'discount' => $request->bill_items2[0]['discount'],
        'discount_type' => $request->bill_items2[0]['discount_type'],
        'vat' => $request->bill_items2[0]['vat'],
        'receivable_amount' => $request->bill_items2[0]['receivable_amount'],
        'due_amount' => $request->bill_items2[0]['due_amount'],
        'final_amount' => $request->bill_items2[0]['final_amount'],
    ]);

    return redirect()->route('bill.index')->with('success', 'Bill updated successfully');
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
        $title = "Payment History";
        $bill = Bill::findOrFail($billId);
        $finalAmount = $bill->billItems2->first()->final_amount ?? 'N/A';
        $payments = PaymentHistory::where('bill_id', $billId)->latest()->get();

        $hasPartial = $payments->where('due_amount', '>', 0)->isNotEmpty();
        return view('admin.bill.payment_history', compact('payments', 'bill', 'finalAmount', 'hasPartial','title'));
    }


    public function PaymentDestroy($id)
    {
        $payment = PaymentHistory::findOrFail($id);
        $payment->delete();

        return redirect()->back()->with('success', 'Payment record deleted successfully.');
    }
}
