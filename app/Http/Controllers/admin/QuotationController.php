<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\IrregularCustomer;
use App\Models\Product;
use App\Models\QuotationItem;
use App\Models\QuotationItem2;
use Illuminate\Support\Facades\Log;
class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with(['irregularCustomer'])->get();

        return view('admin.quotation.index', compact('quotations'));
    }

    public function create()
    {
        $irregularCustomers = IrregularCustomer::all();
        $products = Product::all();
        return view('admin.quotation.create', ['irregularCustomers' => $irregularCustomers, 'products' => $products]);
    }

    public function getProduct(Request $request)
    {
        $productId = $request->input('productId');
        $product = Product::find($productId);
        return response()->json($product);
    }
    public function quotation($id)
    {
        // Fetch bill items related to the bill
        $products = QuotationItem::where('quotation_id', $id)->get();
        $quotationItems2 = QuotationItem2::where('quotation_id', $id)->get();
        // Fetch the bill details
        $quotation = Quotation::find($id);

        // Determine whether the customer is regular or irregular and fetch customer details accordingly
        $customer =  IrregularCustomer::find($quotation->irregular_customer_id);

        return view('admin.quotation.quotation', compact('customer', 'products', 'quotation', 'quotationItems2'));
    }
    public function store(Request $request)
{
    // Create a new quotation instance
    $customerId = $request->input('customerName');

    $quotation = new Quotation();
    $quotation->irregular_customer_id = $customerId;
    $quotation->customer_name = IrregularCustomer::find($customerId)->name;
    $quotation->quotation_date = $request->input('quotationDate');
    $quotation->final_amount = 0;

    // Save the quotation to generate an ID
    $quotation->save();

    // Create quotation items
    $productNames = $request->input('product_name', []);
    $descriptions = $request->input('description', []);
    $quantities = $request->input('quantity', []);
    $unitPrices = $request->input('unitPrice', []);
    $discounts = $request->input('discount', []);
    $discountTypes = $request->input('discountType', []);

    //$totalFinalAmount = 0;

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

        $quotationItem = new QuotationItem();
        $quotationItem->quotation_id = $quotation->id;
        $quotationItem->product_name = $productName;
        $quotationItem->description = $descriptions[$index];
        $quotationItem->quantity = $quantity;
        $quotationItem->unit_price = $unitPrice;
        $quotationItem->discount = $discount;
        $quotationItem->discount_type = $discountType;
        $quotationItem->total_amount = $totalAmount;
        $quotationItem->save();

        // Update final amount
        //$totalFinalAmount += $totalAmount;
        $quotation->final_amount += $totalAmount;

    }

    // Process QuotationItem2 items
    $quotationItems2 = $request->input('bill_items2', []);
    foreach ($quotationItems2 as $quotationItem2Data) {
        $quotationItem2 = new QuotationItem2();
        $quotationItem2->quotation_id = $quotation->id;
        $quotationItem2->discount_type = $quotationItem2Data['discount_type'];
        $quotationItem2->discount = $quotationItem2Data['discount'];
        $quotationItem2->vat = $quotationItem2Data['vat'];
        $quotationItem2->final_amount = $quotationItem2Data['final_amount'];
        $quotationItem2->save();

        // Calculate final amount considering QuotationItem2 discounts and VAT
        $itemFinalAmount = $quotationItem2Data['final_amount'];
        if ($quotationItem2Data['discount_type'] === 'Percentage') {
            $quotation->final_amount -= $quotation->final_amount * ($quotationItem2Data['discount'] / 100);
        } else {
            $quotation->final_amount -= $quotationItem2Data['discount'];
        }
        $quotation->final_amount += $quotationItem2Data['vat'];
        //$totalFinalAmount = $itemFinalAmount;
    }

    // Update final amount considering both QuotationItem and QuotationItem2
    //$quotation->final_amount = $totalFinalAmount;
    $quotation->save();

    // Return a success response
    return redirect()->route('admin.quotation.index')->with('success', 'Quotation created successfully!');
}



    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('admin.quotation.index')
            ->with('success', 'Quotation deleted successfully.');
    }
}
