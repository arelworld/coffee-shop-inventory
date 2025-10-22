@extends('layouts.app')

@section('title', 'Coffee Shop Inventory Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Inventory Dashboard</h1>
            <p class="text-muted mb-0">Financial overview and business insights</p>
        </div>
        <div>
            <a href="{{ route('items.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-boxes me-2"></i>Manage Inventory
            </a>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Inventory Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₱{{ number_format($totalInventoryValue ?? 0, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalItems }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Low Stock Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockItemsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Out of Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $outOfStockItemsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiry Alerts -->
    @if($expiringSoonItems->count() > 0 || $expiredItems->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <!-- Expired Items Warning -->
            @if($expiredItems->count() > 0)
            <div class="alert alert-danger alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <i class="fas fa-skull-crossbones fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="alert-heading mb-1">
                                    <i class="fas fa-ban me-2"></i>Expired Items!
                                </h5>
                                <p class="mb-2">The following items have expired and should be discarded:</p>
                            </div>
                            @if(auth()->user()->role === 'manager')
                            <div>
                                <a href="{{ route('reports.expiry.pdf') }}" class="btn btn-sm btn-outline-danger me-1" title="PDF Report">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            @foreach($expiredItems as $item)
                            <div class="col-md-4 mb-1">
                                <span class="badge bg-danger me-2">
                                    <i class="fas fa-skull-crossbones me-1"></i>
                                    {{ $item->name }}
                                </span>
                                <small>
                                    Expired: {{ \Carbon\Carbon::parse($item->expiry_date)->format('M j, Y') }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Expiring Soon Warning -->
            @if($expiringSoonItems->count() > 0)
            <div class="alert alert-warning alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="alert-heading mb-1">
                                    <i class="fas fa-clock me-2"></i>Items Expiring Soon
                                </h5>
                                <p class="mb-2">The following items will expire in the next 3 days:</p>
                            </div>
                            @if(auth()->user()->role === 'manager')
                            <div>
                                <a href="{{ route('reports.expiry.pdf') }}" class="btn btn-sm btn-outline-warning me-1" title="PDF Report">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </div>
                            @endif
                        </div>
                        <div class="row">
                            @foreach($expiringSoonItems as $item)
                            <div class="col-md-4 mb-1">
                                <span class="badge bg-warning text-dark me-2">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $item->name }}
                                </span>
                                <small>
                                    Expires: {{ $item->expiry_date->format('M j, Y') }}
                                    ({{ $item->days_until_expiry }} days)
                                </small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Category Value Distribution -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie me-2"></i>Inventory Value by Category
                    </h6>
                    @if(auth()->user()->role === 'manager')
                    <div>
                        <a href="{{ route('reports.inventory.pdf') }}" class="btn btn-sm btn-outline-primary me-1" title="PDF Report">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <a href="{{ route('exports.inventory.csv') }}" class="btn btn-sm btn-outline-success" title="CSV Export">
                            <i class="fas fa-file-csv"></i>
                        </a>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Items</th>
                                    <th>Value</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryValues as $category)
                                @php
                                    $percentage = $totalInventoryValue > 0 ? ($category['value'] / $totalInventoryValue) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>{{ $category['name'] }}</td>
                                    <td>{{ $category['item_count'] }}</td>
                                    <td>₱{{ number_format($category['value'], 2) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $percentage }}%; background-color: {{ $loop->index % 2 == 0 ? '#4e73df' : '#1cc88a' }};"
                                                 aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ round($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Status Distribution -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Stock Status Overview
                    </h6>
                    @if(auth()->user()->role === 'manager')
                    <div>
                        <a href="{{ route('reports.inventory.pdf') }}" class="btn btn-sm btn-outline-primary me-1" title="PDF Report">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <a href="{{ route('exports.inventory.csv') }}" class="btn btn-sm btn-outline-success" title="CSV Export">
                            <i class="fas fa-file-csv"></i>
                        </a>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="stockStatusChart" width="400" height="200"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Safe Stock
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Low Stock
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Out of Stock
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Transactions -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exchange-alt me-2"></i>Recent Transactions
                    </h6>
                    <div>
                        @if(auth()->user()->role === 'manager')
                        <a href="{{ route('reports.transactions.pdf') }}" class="btn btn-sm btn-outline-primary me-1" title="PDF Report">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <a href="{{ route('exports.transactions.csv') }}" class="btn btn-sm btn-outline-success me-2" title="CSV Export">
                            <i class="fas fa-file-csv"></i>
                        </a>
                        @endif
                        <a href="{{ route('items.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>Value</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->item->name }}</td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'purchase' => 'success',
                                                'usage' => 'info',
                                                'waste' => 'warning',
                                                'adjustment' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $typeColors[$transaction->type] ?? 'secondary' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->quantity }} {{ $transaction->item->unit->abbreviation ?? '' }}</td>
                                    <td>₱{{ number_format($transaction->quantity * $transaction->cost_per_unit, 2) }}</td>
                                    <td>{{ $transaction->created_at->format('M j, H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Items -->
<div class="col-xl-6 col-lg-6 mb-4">
    <div class="card shadow mb-4 border-left-warning">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Items
            </h6>
            @if(auth()->user()->role === 'manager')
            <div>
                <a href="{{ route('reports.low-stock.pdf') }}" class="btn btn-sm btn-outline-warning me-1" title="PDF Report">
                    <i class="fas fa-file-pdf"></i>
                </a>
                <a href="{{ route('exports.low-stock.csv') }}" class="btn btn-sm btn-outline-success" title="CSV Export">
                    <i class="fas fa-file-csv"></i>
                </a>
            </div>
            @endif
        </div>
        <div class="card-body">
            @if($lowStockItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Current Stock</th>
                                <th>Min Required</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $item)
                            @php
                                $percentage = ($item->quantity / $item->reorder_point) * 100;
                                
                                if ($item->quantity == 0) {
                                    $status = 'Out of Stock';
                                    $badgeClass = 'danger';
                                } elseif ($percentage <= 25) {
                                    $status = 'Critical';
                                    $badgeClass = 'danger';
                                } elseif ($percentage <= 50) {
                                    $status = 'Low';
                                    $badgeClass = 'warning';
                                } else {
                                    $status = 'Warning';
                                    $badgeClass = 'info';
                                }
                            @endphp
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>
                                    <span class="fw-bold {{ $item->quantity == 0 ? 'text-danger' : 'text-warning' }}">
                                        {{ $item->quantity }}
                                    </span>
                                    <span class="text-muted ms-1">{{ $item->unit->abbreviation ?? '' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $item->reorder_point }}</span>
                                    <span class="text-muted ms-1">{{ $item->unit->abbreviation ?? '' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $badgeClass }}">
                                        @if($item->quantity == 0)
                                        <i class="fas fa-times-circle me-1"></i>
                                        @elseif($percentage <= 25)
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        @else
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        @endif
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">All items are properly stocked! 🎉</p>
                </div>
            @endif
        </div>
    </div>
</div>

        <!-- Most Used Items -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-fire me-2"></i>Most Used Items (Last 30 Days)
                    </h6>
                    @if(auth()->user()->role === 'manager')
                    <div>
                        <a href="{{ route('reports.transactions.pdf') }}" class="btn btn-sm btn-outline-primary me-1" title="PDF Report">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        <a href="{{ route('exports.transactions.csv') }}" class="btn btn-sm btn-outline-success" title="CSV Export">
                            <i class="fas fa-file-csv"></i>
                        </a>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($mostUsedItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity Used</th>
                                        <th>Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mostUsedItems as $usage)
                                    <tr>
                                        <td>{{ $usage->item->name }}</td>
                                        <td>{{ $usage->total_used }} {{ $usage->item->unit->abbreviation ?? '' }}</td>
                                        <td>₱{{ number_format($usage->total_used * $usage->item->cost_per_unit, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No usage data available for the last 30 days.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Stock Status Pie Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('stockStatusChart').getContext('2d');
    const stockStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Safe Stock', 'Low Stock', 'Out of Stock'],
            datasets: [{
                data: [{{ $safeStockItemsCount }}, {{ $lowStockItemsCount }}, {{ $outOfStockItemsCount }}],
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#dda20a', '#be2617'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 70,
        },
    });
});
</script>
@endsection