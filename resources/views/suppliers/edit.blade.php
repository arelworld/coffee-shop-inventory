@extends('layouts.app')

@section('title', 'Edit Supplier - Coffee Shop')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-edit text-primary me-2"></i>Edit Supplier: {{ $supplier->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Supplier Name *</label>
                                <input type="text" name="name" class="form-control" value="{{ $supplier->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" value="{{ $supplier->contact_person }}">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Phone Number</label>
                                <input type="tel" name="phone" class="form-control" value="{{ $supplier->phone }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ $supplier->email }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-medium">Address</label>
                            <textarea name="address" class="form-control" rows="3">{{ $supplier->address }}</textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection