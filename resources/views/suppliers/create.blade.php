@extends('layouts.app')

@section('title', 'Add New Supplier - Coffee Shop')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle text-primary me-2"></i>Add New Supplier
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Supplier Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g., Local Coffee Roaster" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" placeholder="e.g., Juan Dela Cruz">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" placeholder="e.g., 09171234567">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="e.g., supplier@example.com">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-medium">Address</label>
                            <textarea name="address" class="form-control" placeholder="Full supplier address..." rows="3"></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Suppliers
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection