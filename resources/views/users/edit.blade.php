@extends('layouts.app')

@section('title', 'Edit Staff Member - Coffee Shop Inventory')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit text-primary me-2"></i>Edit Staff Member: {{ $user->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff Member</option>
                                    <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                                </select>
                                @error('role')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                
                                @if(auth()->id() === $user->id)
                                    <div class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        You cannot change your own role from manager.
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                       value="1" {{ $user->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="is_active">
                                    Account is active
                                </label>
                            </div>
                            <div class="form-text">
                                Inactive users cannot log into the system.
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Staff Account
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>

                    <!-- Password Update Section -->
                    <hr class="my-4">
                    <form action="{{ route('users.update-password', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h6 class="fw-medium mb-3">
                            <i class="fas fa-key me-2"></i>Change Password
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">New Password</label>
                                <input type="password" name="password" class="form-control" required>
                                @error('password')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection