@extends('layouts.app')

@section('title', $supplier->name . ' - Coffee Shop')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $supplier->name }}</h1>
            <p class="text-muted mb-0">Supplier details and associated items</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
            </a>
        </div>
    </div>

    <!-- Supplier Info Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>Supplier Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium text-muted" width="40%">Contact Person:</td>
                                    <td>{{ $supplier->contact_person ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Phone:</td>
                                    <td>
                                        @if($supplier->phone)
                                            <i class="fas fa-phone me-2 text-muted"></i>{{ $supplier->phone }}
                                        @else
                                            Not specified
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-medium text-muted" width="40%">Email:</td>
                                    <td>
                                        @if($supplier->email)
                                            <i class="fas fa-envelope me-2 text-muted"></i>{{ $supplier->email }}
                                        @else
                                            Not specified
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium text-muted">Items Supplied:</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $items->count() }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($supplier->address)
                    <div class="mt-3">
                        <label class="form-label fw-medium text-muted">Address:</label>
                        <p class="mb-0">{{ $supplier->address }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Inventory Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h3 text-primary">₱{{ number_format($totalInventoryValue, 2) }}</div>
                        <small class="text-muted">Total Inventory Value</small>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h5 mb-0">{{ $items->count() }}</div>
                            <small class="text-muted">Items</small>
                        </div>
                        <div class="col-4">
                            <div class="h5 mb-0 text-warning">{{ $lowStockItems->count() }}</div>
                            <small class="text-muted">Low Stock</small>
                        </div>
                        <div class="col-4">
                            <div class="h5 mb-0 text-danger">{{ $outOfStockItems->count() }}</div>
                            <small class="text-muted">Out of Stock</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Items -->
    <div class="card shadow">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-boxes text-primary me-2"></i>Supplied Items
            </h5>
        </div>
        <div class="card-body p-0">
            @if($items->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Item Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Unit Cost</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        @php
                            $statusClass = [
                                'safe' => ['class' => 'success', 'icon' => 'fa-check-circle'],
                                'low' => ['class' => 'warning', 'icon' => 'fa-exclamation-triangle'],
                                'out' => ['class' => 'danger', 'icon' => 'fa-times-circle']
                            ][$item->stock_status];
                        @endphp
                        <tr>
                            <td class="ps-4 fw-medium">{{ $item->name }}</td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $item->category->name ?? 'Uncategorized' }}</span>
                            </td>
                            <td>
                                <span class="fw-bold">{{ $item->quantity }}</span>
                                <small class="text-muted">{{ $item->unit->abbreviation ?? '' }}</small>
                            </td>
                            <td>₱{{ number_format($item->cost_per_unit, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $statusClass['class'] }}">
                                    <i class="fas {{ $statusClass['icon'] }} me-1"></i>{{ ucfirst($item->stock_status) }}
                                </span>
                            </td>
                            <td class="text-end pe-4 fw-medium">
                                ₱{{ number_format($item->quantity * $item->cost_per_unit, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No items from this supplier</h5>
                <p class="text-muted">This supplier doesn't have any items in your inventory yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection