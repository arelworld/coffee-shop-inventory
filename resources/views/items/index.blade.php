@extends('layouts.app')

@section('title', 'Coffee Shop Inventory Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Inventory Dashboard</h1>
            <p class="text-muted mb-0">Manage your coffee shop stock and supplies</p>
        </div>
        <div class="d-flex align-items-center">
            <!-- Quick Stats -->
            <div class="me-4">
                <div class="d-flex">
                    <div class="me-3 text-center">
                        <div class="fw-bold text-primary">{{ $totalItems ?? 0 }}</div>
                        <small class="text-muted">Total Items</small>
                    </div>
                    <div class="me-3 text-center">
                        <div class="fw-bold text-warning">{{ $lowStockItemsCount ?? 0 }}</div>
                        <small class="text-muted">Low Stock</small>
                    </div>
                    <div class="me-3 text-center">
                        <div class="fw-bold text-danger">{{ $outOfStockItemsCount ?? 0 }}</div>
                        <small class="text-muted">Out of Stock</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-success">₱{{ number_format($totalInventoryValue ?? 0, 2) }}</div>
                        <small class="text-muted">Inventory Value</small>
                    </div>
                </div>
            </div>
            
            <!-- Add Item Button -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createItemModal">
                <i class="fas fa-plus me-2"></i>Add New Item
            </button>
        </div>
    </div>

    <!-- Low Stock Alerts Section -->
    @php
        $lowStockItems = \App\Models\Item::where('quantity', '>', 0)
                            ->where('quantity', '<=', \DB::raw('reorder_point'))
                            ->with(['category', 'supplier'])
                            ->get();
        
        $outOfStockItems = \App\Models\Item::where('quantity', '<=', 0)
                            ->with(['category', 'supplier'])
                            ->get();
    @endphp

    @if($lowStockItems->count() > 0 || $outOfStockItems->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <!-- Low Stock Warning -->
            @if($lowStockItems->count() > 0)
            <div class="alert alert-warning alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">
                            <i class="fas fa-box me-2"></i>Low Stock Alert!
                        </h5>
                        <p class="mb-2">The following items are running low and need restocking:</p>
                        <div class="row">
                            @foreach($lowStockItems as $item)
                            <div class="col-md-4 mb-1">
                                <span class="badge bg-warning text-dark me-2">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $item->name }}
                                </span>
                                <small class="text-muted">
                                    (Stock: {{ $item->quantity }} {{ $item->unit->abbreviation ?? '' }}, Need: {{ $item->reorder_point }}+)
                                </small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Out of Stock Warning -->
            @if($outOfStockItems->count() > 0)
            <div class="alert alert-danger alert-dismissible fade show">
                <div class="d-flex align-items-center">
                    <i class="fas fa-times-circle fa-2x me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-1">
                            <i class="fas fa-ban me-2"></i>Out of Stock!
                        </h5>
                        <p class="mb-2">The following items are completely out of stock:</p>
                        <div class="row">
                            @foreach($outOfStockItems as $item)
                            <div class="col-md-4 mb-1">
                                <span class="badge bg-danger me-2">
                                    <i class="fas fa-times me-1"></i>
                                    {{ $item->name }}
                                </span>
                                <small>Urgent restock needed!</small>
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

    <!-- Filters & Search Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search items...">
                    </div>
                </div>
                <div class="col-md-3">
                    <form method="GET" class="mb-0">
                        <select name="category_id" class="form-select" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-md-3">
                    <select id="stockFilter" class="form-select">
                        <option value="">All Stock Levels</option>
                        <option value="safe">Safe Stock</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-outline-secondary" id="resetFilters">
                        <i class="fas fa-redo me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Add this after the filters card in items/index.blade.php -->

<!-- Quick Export Buttons -->
<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('reports.inventory.pdf') }}" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-file-pdf me-1"></i>Export PDF
            </a>
            <a href="{{ route('reports.low-stock.pdf') }}" class="btn btn-sm btn-outline-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>Stock Alerts PDF
            </a>

            <!-- CSV buttons -->
    <a href="{{ route('exports.inventory.csv') }}" class="btn btn-sm btn-outline-success">
        <i class="fas fa-file-csv me-1"></i>Export CSV
    </a>
    <a href="{{ route('exports.low-stock.csv') }}" class="btn btn-sm btn-outline-warning">
        <i class="fas fa-exclamation-triangle me-1"></i>Stock Alerts CSV
    </a>
        </div>
    </div>
