<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Item;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('items')
            ->withSum('items', 'quantity')
            ->get()
            ->map(function($supplier) {
                $supplier->inventory_value = $supplier->items->sum(function($item) {
                    return $item->quantity * $item->cost_per_unit;
                });
                return $supplier;
            });

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully!');
    }

    public function show(Supplier $supplier)
    {
        $items = $supplier->items()->with(['category', 'unit'])->get();
        $lowStockItems = $items->where('stock_status', 'low');
        $outOfStockItems = $items->where('stock_status', 'out');
        
        $totalInventoryValue = $items->sum(function($item) {
            return $item->quantity * $item->cost_per_unit;
        });

        return view('suppliers.show', compact('supplier', 'items', 'lowStockItems', 'outOfStockItems', 'totalInventoryValue'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $supplier->update($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        // Check if supplier has items
        if ($supplier->items()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete supplier with associated items. Please reassign items first.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully!');
    }
}