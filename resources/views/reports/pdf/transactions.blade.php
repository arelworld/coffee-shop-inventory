<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; }
        .header .subtitle { color: #7f8c8d; margin-top: 5px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { background: #34495e; color: white; padding: 10px; text-align: left; }
        .table td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .table tr:nth-child(even) { background: #f8f9fa; }
        .type-purchase { color: #27ae60; }
        .type-usage { color: #3498db; }
        .type-waste { color: #e74c3c; }
        .type-adjustment { color: #f39c12; }
        .footer { margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 12px; }
        .summary { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Coffee Shop - Stock Transactions Report</h1>
        <div class="subtitle">{{ $title }}</div>
        <div class="subtitle">Generated: {{ $generated_at }}</div>
    </div>

    <div class="summary">
        <strong>Report Period:</strong> Last {{ $period }} | 
        <strong>Total Transactions:</strong> {{ $total_transactions }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Item</th>
                <th>Transaction Type</th>
                <th>Quantity</th>
                <th>Cost/Unit</th>
                <th>Total Value</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->created_at->format('M j, Y H:i') }}</td>
                <td>{{ $transaction->item->name }}</td>
                <td class="type-{{ $transaction->type }}">
                    @if($transaction->type === 'purchase') 📥 Purchase
                    @elseif($transaction->type === 'usage') 🍽️ Usage
                    @elseif($transaction->type === 'waste') 🗑️ Waste
                    @elseif($transaction->type === 'adjustment') 📊 Adjustment
                    @endif
                </td>
                <td>
                    @if(in_array($transaction->type, ['usage', 'waste']))
                    -{{ $transaction->quantity }}
                    @else
                    +{{ $transaction->quantity }}
                    @endif
                    {{ $transaction->item->unit->abbreviation ?? '' }}
                </td>
                <td>₱{{ number_format($transaction->cost_per_unit, 2) }}</td>
                <td>₱{{ number_format($transaction->quantity * $transaction->cost_per_unit, 2) }}</td>
                <td>{{ $transaction->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Coffee Shop Inventory System | Transaction History Report
    </div>
</body>
</html>