</div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Inventory Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="inventory-table">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Item Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                            <th>Unit</th>
                            <th>Cost/Unit</th>
                            <th>Supplier</th>
                            <th>Reorder Point</th>
                            <th>Status</th>
                            <th>Stock Level</th>
                            <th>Expiry Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            @php
                                $statusClass = [
                                    'safe' => ['class' => 'success', 'icon' => 'fa-check-circle', 'text' => 'Safe', 'row_class' => ''],
                                    'low' => ['class' => 'warning', 'icon' => 'fa-exclamation-triangle', 'text' => 'Low Stock', 'row_class' => 'table-warning'],
                                    'out' => ['class' => 'danger', 'icon' => 'fa-times-circle', 'text' => 'Out of Stock', 'row_class' => 'table-danger']
                                ][$item->stock_status];
                            @endphp
                          
                            <tr class="inventory-item" data-status="{{ $item->stock_status }}">
                                <td class="ps-4 fw-medium">
                                    <div>{{ $item->name }}</div>
                                    @if($item->sku)
                                        <small class="text-muted">SKU: {{ $item->sku }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $item->category?->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $item->quantity }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $item->unit->abbreviation ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="fw-medium">₱{{ number_format($item->cost_per_unit, 2) }}</span>
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $item->supplier?->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $item->reorder_point }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusClass['class'] }}">
                                        <i class="fas {{ $statusClass['icon'] }} me-1"></i>{{ $statusClass['text'] }}
                                    </span>
                                </td>
                                <td style="width: 180px;">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $statusClass['class'] }}" 
                                                 style="width:{{ $item->stock_percentage }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted ms-2">{{ round($item->stock_percentage) }}%</small>
                                    </div>
                                </td>
                                <td>
                                    @if($item->is_perishable && $item->expiry_date)
                                        <span class="badge bg-{{ $item->expiry_status_color }}">
                                            <i class="fas 
                                                @if($item->expiry_status == 'expired') fa-exclamation-triangle
                                                @elseif($item->expiry_status == 'expires-soon') fa-exclamation-circle
                                                @elseif($item->expiry_status == 'expires-today') fa-skull-crossbones
                                                @else fa-check-circle
                                                @endif
                                            me-1"></i>
                                            {{ $item->expiry_status_text }}
                                            @if($item->days_until_expiry !== null)
                                                <br><small>({{ $item->days_until_expiry }} days)</small>
                                            @endif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Non-Perishable</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
    <div class="btn-group btn-group-sm">
        <button class="btn btn-outline-primary edit-btn"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                data-quantity="{{ $item->quantity }}"
                data-reorder="{{ $item->reorder_point }}"
                data-category="{{ $item->category_id }}"
                data-supplier="{{ $item->supplier_id }}"
                data-unit="{{ $item->unit_id }}"
                data-cost="{{ $item->cost_per_unit }}"
                data-sku="{{ $item->sku }}"
                data-is-perishable="{{ $item->is_perishable }}"
                data-expiry-date="{{ $item->expiry_date ? (\Carbon\Carbon::parse($item->expiry_date)->format('Y-m-d')) : '' }}"
                data-manufacture-date="{{ $item->manufacture_date ? (\Carbon\Carbon::parse($item->manufacture_date)->format('Y-m-d')) : '' }}"
                data-shelf-life-days="{{ $item->shelf_life_days }}"
                data-batch-number="{{ $item->batch_number }}"
                title="Edit Item">
            <i class="fas fa-edit"></i>
        </button>

        <button class="btn btn-outline-success restock-btn"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                title="Restock Item">
            <i class="fas fa-truck"></i>
        </button>

                                        <button class="btn btn-outline-info usage-btn"
                                                data-id="{{ $item->id }}"
                                                data-name="{{ $item->name }}"
                                                data-quantity="{{ $item->quantity }}"
                                                data-unit="{{ $item->unit->abbreviation ?? '' }}"
                                                title="Record Usage/Waste">
                                            <i class="fas fa-utensils"></i>
                                        </button>

                                        @auth
    @if(auth()->user()->canDeleteItems())
        <button class="btn btn-outline-danger delete-btn"
                data-id="{{ $item->id }}"
                data-name="{{ $item->name }}"
                title="Delete Item">
            <i class="fas fa-trash"></i>
        </button>
    @endif
