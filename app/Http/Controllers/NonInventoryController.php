<?php

namespace App\Http\Controllers;

use App\Models\NonInventory;
use Illuminate\Http\Request;

class NonInventoryController extends Controller
{
    public function index()
    {
        $nonInventories = NonInventory::all();
        return view('admin.nonInventory.index', compact('nonInventories'));
    }

    public function create()
    {
        return view('admin.nonInventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'purchase_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        $nonInventory = new NonInventory($request->all());
        $nonInventory->total_amount = $nonInventory->purchase_price * $nonInventory->quantity;
        $nonInventory->save();

        return redirect()->route('admin.nonInventory.index')
            ->with('success', 'Non-Inventory Item created successfully.');
    }

    public function edit($id)
    {
        $nonInventory = NonInventory::findOrFail($id);
        return view('admin.nonInventory.edit', compact('nonInventory'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'purchase_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        $nonInventory = NonInventory::findOrFail($id);
        $nonInventory->fill($request->all());
        $nonInventory->total_amount = $nonInventory->purchase_price * $nonInventory->quantity;
        $nonInventory->save();

        return redirect()->route('admin.nonInventory.index')
            ->with('success', 'Non-Inventory Item updated successfully.');
    }

    public function destroy($id)
    {
        $nonInventory = NonInventory::findOrFail($id);
        $nonInventory->delete();

        return redirect()->route('admin.nonInventory.index')
            ->with('success', 'Non-Inventory Item deleted successfully.');
    }
    public function toggleStatus($id)
    {
        $nonInventory = NonInventory::findOrFail($id);
        $nonInventory->status = $nonInventory->status === 'active' ? 'inactive' : 'active';
        $nonInventory->save();

        return redirect()->route('admin.nonInventory.index')
            ->with('success', 'Non-Inventory Item status updated successfully.');
    }
}
