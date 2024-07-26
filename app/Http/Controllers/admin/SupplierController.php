<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('admin.supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'status' => 'required'
        ]);

        Supplier::create($request->all());
        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'status' => 'required'
        ]);

        $supplier->update($request->all());
        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->status = $supplier->status == 'active' ? 'inactive' : 'active';
        $supplier->save();

        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier status updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
