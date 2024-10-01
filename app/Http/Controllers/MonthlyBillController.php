<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PaymentHistory;
use Illuminate\Http\Request;
use App\Models\MonthlyBill;
use App\Models\RegularCustomer;
use App\Models\Payment;
use Carbon\Carbon;

class MonthlyBillController extends Controller
{
    public function create()
    {
        $title = "Monthly Bill";
        $customers = RegularCustomer::all();
        return view('admin.monthlyBill.create', compact('customers', 'title'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:regular_customers,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'service' => 'required|in:lift,generator,lift and generator',
            'bill_month' => 'required|date',
            'start_date' => 'required|date',

        ]);

        MonthlyBill::create([
            'regular_customer_id' => $request->customer_id,
            'customer_address' => $request->customer_address,
            'amount' => $request->amount,
            'description' => $request->description,
            'service' => $request->service,
            'bill_month' => $request->bill_month,
            'type' => 'initial',
            'start_date' => $request->start_date,
            'next_generation_date' => Carbon::parse($request->start_date)->addMonth(),
            // 'status' => $request->status,
        ]);

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Bill created successfully.');
    }
    public function index(Request $request)
    {
        $title = "Monthly Bill";
        $query = MonthlyBill::query();

        if ($request->filled('month')) {
            // Split the 'month' value into year and month
            $yearMonth = explode('-', $request->month);
            $year = $yearMonth[0];
            $month = $yearMonth[1];

            // Filter by both year and month
            $query->whereYear('bill_month', '=', $year)
                ->whereMonth('bill_month', '=', $month);
        }

        if ($request->filled('customer_name')) {
            $query->whereHas('regularCustomer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bills = $query->with('regularCustomer')->get();

        return view('admin.monthlyBill.index', compact('bills', 'title'));
    }

    public function destroy($id)
    {
        $bill = MonthlyBill::findOrFail($id);
        $bill->delete();

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Monthly Bill deleted successfully.');
    }

    public function showInvoice($clientId, $month)
    {
        $customer = RegularCustomer::findOrFail($clientId); // Retrieve the customer
        $bills = MonthlyBill::where('regular_customer_id', $clientId)
            ->where('bill_month', '<', $month) // Exclude the current month
            ->orderBy('bill_month')
            ->get();

        $previousDues = [];

        foreach ($bills as $bill) {
            if ($bill->status == 'due' || $bill->status == 'partial') {
                $previousDues[] = [
                    'sl_no' => $bill->id,
                    'month' => $bill->bill_month,
                    'due_amount' => $bill->due_amount ?? 0,
                ];
            }
        }

        return view('admin.monthlyBill.invoice', compact('bills', 'previousDues', 'customer'));
    }


    public function showInvoicePrint(MonthlyBill $bill)
    {
        $customer = $bill->regularCustomer;

        // Calculate previous months' due
        $previousBills = MonthlyBill::where('regular_customer_id', $bill->regular_customer_id)
            ->where(function ($q) {
                $q->where('status', 'Due')
                    ->orWhere('status', 'partial')
                    ->orWhere('status', 'Mark as Paid');
            })
            ->where('bill_month', '<', $bill->bill_month) // Exclude the current bill's month
            ->orderBy('bill_month', 'asc')
            ->get(['bill_month', 'amount', 'due_amount', 'status']); // Include status in the select

        return view('admin.monthlyBill.invoice_print', compact('bill', 'customer', 'previousBills'));
    }

    public function Paid(Request $request)
    {
        $bill = MonthlyBill::findOrFail($request->bill_id);

        if ($bill->status === 'pending' || $bill->status === 'due') {
            $billAmount = $bill->amount;
        } else {
            $billAmount = $bill->due_amount;
        }

        $receivableAmount = $request->receivable_amount;
        $dueAmount = $billAmount - $receivableAmount;

        // Check if due amount is greater than 0 and status should be paid
        if ($dueAmount > 0) {
            return response()->json(['success' => false, 'error' => 'Due amount must be zero to mark as Paid.'], 400);
        }

        $bill->type = 'Ongoing';
        $bill->due_amount = $dueAmount;
        $bill->status = 'paid';
        $bill->save();

        $payment = Payment::create([
            'bill_id' => $bill->id,
            'description' => $request->description,
            'receive_date' => $request->input('receive_date'),
            'amount' => $billAmount,
            'receivable_amount' => $receivableAmount,
            'due_amount' => $dueAmount,
        ]);

        return response()->json(['success' => true, 'dueAmount' => $payment->due_amount]);
    }



    public function showBill($billId)
    {
        $title = "Payment History";
        $payments = Payment::where('bill_id', $billId)->latest()->get();
        return view('admin.monthlyBill.storePayment', compact('payments', 'title'));
    }

    public function generateMonthlyBills()
    {
        $regularCustomers = RegularCustomer::all();

        foreach ($regularCustomers as $customer) {
            $start_date = now()->startOfMonth();
            $bill_month = $start_date->format('Y-m');

            // Fetch the most recent bill amount or set a default amount
            $lastBill = MonthlyBill::where('regular_customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $amount = $lastBill ? $lastBill->amount : 0; // Replace 100 with your default logic if necessary

            MonthlyBill::create([
                'regular_customer_id' => $customer->id,
                'amount' => $amount,
                'description' => 'Monthly bill for ' . $start_date->format('F Y'),
                'service' => $lastBill ? $lastBill->service : 'default_service', // Use previous service or set a default
                'bill_month' => $bill_month,
                'start_date' => $start_date,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Monthly Bills generated successfully.');
    }
    public function getBillDetails(RegularCustomer $customer)
    {
        // Fetch the most recent bill
        $lastBill = MonthlyBill::where('regular_customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Safely handle defaults
        $amount = $lastBill ? $lastBill->amount : 0; // Default to 0 if no bill
        $description = $lastBill->description ?? 'None'; // Default description
        $next_generation_date = $lastBill ? $lastBill->next_generation_date : Carbon::now()->addMonth(); // Default date

        // Define max length for service
        $maxServiceLength = 255; // Adjust based on your schema
        $service = $lastBill ? substr($lastBill->service, 0, $maxServiceLength) : 'default_service'; // Truncate if necessary

        return [
            'amount' => $amount,
            'description' => $description,
            'service' => $service,
            'next_generation_date' => $next_generation_date
        ];
    }

    public function edit($id)
    {
        $bill = MonthlyBill::findOrFail($id);
        $customers = RegularCustomer::all(); // Assuming RegularCustomer is the model for customers
        return view('admin.monthlyBill.edit', compact('bill', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'service' => 'required|string',

        ]);

        $bill = MonthlyBill::findOrFail($id);
        $bill->amount = $request->amount;
        $bill->description = $request->description;
        $bill->service = $request->service;
        $bill->save();

        return redirect()->route('admin.monthlyBill.index')->with('success', 'Bill updated successfully');
    }
}
