<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EasyStock - @yield('title', 'Pharmacy Management System')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        :root {
            --primary-color: #2A5C7D;
            --primary-dark: #1E4560;
            --secondary-color: #4ECDC4;
            --accent-color: #FF6B6B;
            --dark-color: #2C3E50;
            --light-color: #F7F9FC;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            transition: width 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar.collapsed .brand-text,
        .sidebar.collapsed .user-details,
        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .sidebar-footer span,
        .sidebar.collapsed .dropdown-icon,
        .sidebar.collapsed .submenu {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 15px;
        }

        .sidebar.collapsed .nav-link i {
            margin: 0;
            font-size: 1.3rem;
        }

        .sidebar.collapsed .user-info {
            padding: 20px 0;
            justify-content: center;
        }

        .sidebar.collapsed .user-avatar i {
            font-size: 2rem;
        }

        .sidebar.collapsed .sidebar-footer {
            padding: 20px 0;
        }

        .sidebar.collapsed .footer-link {
            justify-content: center;
        }

        .sidebar.collapsed .footer-link i {
            margin: 0;
        }

        /* Sidebar Header */
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.2rem;
        }

        .sidebar-brand i {
            font-size: 1.8rem;
            color: var(--secondary-color);
        }

        .sidebar-toggler {
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .sidebar-toggler:hover {
            background: rgba(255,255,255,0.2);
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .user-avatar i {
            font-size: 2.5rem;
            color: var(--secondary-color);
        }

        .user-details {
            overflow: hidden;
        }

        .user-name {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .user-role {
            display: block;
            margin-top: 5px;
        }

        .role-badge {
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            color: white;
        }

        .role-badge.bg-warning.text-dark {
            color: #212529 !important;
        }

        /* Sidebar Menu */
        .sidebar-menu {
            padding: 20px 0;
        }

        .nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin: 5px 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-item.active .nav-link {
            background: var(--secondary-color);
            color: var(--dark-color);
        }

        .nav-link i {
            width: 20px;
            font-size: 1.1rem;
        }

        .dropdown-icon {
            margin-left: auto;
            font-size: 0.8rem;
            transition: transform 0.3s;
        }

        .submenu {
            list-style: none;
            padding-left: 47px;
            margin-top: 5px;
        }

        .submenu li {
            margin: 5px 0;
        }

        .submenu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 15px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .submenu a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .submenu i {
            font-size: 0.9rem;
            width: 18px;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            flex-direction: column;
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            background: inherit;
        }

        .footer-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            white-space: nowrap;
        }

        .footer-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .footer-link.text-danger:hover {
            background: rgba(244, 67, 54, 0.2);
            color: #ff6b6b !important;
        }

        .footer-link i {
            width: 20px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 15px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .last-updated {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .last-updated i {
            margin-right: 5px;
        }

        /* Existing styles... */
        .stat-card {
            border-radius: 15px;
            padding: 25px;
            color: white;
            transition: transform 0.3s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.green {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stat-card.orange {
            background: linear-gradient(135deg, #f46b45 0%, #eea849 100%);
        }

        .stat-card.purple {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: var(--dark-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                left: -280px;
            }
            
            .sidebar.active {
                left: 0;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .sidebar-toggle-mobile {
                display: block;
            }
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Hidden logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    @auth
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')
    @endauth

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        @auth
        <!-- Top Bar -->
        <div class="top-bar">
            <h1 class="page-title">@yield('title', 'Dashboard')</h1>
            <div class="last-updated" id="lastUpdated">
                <i class="fas fa-clock"></i> Last updated: {{ now()->format('h:i A') }}
            </div>
        </div>
        @endauth

        <!-- Alerts -->
        <div class="container-fluid px-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Chatbot Component -->
    @auth
        @include('components.chatbot')
    @endauth

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Load sidebar state
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }

            // Mobile menu toggle
            const mobileToggle = document.createElement('button');
            mobileToggle.className = 'sidebar-toggle-mobile btn btn-primary d-md-none';
            mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
            mobileToggle.onclick = () => {
                document.querySelector('.sidebar').classList.toggle('active');
            };
            document.querySelector('.top-bar').prepend(mobileToggle);
        });

        // Submenu Toggle
        function toggleSubmenu(event, submenuId) {
            event.preventDefault();
            const submenu = document.getElementById(submenuId);
            const icon = event.currentTarget.querySelector('.dropdown-icon');
            
            if (submenu.style.display === 'none' || !submenu.style.display) {
                submenu.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            } else {
                submenu.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Auto-refresh dashboard stats (if on dashboard)
        @if(request()->routeIs('dashboard'))
        function refreshStats() {
            fetch('{{ route("dashboard.stats") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalMedicines').textContent = data.total_medicines;
                    document.getElementById('lowStock').textContent = data.low_stock;
                    document.getElementById('todaySales').textContent = data.today_sales;
                    document.getElementById('monthSales').textContent = data.month_sales;
                    document.getElementById('lastUpdated').innerHTML = '<i class="fas fa-clock me-1"></i> Last updated: ' + data.updated_at;
                })
                .catch(error => console.error('Error refreshing stats:', error));
        }

        // Refresh every 60 seconds
        setInterval(refreshStats, 60000);
        @endif
    </script>
    
    @stack('scripts')
</body>
</html>