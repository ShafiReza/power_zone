<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonthlyBill;
use App\Models\RegularCustomer;
use App\Models\Payment;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyBillController extends Controller
{
    public function create()
    {
        $customers = RegularCustomer::all();
        return view('admin.monthlyBill.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:regular_customers,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'service' => 'required|in:lift,generator,lift and generator',
            'bill_month' => 'required|date_format:Y-m',
            'start_date' => 'required|date',

        ]);

        MonthlyBill::create([
            'regular_customer_id' => $request->customer_id,
            'customer_address' => $request->customer_address,
            'amount' => $request->amount,
            'description' => $request->description,
            'service' => $request->service,
            'bill_month' => $request->bill_month,
            'start_date' => $request->start_date,
            'next_generation_date' => Carbon::parse($request->start_date)->addMonth(),
            // 'status' => $request->status,
        ]);

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Bill created successfully.');
    }

    public function index(Request $request)
    {


        $query = MonthlyBill::with('regularCustomer');

        if ($request->has('month')) {
            $query->where('bill_month', 'LIKE', $request->month . '%');
        }

        if ($request->has('customer_name')) {
            $query->whereHas('regularCustomer', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->customer_name . '%');
            });
        }

        $bills = $query->get();

        $bill = $bills->first();

        return view('admin.monthlyBill.index', compact('bills','bill'));
    }

    public function destroy($id)
    {
        MonthlyBill::find($id)->delete();
        return redirect()->route('admin.monthlyBill.index')->with('success', 'Bill deleted successfully.');
    }
    public function toggleStatus($id)
    {
        $bill = MonthlyBill::findOrFail($id);

        $startDate = Carbon::parse($bill->start_date);
        $currentDate = Carbon::now();

        if ($bill->status == 'pending') {
            if ($startDate->diffInMonths($currentDate) < 1) {
                $bill->status = 'Paid';
            } elseif ($startDate->diffInMonths($currentDate) >= 1) {
                $bill->status = 'Due';
            }
        } else {
            $bill->status = 'pending'; // Reset status to pending if it's currently paid or due
        }

        $bill->save();

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Bill status toggled successfully.');
    }

    public function showInvoice($id)
    {
        $bill = MonthlyBill::with('regularCustomer')->findOrFail($id);
        $customer = $bill->regularCustomer;

        // Calculate previous months' due
        $previousBills = MonthlyBill::where('regular_customer_id', $bill->regular_customer_id)
            ->where('bill_month', '<', $bill->bill_month)
            ->where('status', 'due')
            ->orderBy('bill_month', 'asc')
            ->get(['bill_month', 'amount']);

        return view('admin.monthlyBill.invoice', compact('bill', 'customer', 'previousBills'));
    }

    public function showInvoicePrint(MonthlyBill $bill)
    {
        $customer = $bill->regularCustomer;

        // Calculate previous months' due
        $previousBills = MonthlyBill::where('regular_customer_id', $bill->regular_customer_id)
            ->where('bill_month', '<', $bill->bill_month)
            ->where('status', 'due')
            ->orderBy('bill_month', 'asc')
            ->get(['bill_month', 'amount']);

        return view('admin.monthlyBill.invoice_print', compact('bill', 'customer', 'previousBills'));
    }
    // public function storePayment(Request $request, $id)
    // {
    //     // Validate the request data
    //     $validatedData = $request->validate([
    //         'description' => 'required',
    //         'receiveable_amount' => 'required|numeric',
    //     ]);

    //     // Find the bill and update its status
    //     $bill = MonthlyBill::findOrFail($id);
    //     $bill->status = 'paid';
    //     $bill->save();

    //     // Store the payment details
    //     $payment = new Payment();
    //     $payment->bill_id = $id;
    //     $payment->description = $validatedData['description'];
    //     $payment->receiveable_amount = $validatedData['receiveable_amount'];
    //     $payment->due_amount = $bill->amount - $validatedData['receiveable_amount'];
    //     $payment->save();

    //     // Return the updated bill status and the list of bills
    //     //$bills = MonthlyBill::all(); // or however you fetch the bills
    //     return view('admin.monthlyBill.index', compact('bill'))->with('status', 'Paid');
    // }

    public function storePayment(Request $request)
    {
        // Debugging: Check all request data
        // dd($request->all());

        // Validate the request data
        $validatedData = $request->validate([
            'description' => 'required|string',
            'receiveable_amount' => 'required|numeric',
            'bill_id' => 'required|exists:monthly_bills,id', // Ensure bill_id is present and valid
        ]);

        // Find the bill and update its status
        $bill = MonthlyBill::findOrFail($request->input('bill_id'));

        // if ($bill->bill_type !== 'monthly') {
        //     return response()->json(['error' => 'Cannot insert payment for non-monthly bill.'], 400);
        // }

        // Calculate the due amount
        $dueAmount = $bill->amount - $request->input('receiveable_amount');

        // Create a new payment
        $payment = new Payment();
        $payment->bill_id = $request->input('bill_id');
        $payment->description = $request->input('description');
        $payment->receiveable_amount = $request->input('receiveable_amount');
        $payment->due_amount = $dueAmount;
        $payment->save();

        // Update the bill status to 'paid' if fully paid, otherwise 'pending'
        $bill->status = 'paid';
        $bill->save();

        // Return a JSON response
        return response()->json([
            'status' => 'Payment added successfully.',
            'bill_status' => $bill->status,
        ]);
    }

    public function showBill($id)
    {
        $bill = MonthlyBill::find($id);
        if (!$bill) {
            abort(404);
        }
        $payments = $bill->payments;
        return view('admin.monthlyBill.storePayment', compact('bill', 'payments'));
    }
}
