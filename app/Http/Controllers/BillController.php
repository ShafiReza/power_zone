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

    public function save(Request $request)
    {
        $bill = new Bill();
        $bill->customer_id = $request->customer_id;
        $bill->bill_type = $request->bill_type;
        $bill->bill_date = $request->bill_date;
        $bill->final_amount = $request->final_amount;
        $bill->save();

        // Save bill items
        foreach ($request->product_name as $key => $value) {
            $billItem = new BillItem();
            $billItem->bill_id = $bill->id;
            $billItem->product_name = $value;
            $billItem->description = $request->description[$key];
            $billItem->quantity = $request->quantity[$key];
            $billItem->unit_price = $request->unit_price[$key];
            $billItem->discount = $request->discount[$key];
            $billItem->total_amount = $request->total_amount[$key];
            $billItem->save();
        }

        // Save bill discounts
        foreach ($request->discount_type as $key => $value) {
            $billDiscount = new BillDiscount();
            $billDiscount->bill_id = $bill->id;
            $billDiscount->discount_type = $value;
            $billDiscount->discount_amount = $request->discount_amount[$key];
            $billDiscount->save();
        }

        // Save bill vats
        foreach ($request->vat as $key => $value) {
            $billVat = new BillVat();
            $billVat->bill_id = $bill->id;
            $billVat->vat = $value;
            $billVat->save();
        }

        return response()->json(['message' => 'Bill saved successfully']);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'customer_name' => 'required|string',
            'bill_type' => 'required|string',
            'bill_date' => 'required|date',
            'final_amount' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        $bill = Bill::create($validated);
        $bill = new Bill();
        $bill->id = $request->id;
        $bill->bill_type = $request->bill_type;
        $bill->bill_date = $request->bill_date;
        $bill->final_amount = $request->final_amount;
        $bill->save();

        // Save bill items
        foreach ($request->product_name as $key => $value) {
            $billItem = new BillItem();
            $billItem->bill_id = $bill->id;
            $billItem->product_name = $value;
            $billItem->description = $request->description[$key];
            $billItem->quantity = $request->quantity[$key];
            $billItem->unit_price = $request->unit_price[$key];
            $billItem->discount = $request->discount[$key];
            $billItem->total_amount = $request->total_amount[$key];
            $billItem->save();
        }

        // Save bill discounts
        foreach ($request->discount_type as $key => $value) {
            $billDiscount = new BillDiscount();
            $billDiscount->bill_id = $bill->id;
            $billDiscount->discount_type = $value;
            $billDiscount->discount_amount = $request->discount_amount[$key];
            $billDiscount->save();
        }

        // Save bill vats
        foreach ($request->vat as $key => $value) {
            $billVat = new BillVat();
            $billVat->bill_id = $bill->id;
            $billVat->vat = $value;
            $billVat->save();
        }

        return redirect()->route('admin.bill.index')->with('success', 'Bill saved successfully');
    }


    public function edit($id)
    {
        $bill = Bill::find($id);
        $customers = Customer::all();
        $products = Product::all();
        return view('admin.bill.edit', compact('bill', 'customers', 'products'));
    }

    public function update(Request $request, $id)
    {
        $bill = Bill::find($id);
        $bill->customer_id = $request->customer_id;
        $bill->bill_type = $request->bill_type;
        $bill->bill_date = $request->bill_date;
        $bill->final_amount = $request->final_amount;
        $bill->save();

        // Update bill items
        foreach ($request->product_name as $key => $value) {
            $billItem = BillItem::find($request->bill_item_id[$key]);
            $billItem->product_name = $value;
            $billItem->description = $request->description[$key];
            $billItem->quantity = $request->quantity[$key];
            $billItem->unit_price = $request->unit_price[$key];
            $billItem->discount = $request->discount[$key];
            $billItem->total_amount = $request->total_amount[$key];
            $billItem->save();
        }

        // Update bill discounts
        foreach ($request->discount_type as $key => $value) {
            $billDiscount = BillDiscount::find($request->bill_discount_id[$key]);
            $billDiscount->discount_type = $value;
            $billDiscount->discount_amount = $request->discount_amount[$key];
            $billDiscount->save();
        }

        // Update bill vats
        foreach ($request->vat as $key => $value) {
            $billVat = BillVat::find($request->bill_vat_id[$key]);
            $billVat->vat = $value;
            $billVat->save();
        }

        return redirect()->route('bill.index');
    }

    public function destroy($id)
    {
        $bill = Bill::find($id);
        $bill->delete();
        return redirect()->route('bill.index');
    }
}
