<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\StockTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $units = Unit::all();

        $query = Item::query()->with(['category', 'supplier', 'unit']);

        // Filter by category if selected
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->get();

        // Coffee shop statistics with alert counts
        $totalItems = Item::count();
        $lowStockItemsCount = Item::where('quantity', '>', 0)
                            ->where('quantity', '<=', DB::raw('reorder_point'))
                            ->count();
        $outOfStockItemsCount = Item::where('quantity', '<=', 0)->count();
        $totalInventoryValue = Item::sum(DB::raw('quantity * cost_per_unit'));

        return view('items.index', compact(
            'items', 
            'categories', 
            'suppliers',
            'units',
            'totalItems', 
            'lowStockItemsCount',
            'outOfStockItemsCount',
            'totalInventoryValue'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $units = Unit::all();
        return view('items.create', compact('categories', 'suppliers', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'reorder_point' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_id' => 'required|exists:units,id',
            'cost_per_unit' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'is_perishable' => 'sometimes|boolean', // FIXED: changed to 'sometimes'
            'expiry_date' => 'nullable|date|after_or_equal:today', // FIXED: changed to after_or_equal
            'shelf_life_days' => 'nullable|integer|min:1',
            'manufacture_date' => 'nullable|date|before_or_equal:today', // FIXED: changed to before_or_equal
            'batch_number' => 'nullable|string|max:100',
        ]);

        // Set last_restock_date if quantity is being added
        $data = $request->all();
        
        // Handle boolean field properly
        $data['is_perishable'] = $request->has('is_perishable') ? 1 : 0;
        
        if ($request->quantity > 0) {
            $data['last_restock_date'] = now();
        }

        Item::create($data);
        return redirect()->route('items.index')->with('success', 'Item added successfully!');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        $units = Unit::all();
        return view('items.edit', compact('item', 'categories', 'suppliers', 'units'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'reorder_point' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_id' => 'required|exists:units,id',
            'cost_per_unit' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100',
            'is_perishable' => 'sometimes|boolean', // ADDED: expiry fields validation
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'shelf_life_days' => 'nullable|integer|min:1',
            'manufacture_date' => 'nullable|date|before_or_equal:today',
            'batch_number' => 'nullable|string|max:100',
        ]);

        // Handle boolean field properly
        $data = $request->all();
        $data['is_perishable'] = $request->has('is_perishable') ? 1 : 0;

        $item->update($data);
        return redirect()->route('items.index')->with('success', 'Item updated successfully!');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully!');
    }

    public function restock(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.01',
            'cost_per_unit' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = Item::findOrFail($request->item_id);

        try {
            // Get user ID
            $userId = null;
            if (auth()->check()) {
                $userId = auth()->id();
            } else {
                $firstUser = User::first();
                if ($firstUser) {
                    $userId = $firstUser->id;
                }
            }

            // Create stock transaction
            StockTransaction::create([
                'item_id' => $item->id,
                'type' => 'purchase',
                'quantity' => $request->quantity,
                'cost_per_unit' => $request->cost_per_unit,
                'notes' => $request->notes,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            // If transaction fails, continue with restock but log the error
            \Log::error('Stock transaction failed: ' . $e->getMessage());
        }

        // Update item stock and cost
        $item->quantity += $request->quantity;
        $item->cost_per_unit = $request->cost_per_unit;
        $item->last_restock_date = now();
        if ($request->supplier_id) {
            $item->supplier_id = $request->supplier_id;
        }
        $item->save();

        return redirect()->route('items.index')->with('success', 'Stock restocked successfully!');
    }

    public function recordUsage(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:usage,waste,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = Item::findOrFail($request->item_id);

        // Check if we have enough stock for usage/waste
        if (in_array($request->type, ['usage', 'waste']) && $item->quantity < $request->quantity) {
            return redirect()->back()->with('error', "Not enough stock! Only {$item->quantity} {$item->unit->abbreviation} available.");
        }

        try {
            // Get user ID
            $userId = null;
            if (auth()->check()) {
                $userId = auth()->id();
            } else {
                $firstUser = User::first();
                if ($firstUser) {
                    $userId = $firstUser->id;
                }
            }

            // Create stock transaction
            StockTransaction::create([
                'item_id' => $item->id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'cost_per_unit' => $item->cost_per_unit,
                'notes' => $request->notes,
                'user_id' => $userId,
            ]);

            // Update item stock based on type
            if ($request->type === 'usage' || $request->type === 'waste') {
                $item->quantity -= $request->quantity;
            } else { // adjustment (positive addition)
                $item->quantity += $request->quantity;
            }
            
            $item->save();

            $typeLabels = [
                'usage' => 'Usage',
                'waste' => 'Waste', 
                'adjustment' => 'Adjustment'
            ];

            return redirect()->route('items.index')->with('success', "{$typeLabels[$request->type]} recorded successfully!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error recording transaction: ' . $e->getMessage());
        }
    }
}