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
        .alert-title.expired { background: #c0392b; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background: #34495e; color: white; padding: 10px; text-align: left; }
        .table td { padding: 8px 10px; border-bottom: 1px solid #ddd; }
        .table tr:nth-child(even) { background: #f8f9fa; }
        .expired-row { background: #ffebee !important; color: #c62828; }
        .expiring-row { background: #fff3e0 !important; }
        .footer { margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 12px; }
        .summary { background: #f8f9fa; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Coffee Shop - Expiry Alerts Report</h1>
        <div class="subtitle">{{ $title }}</div>
        <div class="subtitle">Generated: {{ $generated_at }}</div>
    </div>

    <div class="summary">
        <strong>Expiry Summary:</strong> 
        {{ $total_expired }} Expired Items | 
        {{ $total_expiring_soon }} Expiring Soon (Next 7 Days)
    </div>

    @if($expired_items->count() > 0)
    <div class="alert-section">
        <div class="alert-title expired">
            🚫 EXPIRED ITEMS - DISCARD IMMEDIATELY ({{ $expired_items->count() }})
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Batch Number</th>
                    <th>Expiry Date</th>
                    <th>Days Expired</th>
                    <th>Quantity</th>
                    <th>Supplier</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expired_items as $item)
                <tr class="expired-row">
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->batch_number ?? 'N/A' }}</td>
                    <td>{{ $item->expiry_date->format('M j, Y') }}</td>
                    <td>{{ abs($item->expiry_date->diffInDays(now())) }} days</td>
                    <td>{{ $item->quantity }} {{ $item->unit->abbreviation ?? '' }}</td>
                    <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                    <td><strong>DISCARD</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($expiring_soon->count() > 0)
    <div class="alert-section">
        <div class="alert-title warning">
            ⏰ ITEMS EXPIRING SOON (Next 7 Days) - {{ $expiring_soon->count() }}
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Batch Number</th>
                    <th>Expiry Date</th>
                    <th>Days Left</th>
                    <th>Quantity</th>
                    <th>Supplier</th>
                    <th>Priority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expiring_soon as $item)
                <tr class="expiring-row">
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->batch_number ?? 'N/A' }}</td>
                    <td>{{ $item->expiry_date->format('M j, Y') }}</td>
                    <td>
                        @php
                            $daysLeft = $item->expiry_date->diffInDays(now());
                        @endphp
                        {{ $daysLeft }} days
                    </td>
                    <td>{{ $item->quantity }} {{ $item->unit->abbreviation ?? '' }}</td>
                    <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                    <td>
                        @if($daysLeft <= 1)
                        <strong>USE TODAY</strong>
                        @elseif($daysLeft <= 3)
                        <strong>HIGH PRIORITY</strong>
                        @else
                        USE SOON
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Coffee Shop Inventory System | Food Safety First - Check Expiry Dates Regularly
    </div>
</body>
</html>