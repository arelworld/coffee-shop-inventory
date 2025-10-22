@extends('layouts.app')

@section('title', 'Edit Item')

@section('content')
<div class="container mt-4">
    <h2>Edit Item</h2>

    <form method="POST" action="{{ route('items.update', $item) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label fw-medium">Item Name</label>
            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-medium">Current Quantity</label>
                <input type="number" name="quantity" class="form-control" value="{{ $item->quantity }}" min="0" step="0.01" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-medium">Reorder Point</label>
                <input type="number" name="reorder_point" class="form-control" value="{{ $item->reorder_point }}" min="0" step="0.01" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-medium">Cost per Unit</label>
                <input type="number" name="cost_per_unit" class="form-control" value="{{ $item->cost_per_unit }}" min="0" step="0.01" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-medium">SKU</label>
                <input type="text" name="sku" class="form-control" value="{{ $item->sku }}" placeholder="Optional SKU">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-medium">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">Uncategorized</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-medium">Unit</label>
                <select name="unit_id" class="form-select" required>
                    <option value="">Select Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ $item->unit_id == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }} ({{ $unit->abbreviation }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Supplier</label>
            <select name="supplier_id" class="form-select">
                <option value="">No Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ $item->supplier_id == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Expiry Tracking Section -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-clock text-warning me-2"></i>Expiry Date Tracking
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="is_perishable" class="form-check-input" id="is_perishable" value="1" 
                            {{ $item->is_perishable ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="is_perishable">
                            This item is perishable
                        </label>
                    </div>
                </div>

                <div id="perishable-fields" style="display: {{ $item->is_perishable ? 'block' : 'none' }};">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control" 
                                   value="{{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '' }}" 
                                   min="{{ date('Y-m-d') }}">
                            <div class="form-text">When this item expires</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Manufacture Date</label>
                            <input type="date" name="manufacture_date" class="form-control" 
                                   value="{{ $item->manufacture_date ? $item->manufacture_date->format('Y-m-d') : '' }}" 
                                   max="{{ date('Y-m-d') }}">
                            <div class="form-text">When this item was made</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Shelf Life (Days)</label>
                            <input type="number" name="shelf_life_days" class="form-control" 
                                   value="{{ $item->shelf_life_days }}" min="1" placeholder="e.g., 7">
                            <div class="form-text">How many days this item lasts</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Batch Number</label>
                            <input type="text" name="batch_number" class="form-control" 
                                   value="{{ $item->batch_number }}" placeholder="e.g., BATCH-001">
                            <div class="form-text">Optional batch tracking</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Update Item
            </button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const perishableCheckbox = document.getElementById('is_perishable');
    const perishableFields = document.getElementById('perishable-fields');
    
    if (perishableCheckbox) {
        perishableCheckbox.addEventListener('change', function() {
            if (perishableFields) {
                perishableFields.style.display = this.checked ? 'block' : 'none';
            }
        });
    }
});
</script>
@endsection