<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header .subtitle { color: #7f8c8d; margin-top: 5px; }
        .summary { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { background: #34495e; color: white; padding: 10px; text-align: left; }
        .table td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .table tr:nth-child(even) { background: #f8f9fa; }
        .status-safe { color: #27ae60; }
        .status-low { color: #f39c12; }
        .status-out { color: #e74c3c; }
        .footer { margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Coffee Shop Inventory Report</h1>
        <div class="subtitle">{{ $title }}</div>
        <div class="subtitle">Generated: {{ $generated_at }}</div>
    </div>

    <div class="summary">
        <strong>Summary:</strong> {{ $total_items }} items | Total Value: ₱{{ number_format($total_value, 2) }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Unit</th>
                <th>Cost/Unit</th>
                <th>Total Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name ?? 'N/A' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit->abbreviation ?? 'N/A' }}</td>
                <td>₱{{ number_format($item->cost_per_unit, 2) }}</td>
                <td>₱{{ number_format($item->quantity * $item->cost_per_unit, 2) }}</td>
                <td class="status-{{ $item->stock_status }}">
                    @if($item->stock_status == 'safe') Safe
                    @elseif($item->stock_status == 'low') Low Stock
                    @else Out of Stock
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Coffee Shop Inventory System | Page 1 of 1
    </div>
</body>
</html>