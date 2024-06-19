<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;

class BillController extends Controller
{
    public function save(Request $request)
    {
        $bill = new Bill();
        $bill->customer_name = $request->customerName;
        $bill->bill_type = $request->billType;
        $bill->bill_date = $request->billDate;
        $bill->amount = array_sum($request->totalAmount);
        $bill->status = 'active';
        $bill->save();

        // Save bill items
        foreach ($request->productName as $index => $productName) {
            $bill->items()->create([
                'product_name' => $productName,
                'description' => $request->description[$index],
                'quantity' => $request->quantity[$index],
                'unit_price' => $request->unitPrice[$index],
                'discount' => $request->discount[$index],
                'total_amount' => $request->totalAmount[$index],
            ]);
        }

        return response()->json(['message' => 'Bill saved successfully']);
    }
}
