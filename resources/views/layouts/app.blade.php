<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Coffee Shop Inventory Management')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4A6FDC;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --header-bg: #ffffff;
            --card-shadow: 0 0.125rem 0.375rem 0 rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #wrapper {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        /* Sidebar */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: var(--sidebar-bg);
            color: #fff;
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        #sidebar.collapsed {
            margin-left: -260px;
        }

        #sidebar .sidebar-header {
            padding: 1.5rem 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        #sidebar .sidebar-header .brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: white;
            text-decoration: none;
        }

        #sidebar ul.components {
            padding: 1rem 0;
            margin: 0;
            list-style: none;
        }

        #sidebar ul li {
            margin: 0.25rem 0.75rem;
        }

        #sidebar ul li a {
            color: #cbd5e1;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }

        #sidebar ul li a:hover {
            background: var(--sidebar-hover);
            color: white;
        }

        #sidebar ul li a.active {
            background: var(--primary-color);
            color: white;
        }

        #sidebar ul li a i {
            width: 1.5rem;
            margin-right: 0.75rem;
            text-align: center;
        }

        #content {
            width: 100%;
            padding: 0;
            overflow-x: auto;
            background-color: #f8fafc;
        }

        /* Navbar */
        .navbar {
            background: var(--header-bg);
            box-shadow: var(--card-shadow);
            padding: 0.75rem 1rem;
        }

        .navbar-brand {
            font-weight: 600;
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: 0.5rem;
        }

        /* Progress bars */
        .progress {
            border-radius: 0.375rem;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }

        /* Buttons */
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
        }

        /* Table */
        .table > :not(caption) > * > * {
            padding: 0.875rem 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* DataTables customization */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 1rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            border: 1px solid #dee2e6;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -260px;
            }
            #sidebar.collapsed {
                margin-left: 0;
            }
            #content {
                margin-left: 0;
            }
        }

        
    </style>

    @yield('styles')
</head>
<body>
    <!-- Top navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <button type="button" id="sidebarCollapse" class="btn btn-light">
                <i class="fas fa-bars"></i>
            </button>
            <a class="navbar-brand ms-2" href="{{ route('items.index') }}">
                <i class="fas fa-coffee me-2 text-primary"></i>Coffee Inventory Pro
            </a>
            
            <div class="d-flex align-items-center">
                <!-- Replace the entire user dropdown section with this -->
@auth
    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>My Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
@else
    <a href="{{ route('login') }}" class="btn btn-outline-primary">
        <i class="fas fa-sign-in-alt me-2"></i>Staff Login
    </a>
@endauth
            </div>
        </div>
    </nav>

    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('items.index') }}" class="brand">
                    <i class="fas fa-coffee me-2"></i>Coffee Inventory
                </a>
            </div>
       <ul class="components">
    <li>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i>Dashboard
        </a>
    </li>

    @auth
        <!-- Inventory Management - Accessible to both staff and managers -->
        @if(in_array(auth()->user()->role, ['staff', 'manager']))
            <li>
                <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>Inventory Management
                </a>
            </li>
        @endif

        <!-- Suppliers - Accessible to both staff and managers -->
        @if(in_array(auth()->user()->role, ['staff', 'manager']))
            <li>
                <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>Suppliers
                </a>
            </li>
        @endif

        <!-- Staff Management - Manager only -->
        @if(auth()->user()->role === 'manager')
            <li>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>Staff Management
                </a>
            </li>
        @endif
    @endauth
</ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function () {
        // Sidebar toggle
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('collapsed');
        });

        // Set active sidebar item based on current page - FIXED LOGIC
        const currentPath = window.location.pathname;
        $('.components li a').each(function() {
            const linkPath = $(this).attr('href');
            if (linkPath && currentPath === linkPath) {
                $(this).addClass('active');
                $(this).closest('li').siblings().find('a').removeClass('active');
            }
        });

        // Initialize DataTables if present
        if ($.fn.DataTable) {
            $('.datatable').DataTable({
                "pageLength": 25,
                "responsive": true
            });
        }
    });
</script>

    @yield('scripts')
</body>
</html>