@endauth
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Empty State -->
            @if($items->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-box-open fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted">No inventory items found</h5>
                <p class="text-muted mb-3">Get started by adding your first item</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createItemModal">
                    <i class="fas fa-plus me-2"></i>Add New Item
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Item Modal -->
<div class="modal fade" id="createItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('items.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle text-primary me-2"></i>Add New Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Item Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter item name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Current Quantity</label>
                            <input type="number" name="quantity" class="form-control" min="0" step="0.01" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Reorder Point</label>
                            <input type="number" name="reorder_point" class="form-control" min="0" step="0.01" placeholder="Set reorder level" required>
                            <div class="form-text">Alert when stock falls below this level</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Cost per Unit</label>
                            <input type="number" name="cost_per_unit" class="form-control" min="0" step="0.01" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">SKU</label>
                            <input type="text" name="sku" class="form-control" placeholder="Optional SKU">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">Uncategorized</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Unit</label>
                            <select name="unit_id" class="form-select" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">No Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Expiry Tracking Section for Create Modal -->
                    <div class="card mt-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fs-6">
                                <i class="fas fa-clock text-warning me-2"></i>Expiry Date Tracking
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="mb-2">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_perishable" class="form-check-input" id="create-is-perishable" value="1">
                                    <label class="form-check-label fw-medium" for="create-is-perishable">
                                        This item is perishable
                                    </label>
                                </div>
                            </div>

                            <div id="create-perishable-fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-medium small">Expiry Date</label>
                                        <input type="date" name="expiry_date" class="form-control form-control-sm" min="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-medium small">Manufacture Date</label>
                                        <input type="date" name="manufacture_date" class="form-control form-control-sm" max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-medium small">Shelf Life (Days)</label>
                                        <input type="number" name="shelf_life_days" class="form-control form-control-sm" min="1" placeholder="e.g., 7">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-medium small">Batch Number</label>
                                        <input type="text" name="batch_number" class="form-control form-control-sm" placeholder="e.g., BATCH-001">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="editItemForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-edit text-primary me-2"></i>Edit Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Item Name</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Current Quantity</label>
                            <input type="number" name="quantity" id="edit-quantity" class="form-control" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Reorder Point</label>
                            <input type="number" name="reorder_point" id="edit-reorder" class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Cost per Unit</label>
                            <input type="number" name="cost_per_unit" id="edit-cost" class="form-control" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">SKU</label>
                            <input type="text" name="sku" id="edit-sku" class="form-control" placeholder="Optional SKU">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Category</label>
                            <select name="category_id" id="edit-category" class="form-select">
                                <option value="">Uncategorized</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Unit</label>
                            <select name="unit_id" id="edit-unit" class="form-select" required>
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Supplier</label>
                        <select name="supplier_id" id="edit-supplier" class="form-select">
                            <option value="">No Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Expiry Tracking Section for Edit Modal -->
                    <div class="card mt-3">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fs-6">
                                <i class="fas fa-clock text-warning me-2"></i>Expiry Date Tracking
                            </h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="mb-2">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_perishable" class="form-check-input" id="edit-is-perishable" value="1">
                                    <label class="form-check-label fw-medium" for="edit-is-perishable">
                                        This item is perishable
                                    </label>
                                </div>
                            </div>

                            <div id="edit-perishable-fields" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-medium small">Expiry Date</label>
                                        <input type="date" name="expiry_date" id="edit-expiry-date" class="form-control form-control-sm" min="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-medium small">Manufacture Date</label>
                                        <input type="date" name="manufacture_date" id="edit-manufacture-date" class="form-control form-control-sm" max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-medium small">Shelf Life (Days)</label>
                                        <input type="number" name="shelf_life_days" id="edit-shelf-life" class="form-control form-control-sm" min="1" placeholder="e.g., 7">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-medium small">Batch Number</label>
                                        <input type="text" name="batch_number" id="edit-batch-number" class="form-control form-control-sm" placeholder="e.g., BATCH-001">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteItemForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-trash-alt fa-2x text-danger"></i>
                    </div>
                    <h5>Delete Item</h5>
                    <p class="text-muted">Are you sure you want to delete <strong id="delete-item-name" class="text-danger"></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restock Modal -->
<div class="modal fade" id="restockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('items.restock') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-truck-loading text-success me-2"></i>Restock Item
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="item_id" id="restock-item-id">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Restocking: <strong id="restock-item-name"></strong>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Quantity to Add</label>
                            <input type="number" name="quantity" class="form-control" min="0.01" step="0.01" placeholder="0.00" required>
                            <div class="form-text">Current stock: <span id="current-stock"></span></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Cost per Unit</label>
                            <input type="number" name="cost_per_unit" class="form-control" min="0" step="0.01" placeholder="0.00" required>
                            <div class="form-text">New cost for this item</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" placeholder="e.g., Delivery from supplier, batch number, etc." rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Record Restock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Usage/Waste Tracking Modal -->
<div class="modal fade" id="usageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('items.record-usage') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-utensils text-info me-2"></i>Record Usage/Waste
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Transaction Type</label>
                        <select name="type" class="form-select" id="usage-type" required>
                            <option value="usage">Product Usage</option>
                            <option value="waste">Waste/Spillage</option>
                            <option value="adjustment">Stock Adjustment</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Select Item</label>
                        <select name="item_id" class="form-select" id="usage-item-id" required>
                            <option value="">Choose item...</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" data-current-stock="{{ $item->quantity }}" data-unit="{{ $item->unit->abbreviation ?? '' }}">
                                    {{ $item->name }} (Current: {{ $item->quantity }} {{ $item->unit->abbreviation ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Quantity</label>
                        <input type="number" name="quantity" class="form-control" id="usage-quantity" min="0.01" step="0.01" placeholder="0.00" required>
                        <div class="form-text" id="usage-unit-display"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Reason/Notes (Optional)</label>
                        <textarea name="notes" class="form-control" placeholder="e.g., Made 10 cappuccinos, Spilled milk, Expired beans..." rows="3" id="usage-notes"></textarea>
                        <div class="form-text" id="usage-examples">
                            <strong>Examples:</strong><br>
                            <span class="text-success">Usage:</span> "Made 15 lattes", "Prepped cold brew"<br>
                            <span class="text-danger">Waste:</span> "Spilled milk", "Expired pastries"<br>
                            <span class="text-warning">Adjustment:</span> "Found extra stock", "Counting error"
                        </div>
                    </div>

                    <div class="alert alert-warning" id="usage-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="warning-text"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-2"></i>Record Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable with enhanced options
    $('#inventory-table').DataTable({
        "pageLength": 25,
        "order": [[ 0, "asc" ]],
        "language": {
            "search": "",
            "searchPlaceholder": "Search items...",
            "lengthMenu": "Show _MENU_ items",
            "info": "Showing _START_ to _END_ of _TOTAL_ items",
            "infoEmpty": "No items available",
            "infoFiltered": "(filtered from _MAX_ total items)"
        },
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
    
    // Custom search functionality
    $('#searchInput').on('keyup', function() {
        $('#inventory-table').DataTable().search($(this).val()).draw();
    });
    
    // Stock level filter - enhanced
    $('#stockFilter').on('change', function() {
        const status = $(this).val();
        const table = $('#inventory-table').DataTable();
        
        if (status === 'low') {
            table.column(7).search('Low Stock', true, false).draw();
        } else if (status === 'out') {
            table.column(7).search('Out of Stock', true, false).draw();
        } else if (status === 'safe') {
            table.column(7).search('Safe', true, false).draw();
        } else {
            table.column(7).search('').draw();
        }
    });
    
    // Reset filters
    $('#resetFilters').on('click', function() {
        $('#searchInput').val('');
        $('#stockFilter').val('');
        $('#inventory-table').DataTable().search('').columns().search('').draw();
    });

    // Edit modal
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let quantity = $(this).data('quantity');
        let reorder = $(this).data('reorder');
        let category = $(this).data('category');
        let supplier = $(this).data('supplier');
        let unit = $(this).data('unit');
        let cost = $(this).data('cost');
        let sku = $(this).data('sku');
        let isPerishable = $(this).data('is-perishable');
        let expiryDate = $(this).data('expiry-date');
        let manufactureDate = $(this).data('manufacture-date');
        let shelfLifeDays = $(this).data('shelf-life-days');
        let batchNumber = $(this).data('batch-number');

        $('#editItemForm').attr('action', '/items/' + id);
        $('#edit-name').val(name);
        $('#edit-quantity').val(quantity);
        $('#edit-reorder').val(reorder);
        $('#edit-category').val(category);
        $('#edit-supplier').val(supplier);
        $('#edit-unit').val(unit);
        $('#edit-cost').val(cost);
        $('#edit-sku').val(sku);

        // Set perishable fields
        $('#edit-is-perishable').prop('checked', isPerishable == 1);
        if (isPerishable == 1) {
            $('#edit-perishable-fields').show();
            $('#edit-expiry-date').val(expiryDate);
            $('#edit-manufacture-date').val(manufactureDate);
            $('#edit-shelf-life').val(shelfLifeDays);
            $('#edit-batch-number').val(batchNumber);
        } else {
            $('#edit-perishable-fields').hide();
        }

        let editModal = new bootstrap.Modal(document.getElementById('editItemModal'));
        editModal.show();
    });

    // Delete modal
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');

        $('#deleteItemForm').attr('action', '/items/' + id);
        $('#delete-item-name').text(name);

        let deleteModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
        deleteModal.show();
    });

    // Restock button handler
    $(document).on('click', '.restock-btn', function() {
        const itemId = $(this).data('id');
        const itemName = $(this).data('name');
        const currentStock = $(this).closest('tr').find('td:eq(2)').text().trim();
        
        $('#restock-item-id').val(itemId);
        $('#restock-item-name').text(itemName);
        $('#current-stock').text(currentStock);
        
        $('#restockModal .modal-title').html(
            `<i class="fas fa-truck-loading text-success me-2"></i>Restock ${itemName}`
        );

        let restockModal = new bootstrap.Modal(document.getElementById('restockModal'));
        restockModal.show();
    });

    // Usage/Waste button handler
    $(document).on('click', '.usage-btn', function() {
        console.log('Usage button clicked');
        
        let id = $(this).data('id');
        let name = $(this).data('name');
        let quantity = $(this).data('quantity');
        let unit = $(this).data('unit');
        
        $('#usage-item-id').val(id);
        $('#usage-quantity').val('');
        $('#usage-notes').val('');
        $('#usage-warning').hide();
        
        $('#usage-unit-display').text(`Current stock: ${quantity} ${unit}`);
        
        $('#usageModal .modal-title').html(
            `<i class="fas fa-utensils text-info me-2"></i>Record Usage/Waste for ${name}`
        );

        let usageModal = new bootstrap.Modal(document.getElementById('usageModal'));
        usageModal.show();
    });

    // Expiry tracking for create modal
    $('#create-is-perishable').on('change', function() {
        const fields = $('#create-perishable-fields');
        if (this.checked) {
            fields.show();
        } else {
            fields.hide();
        }
    });

    // Expiry tracking for edit modal
    $('#edit-is-perishable').on('change', function() {
        const fields = $('#edit-perishable-fields');
        if (this.checked) {
            fields.show();
        } else {
            fields.hide();
        }
    });

    // Auto-hide alerts after 10 seconds (but keep critical ones)
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-danger)');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 10000);

    // Initialize Bootstrap alerts
    var alertList = document.querySelectorAll('.alert');
    alertList.forEach(function (alert) {
        new bootstrap.Alert(alert);
    });

    // Add click to filter low stock items
    $(document).on('click', '.low-stock-filter', function() {
        $('#stockFilter').val('low').trigger('change');
    });

    $(document).on('click', '.out-of-stock-filter', function() {
        $('#stockFilter').val('out').trigger('change');
    });
});

