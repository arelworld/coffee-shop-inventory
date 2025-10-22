<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header .subtitle { color: #7f8c8d; margin-top: 5px; }
        .alert-section { margin-bottom: 30px; }
        .alert-title { background: #e74c3c; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .alert-title.warning { background: #f39c12; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background: #34495e; color: white; padding: 10px; text-align: left; }
        .table td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .table tr:nth-child(even) { background: #f8f9fa; }
        .urgent { color: #e74c3c; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 12px; }
        .summary { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Coffee Shop - Stock Alerts Report</h1>
        <div class="subtitle">{{ $title }}</div>
        <div class="subtitle">Generated: {{ $generated_at }}</div>
    </div>

    <div class="summary">
        <strong>Alert Summary:</strong> 
        {{ $total_low_stock }} Low Stock Items | 
        {{ $total_out_of_stock }} Out of Stock Items
    </div>

    @if($out_of_stock_items->count() > 0)
    <div class="alert-section">
        <div class="alert-title">
            🚨 URGENT - OUT OF STOCK ITEMS ({{ $out_of_stock_items->count() }})
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Last Restock</th>
                    <th>Action Needed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($out_of_stock_items as $item)
                <tr class="urgent">
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? 'N/A' }}</td>
                    <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $item->last_restock_date ? $item->last_restock_date->format('M j, Y') : 'Never' }}</td>
                    <td>IMMEDIATE RESTOCK REQUIRED</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($low_stock_items->count() > 0)
    <div class="alert-section">
        <div class="alert-title warning">
            ⚠️ LOW STOCK ITEMS ({{ $low_stock_items->count() }})
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Reorder Point</th>
                    <th>Unit</th>
                    <th>Supplier</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($low_stock_items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->reorder_point }}</td>
                    <td>{{ $item->unit->abbreviation ?? 'N/A' }}</td>
                    <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                    <td>
                        @if($item->quantity <= $item->reorder_point * 0.3)
                        HIGH
                        @elseif($item->quantity <= $item->reorder_point * 0.6)
                        MEDIUM
                        @else
                        LOW
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Coffee Shop Inventory System | Generated on {{ $generated_at }}
    </div>
</body>
</html>