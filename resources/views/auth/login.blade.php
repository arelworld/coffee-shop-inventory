@extends('layouts.auth')

@section('title', 'KOFI — Login')

@section('content')
<style>
    body{
        background:#f7f5f2;
        font-family: 'Segoe UI', sans-serif;
    }

    .login-card{
        background:#ffffff;
        border-radius:16px;
        padding:40px;
        box-shadow:0 10px 30px rgba(0,0,0,0.08);
    }

    .kofi-title{
        font-weight:700;
        letter-spacing:4px;
        font-size:28px;
        color:#4b3621;
    }

    .kofi-sub{
        font-size:13px;
        color:#888;
        margin-bottom:30px;
    }

    .form-control{
        border-radius:10px;
        padding:12px;
        border:1px solid #e5e5e5;
    }

    .form-control:focus{
        border-color:#8b5e3c;
        box-shadow:none;
    }

    .login-btn{
        background:#4b3621;
        color:white;
        border-radius:10px;
        padding:12px;
        font-weight:500;
        border:none;
        transition:0.2s;
    }

    .login-btn:hover{
        background:#3a2919;
    }

    .footer-text{
        font-size:12px;
        color:#aaa;
        margin-top:20px;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center align-items-center" style="min-height:80vh;">
        <div class="col-md-4">
            <div class="login-card text-center">

                <div class="kofi-title">KOFI</div>
                <div class="kofi-sub">Coffee Inventory System</div>

                @if($errors->any())
                    <div class="alert alert-danger text-start">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-3 text-start">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="your@email.com">
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required placeholder="Enter password">
                    </div>

                    <div class="mb-3 form-check text-start">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="login-btn w-100">
                        Login
                    </button>
                </form>

                <div class="footer-text">
                    Staff access only
                </div>

            </div>
        </div>
    </div>
</div>
@endsection