// Real-time validation for usage modal
document.addEventListener('DOMContentLoaded', function() {
    const usageQuantity = document.getElementById('usage-quantity');
    const usageType = document.getElementById('usage-type');
    const usageItemId = document.getElementById('usage-item-id');
    
    if (usageQuantity) {
        usageQuantity.addEventListener('input', function() {
            const quantity = parseFloat(this.value) || 0;
            const selectedItem = document.getElementById('usage-item-id');
            const currentStock = parseFloat(selectedItem.options[selectedItem.selectedIndex]?.getAttribute('data-current-stock')) || 0;
            const unit = selectedItem.options[selectedItem.selectedIndex]?.getAttribute('data-unit') || '';
            const type = document.getElementById('usage-type').value;
            
            const warning = document.getElementById('usage-warning');
            const warningText = document.getElementById('warning-text');
            
            if (warning) warning.style.display = 'none';
            
            if (quantity > 0 && warningText) {
                if ((type === 'usage' || type === 'waste') && quantity > currentStock) {
                    warningText.innerHTML = `<strong>Warning:</strong> Quantity (${quantity} ${unit}) exceeds current stock (${currentStock} ${unit})`;
                    warning.className = 'alert alert-warning';
                    warning.style.display = 'block';
                } else if (type === 'adjustment' && quantity > 0) {
                    warningText.innerHTML = `<strong>Note:</strong> This will add ${quantity} ${unit} to stock (adjustment)`;
                    warning.className = 'alert alert-success';
                    warning.style.display = 'block';
                }
            }
        });
    }
    
    if (usageType) {
        usageType.addEventListener('change', function() {
            document.getElementById('usage-quantity')?.dispatchEvent(new Event('input'));
        });
    }
    
    if (usageItemId) {
        usageItemId.addEventListener('change', function() {
            document.getElementById('usage-quantity')?.dispatchEvent(new Event('input'));
        });
    }
});
</script>
@endsection