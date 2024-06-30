<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegularCustomer;
use App\Models\Customer;

class RegularCustomerController extends Controller
{
    public function index()
    {
        $regularCustomers = RegularCustomer::all();
        return view('admin.regularCustomer.index', compact('regularCustomers'));
    }

    public function create()
    {
        return view('admin.regularCustomer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'area' => 'required',
            'city' => 'required',
            'initial_bill_amount' => 'required|numeric',
            'start_date' => 'required|date',
            'next_bill_date' => 'required|date',
            'status' => 'required'
        ]);

        $regularCustomer = RegularCustomer::create($request->all());

    Customer::create([
        'customer_id' => $regularCustomer->id,
        'customer_type' => 'regular',
        'customer_name' => $regularCustomer->name,

    ]);

       // RegularCustomer::create($request->all());
        return redirect()->route('admin.regularCustomer.index')
            ->with('success', 'Regular Customer created successfully.');
    }

    public function edit(RegularCustomer $customer)
    {
        return view('admin.regularCustomer.edit', compact('customer'));
    }

    public function update(Request $request, RegularCustomer $customer)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'area' => 'required',
            'city' => 'required',
            'initial_bill_amount' => 'required|numeric',
            'start_date' => 'required|date',
            'next_bill_date' => 'required|date',
            'status' => 'required'
        ]);

        $customer->update($request->all());
        return redirect()->route('admin.regularCustomer.index')
            ->with('success', 'Regular Customer updated successfully.');
    }

    public function destroy(RegularCustomer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.regularCustomer.index')
            ->with('success', 'Regular Customer deleted successfully.');
    }

    public function toggleStatus(RegularCustomer $customer)
    {
        $customer->status = $customer->status == 'Active' ? 'Inactive' : 'Active';
        $customer->save();

        return redirect()->route('admin.regularCustomer.index')
            ->with('success', 'Regular Customer status updated successfully.');
    }
}

