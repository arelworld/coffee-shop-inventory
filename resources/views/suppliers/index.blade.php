@extends('layouts.app')

@section('title', 'Supplier Management - Coffee Shop')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Supplier Management</h1>
            <p class="text-muted mb-0">Manage your coffee shop suppliers and contacts</p>
        </div>
        <div>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Supplier
            </a>
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

    <!-- Supplier Cards Grid -->
    <div class="row">
        @foreach($suppliers as $supplier)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 font-weight-bold text-primary mb-1">
                                {{ $supplier->name }}
                            </div>
                            <div class="text-xs text-muted mb-1">
                                @if($supplier->contact_person)
                                    <i class="fas fa-user me-1"></i>{{ $supplier->contact_person }}
                                @endif
                            </div>
                            @if($supplier->phone)
                            <div class="text-xs text-muted mb-1">
                                <i class="fas fa-phone me-1"></i>{{ $supplier->phone }}
                            </div>
                            @endif
                            @if($supplier->email)
                            <div class="text-xs text-muted mb-2">
                                <i class="fas fa-envelope me-1"></i>{{ $supplier->email }}
                            </div>
                            @endif
                            
                            <div class="row mt-3">
                                <div class="col-6">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Items</div>
                                    <div class="h5 mb-0 text-gray-800">{{ $supplier->items_count }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-xs font-weight-bold text-uppercase mb-1">Value</div>
                                    <div class="h5 mb-0 text-gray-800">₱{{ number_format($supplier->inventory_value, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="/suppliers/{{ $supplier->id }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>View Details
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($suppliers->isEmpty())
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="fas fa-truck fa-3x text-muted"></i>
        </div>
        <h5 class="text-muted">No suppliers found</h5>
        <p class="text-muted mb-3">Your sample suppliers will appear here</p>
    </div>
    @endif
</div>
@endsection