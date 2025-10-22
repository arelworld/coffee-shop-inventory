<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total inventory value and counts
        $totalItems = Item::count();
        $totalInventoryValue = Item::sum(DB::raw('quantity * cost_per_unit'));
        
        // Stock status counts
        $lowStockItemsCount = Item::where('quantity', '>', 0)
                                ->where('quantity', '<=', DB::raw('reorder_point'))
                                ->count();
        $outOfStockItemsCount = Item::where('quantity', '<=', 0)->count();
        $safeStockItemsCount = $totalItems - $lowStockItemsCount - $outOfStockItemsCount;

                // Stock status counts
        $lowStockItemsCount = Item::where('quantity', '>', 0)
                                ->where('quantity', '<=', DB::raw('reorder_point'))
                                ->count();
        
        // ADD THIS LINE - Low stock items collection
        $lowStockItems = Item::where('quantity', '>', 0)
                            ->where('quantity', '<=', DB::raw('reorder_point'))
                            ->with(['category', 'unit', 'supplier'])
                            ->orderBy('quantity', 'asc')
                            ->get();
        
        $outOfStockItemsCount = Item::where('quantity', '<=', 0)->count();

        // Category-wise inventory value
        $categoryValues = Category::with('items')
            ->get()
            ->map(function($category) {
                $value = $category->items->sum(function($item) {
                    return $item->quantity * $item->cost_per_unit;
                });
                return [
                    'name' => $category->name,
                    'value' => $value,
                    'item_count' => $category->items->count()
                ];
            })
            ->sortByDesc('value');

        // Recent stock transactions
        $recentTransactions = StockTransaction::with(['item', 'item.unit'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Monthly usage value (last 3 months)
        $monthlyUsage = StockTransaction::whereIn('type', ['usage', 'waste'])
            ->where('created_at', '>=', now()->subMonths(3))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(quantity * cost_per_unit) as total_value'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Most used items (last 30 days)
        $mostUsedItems = StockTransaction::where('type', 'usage')
            ->where('created_at', '>=', now()->subDays(30))
            ->with('item')
            ->select('item_id', DB::raw('SUM(quantity) as total_used'))
            ->groupBy('item_id')
            ->orderBy('total_used', 'desc')
            ->take(5)
            ->get();

        // Expiry alerts - FIXED: Added eager loading for relationships
        $expiringSoonItems = Item::where('is_perishable', true)
            ->where('expiry_date', '<=', now()->addDays(3))
            ->where('expiry_date', '>=', now())
            ->with(['unit', 'category', 'supplier']) // ADDED category and supplier
            ->get();

        $expiredItems = Item::where('is_perishable', true)
            ->where('expiry_date', '<', now())
            ->with(['unit', 'category', 'supplier']) // ADDED category and supplier
            ->get();

                return view('dashboard.index', compact(
            'totalItems',
            'totalInventoryValue',
            'lowStockItemsCount',
            'lowStockItems', // ADD THIS LINE
            'outOfStockItemsCount',
            'safeStockItemsCount',
            'categoryValues',
            'recentTransactions',
            'monthlyUsage',
            'mostUsedItems',
            'expiringSoonItems',
            'expiredItems'
        ));
    }




    /**
     * Download Full Inventory PDF Report
     */
    public function downloadInventoryPDF()
    {
        $items = Item::with(['category', 'unit', 'supplier'])
                    ->orderBy('name')
                    ->get();

        $data = [
            'title' => 'Full Inventory Report - ' . now()->format('M j, Y'),
            'items' => $items,
            'total_value' => $items->sum(function($item) {
                return $item->quantity * $item->cost_per_unit;
            }),
            'generated_at' => now()->format('F j, Y g:i A'),
            'total_items' => $items->count()
        ];

        // Use the full namespace
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.inventory', $data);
        return $pdf->download('inventory-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Download Low Stock PDF Report
     */
    public function downloadLowStockPDF()
    {
        $lowStockItems = Item::where('quantity', '>', 0)
                            ->whereRaw('quantity <= reorder_point')
                            ->with(['category', 'unit', 'supplier'])
                            ->orderBy('quantity', 'asc')
                            ->get();

        $outOfStockItems = Item::where('quantity', '<=', 0)
                              ->with(['category', 'unit', 'supplier'])
                              ->get();

        $data = [
            'title' => 'Low Stock Alert Report - ' . now()->format('M j, Y'),
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
            'generated_at' => now()->format('F j, Y g:i A'),
            'total_low_stock' => $lowStockItems->count(),
            'total_out_of_stock' => $outOfStockItems->count()
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.low-stock', $data);
        return $pdf->download('low-stock-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Download Expiry Alerts PDF Report
     */
    public function downloadExpiryPDF()
    {
        $expiringSoon = Item::where('is_perishable', true)
                          ->where('expiry_date', '<=', now()->addDays(7))
                          ->where('expiry_date', '>=', now())
                          ->with(['category', 'unit', 'supplier'])
                          ->orderBy('expiry_date')
                          ->get();

        $expiredItems = Item::where('is_perishable', true)
                          ->where('expiry_date', '<', now())
                          ->with(['category', 'unit', 'supplier'])
                          ->get();

        $data = [
            'title' => 'Expiry Alerts Report - ' . now()->format('M j, Y'),
            'expiring_soon' => $expiringSoon,
            'expired_items' => $expiredItems,
            'generated_at' => now()->format('F j, Y g:i A'),
            'total_expiring_soon' => $expiringSoon->count(),
            'total_expired' => $expiredItems->count()
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.expiry-alerts', $data);
        return $pdf->download('expiry-alerts-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Download Stock Transactions PDF Report
     */
    public function downloadTransactionsPDF(Request $request)
    {
        $days = $request->get('days', 30); // Default to last 30 days
        
        $transactions = StockTransaction::with(['item', 'item.unit', 'user'])
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'title' => 'Stock Transactions Report - Last ' . $days . ' Days',
            'transactions' => $transactions,
            'generated_at' => now()->format('F j, Y g:i A'),
            'period' => $days . ' days',
            'total_transactions' => $transactions->count()
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.transactions', $data);
        return $pdf->download('transactions-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // Add these methods to your DashboardController.php

/**
 * Export Full Inventory to CSV
 */
public function exportInventoryCSV()
{
    $items = Item::with(['category', 'unit', 'supplier'])->get();
    
    $fileName = 'inventory-export-' . now()->format('Y-m-d') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ];

    $callback = function() use ($items) {
        $file = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fwrite($file, "\xEF\xBB\xBF");
        
        // Headers
        fputcsv($file, [
            'Item Name',
            'SKU',
            'Category',
            'Current Stock',
            'Unit',
            'Reorder Point',
            'Cost per Unit',
            'Total Value',
            'Supplier',
            'Stock Status',
            'Last Restock Date',
            'Is Perishable',
            'Expiry Date',
            'Batch Number'
        ]);

        // Data
        foreach ($items as $item) {
            fputcsv($file, [
                $item->name,
                $item->sku ?? 'N/A',
                $item->category->name ?? 'Uncategorized',
                $item->quantity,
                $item->unit->abbreviation ?? 'N/A',
                $item->reorder_point,
                '₱' . number_format($item->cost_per_unit, 2),
                '₱' . number_format($item->quantity * $item->cost_per_unit, 2),
                $item->supplier->name ?? 'No Supplier',
                $this->getStockStatusText($item->stock_status),
                $item->last_restock_date ? $item->last_restock_date->format('M j, Y') : 'Never',
                $item->is_perishable ? 'Yes' : 'No',
                $item->expiry_date ? $item->expiry_date->format('M j, Y') : 'N/A',
                $item->batch_number ?? 'N/A'
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

/**
 * Export Stock Transactions to CSV
 */
public function exportTransactionsCSV(Request $request)
{
    $days = $request->get('days', 30);
    
    $transactions = StockTransaction::with(['item', 'item.unit', 'user'])
        ->where('created_at', '>=', now()->subDays($days))
        ->orderBy('created_at', 'desc')
        ->get();

    $fileName = 'transactions-export-' . now()->format('Y-m-d') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ];

    $callback = function() use ($transactions) {
        $file = fopen('php://output', 'w');
        fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM
        
        fputcsv($file, [
            'Date & Time',
            'Item Name',
            'Transaction Type',
            'Quantity',
            'Unit',
            'Cost per Unit',
            'Total Value',
            'User',
            'Notes'
        ]);

        foreach ($transactions as $transaction) {
            $quantityDisplay = in_array($transaction->type, ['usage', 'waste']) 
                ? '-' . $transaction->quantity 
                : '+' . $transaction->quantity;

            fputcsv($file, [
                $transaction->created_at->format('M j, Y H:i'),
                $transaction->item->name,
                $this->getTransactionTypeText($transaction->type),
                $quantityDisplay,
                $transaction->item->unit->abbreviation ?? 'N/A',
                '₱' . number_format($transaction->cost_per_unit, 2),
                '₱' . number_format($transaction->quantity * $transaction->cost_per_unit, 2),
                $transaction->user->name ?? 'System',
                $transaction->notes ?? '-'
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

/**
 * Export Low Stock Items to CSV
 */
public function exportLowStockCSV()
{
    $lowStockItems = Item::where('quantity', '>', 0)
                        ->whereRaw('quantity <= reorder_point')
                        ->with(['category', 'unit', 'supplier'])
                        ->get();

    $outOfStockItems = Item::where('quantity', '<=', 0)
                          ->with(['category', 'unit', 'supplier'])
                          ->get();

    $allItems = $lowStockItems->merge($outOfStockItems);

    $fileName = 'low-stock-alerts-' . now()->format('Y-m-d') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ];

    $callback = function() use ($allItems) {
        $file = fopen('php://output', 'w');
        fwrite($file, "\xEF\xBB\xBF");
        
        fputcsv($file, [
            'Item Name',
            'Category',
            'Current Stock',
            'Reorder Point',
            'Unit',
            'Supplier',
            'Status',
            'Urgency Level',
            'Cost per Unit',
            'Restock Value',
            'Last Restock Date'
        ]);

        foreach ($allItems as $item) {
            $urgency = $this->getUrgencyLevel($item);
            $restockQuantity = max($item->reorder_point * 2 - $item->quantity, $item->reorder_point);

            fputcsv($file, [
                $item->name,
                $item->category->name ?? 'Uncategorized',
                $item->quantity,
                $item->reorder_point,
                $item->unit->abbreviation ?? 'N/A',
                $item->supplier->name ?? 'No Supplier',
                $this->getStockStatusText($item->stock_status),
                $urgency,
                '₱' . number_format($item->cost_per_unit, 2),
                '₱' . number_format($restockQuantity * $item->cost_per_unit, 2),
                $item->last_restock_date ? $item->last_restock_date->format('M j, Y') : 'Never'
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

// Helper methods
private function getStockStatusText($status)
{
    return match($status) {
        'safe' => 'Safe Stock',
        'low' => 'Low Stock',
        'out' => 'Out of Stock',
        default => 'Unknown'
    };
}

private function getTransactionTypeText($type)
{
    return match($type) {
        'purchase' => 'Purchase',
        'usage' => 'Usage',
        'waste' => 'Waste',
        'adjustment' => 'Adjustment',
        default => 'Unknown'
    };
}

private function getUrgencyLevel($item)
{
    if ($item->quantity <= 0) {
        return 'CRITICAL';
    }

    $percentage = ($item->quantity / $item->reorder_point) * 100;

    if ($percentage <= 30) {
        return 'HIGH';
    } elseif ($percentage <= 60) {
        return 'MEDIUM';
    } else {
        return 'LOW';
    }
}
}

