<aside class="sidebar" id="sidebar">
    <!-- Sidebar Header with Logo -->
    <div class="sidebar-header">
        <div class="logo-wrapper">
            <div class="logo-icon">
                <img src="{{ asset('favicon.ico') }}" alt="EasyStock" style="width: 28px; height: 28px;">
            </div>
            <div class="logo-text">
                <span class="logo-name">EasyStock</span>
                <span class="logo-tagline">Pharmacy Management</span>
            </div>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>

    <!-- User Profile Section -->
    <div class="user-profile">
        <div class="user-avatar">
            <div class="avatar-initials">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="user-status online"></div>
        </div>
        <div class="user-info">
            <div class="user-name">{{ Auth::user()->name }}</div>
            <div class="user-role">
                @if(Auth::user()->isAdministrator())
                    <span class="role-badge admin">
                        <i class="fas fa-crown"></i> Administrator
                    </span>
                @elseif(Auth::user()->isStaff())
                    <span class="role-badge staff">
                        <i class="fas fa-user"></i> Staff
                    </span>
                @elseif(Auth::user()->isPharmacist())
                    <span class="role-badge pharmacist">
                        <i class="fas fa-prescription-bottle"></i> Pharmacist
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="sidebar-nav">
        <!-- Dashboard -->
        <ul class="nav-list">
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <div class="nav-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="nav-text">Dashboard</span>
                    @if(request()->routeIs('dashboard'))
                        <span class="nav-active-indicator"></span>
                    @endif
                </a>
            </li>
        </ul>

        <!-- Inventory Section -->
        <ul class="nav-list">
            <li class="nav-item {{ request()->routeIs('medicines.*') ? 'active' : '' }}">
                <a href="{{ route('medicines.index') }}" class="nav-link">
                    <div class="nav-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <span class="nav-text">Medicines</span>
                    @if(request()->routeIs('medicines.*'))
                        <span class="nav-active-indicator"></span>
                    @endif
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                <a href="{{ route('inventory.index') }}" class="nav-link">
                    <div class="nav-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <span class="nav-text">Inventory</span>
                    @if(request()->routeIs('inventory.*'))
                        <span class="nav-active-indicator"></span>
                    @endif
                </a>
            </li>
        </ul>

        <!-- Sales & Suppliers -->
        <ul class="nav-list">
            <li class="nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <a href="{{ route('sales.index') }}" class="nav-link">
                    <div class="nav-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span class="nav-text">Sales</span>
                    @if(request()->routeIs('sales.*'))
                        <span class="nav-active-indicator"></span>
                    @endif
                </a>
            </li>
            <!-- Suppliers - Accessible by Administrators, Staff, AND Pharmacists -->
            @if(auth()->user()->isAdministrator() || auth()->user()->isStaff() || auth()->user()->isPharmacist())
            <li class="nav-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <a href="{{ route('suppliers.index') }}" class="nav-link">
                    <div class="nav-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <span class="nav-text">Suppliers</span>
                    @if(request()->routeIs('suppliers.*'))
                        <span class="nav-active-indicator"></span>
                    @endif
                </a>
            </li>
            @endif
        </ul>

        <!-- Reports Section - Administrators, Staff, AND Pharmacists -->
        @if(auth()->user()->isAdministrator() || auth()->user()->isStaff() || auth()->user()->isPharmacist())
        <ul class="nav-list">
            <li class="nav-item dropdown {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <a href="#" class="nav-link" onclick="toggleSubmenu(event, 'reportsSubmenu')">
                    <div class="nav-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <span class="nav-text">Reports</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                    @if(request()->routeIs('reports.*'))
                        <span class="nav-active-indicator"></span>
                    @endif
                </a>
                <ul class="submenu" id="reportsSubmenu" style="display: {{ request()->routeIs('reports.*') ? 'block' : 'none' }};">
                    <li>
                        <a href="{{ route('reports.sales') }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Sales Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.stock-alert') }}">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Stock Alert</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.expiry') }}">
                            <i class="fas fa-clock"></i>
                            <span>Expiry Report</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        @endif

        <!-- User/Pharmacist Management - Administrators and Staff only -->
        @if(auth()->user()->isAdministrator() || auth()->user()->isStaff())
        <ul class="nav-list">
            <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a href="{{ route('users.index') }}" class="nav-link">
                    <div class="nav-icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <span class="nav-text">
                        {{ auth()->user()->isAdministrator() ? 'User Management' : 'Pharmacist Management' }}
                    </span>
                    @if(request()->routeIs('users.*'))
                        <span class="nav-active-indicator"></span>
                    @endif
                </a>
            </li>
        </ul>
        @endif
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="footer-divider"></div>
        <div class="footer-links">
            <a href="{{ route('profile.show') }}" class="footer-link">
                <div class="footer-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <span class="footer-text">Profile Settings</span>
            </a>
            <a href="#" class="footer-link logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <div class="footer-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <span class="footer-text">Logout</span>
            </a>
        </div>
    </div>
</aside>

<style>
/* Modern Sidebar CSS */
.sidebar {
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
    width: 280px;
    background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    color: #e2e8f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

/* Scrollbar Styling */
.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Collapsed State */
.sidebar.collapsed {
    width: 80px;
}

.sidebar.collapsed .logo-text,
.sidebar.collapsed .logo-tagline,
.sidebar.collapsed .user-info,
.sidebar.collapsed .nav-text,
.sidebar.collapsed .dropdown-icon,
.sidebar.collapsed .submenu,
.sidebar.collapsed .nav-section-title,
.sidebar.collapsed .footer-text,
.sidebar.collapsed .footer-divider {
    display: none;
}

.sidebar.collapsed .nav-icon {
    margin: 0;
}

.sidebar.collapsed .nav-link {
    justify-content: center;
    padding: 0.875rem;
}

.sidebar.collapsed .nav-link .nav-icon i {
    font-size: 1.25rem;
}

.sidebar.collapsed .user-profile {
    justify-content: center;
    padding: 1.5rem 0;
}

.sidebar.collapsed .avatar-initials {
    width: 40px;
    height: 40px;
    font-size: 1rem;
}

.sidebar.collapsed .sidebar-toggle i {
    transform: rotate(180deg);
}

.sidebar.collapsed .footer-links {
    padding: 1rem 0;
}

/* Header */
.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.logo-icon {
    width: 36px;
    height: 36px;
    /* REMOVED background color */
    background: transparent;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-icon img {
    width: 28px;
    height: 28px;
    object-fit: contain;
}

.logo-text {
    line-height: 1.2;
}

.logo-name {
    display: block;
    font-size: 1rem;
    font-weight: 700;
    color: white;
    letter-spacing: 0.5px;
}

.logo-tagline {
    display: block;
    font-size: 0.65rem;
    color: rgba(255, 255, 255, 0.5);
    font-weight: 400;
}

.sidebar-toggle {
    background: rgba(255, 255, 255, 0.05);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    color: rgba(255, 255, 255, 0.7);
}

.sidebar-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.sidebar-toggle i {
    font-size: 0.875rem;
    transition: transform 0.3s;
}

/* User Profile */
.user-profile {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.user-avatar {
    position: relative;
}

.avatar-initials {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    color: white;
    text-transform: uppercase;
}

.user-status {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #0f172a;
}

.user-status.online {
    background: #10b981;
}

.user-status.away {
    background: #f59e0b;
}

.user-status.offline {
    background: #6b7280;
}

.user-info {
    flex: 1;
}

.user-name {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
    margin-bottom: 0.25rem;
}

.user-role {
    display: block;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.65rem;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.1);
}

.role-badge i {
    font-size: 0.6rem;
}

.role-badge.admin {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.role-badge.staff {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.role-badge.pharmacist {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

/* Navigation */
.sidebar-nav {
    flex: 1;
    padding: 0.5rem 0;
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin: 0.125rem 0;
    position: relative;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.7rem 1.5rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.2s;
    position: relative;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.05);
    color: white;
}

.nav-item.active .nav-link {
    background: rgba(59, 130, 246, 0.15);
    color: white;
}

.nav-item.active .nav-link::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    border-radius: 0 3px 3px 0;
}

.nav-icon {
    width: 24px;
    text-align: center;
}

.nav-icon i {
    font-size: 1rem;
    transition: all 0.2s;
}

.nav-text {
    flex: 1;
    font-size: 0.875rem;
    font-weight: 500;
}

.dropdown-icon {
    font-size: 0.7rem;
    transition: transform 0.2s;
}

.nav-active-indicator {
    width: 6px;
    height: 6px;
    background: #3b82f6;
    border-radius: 50%;
}

/* Submenu */
.submenu {
    list-style: none;
    padding-left: 3rem;
    margin: 0.25rem 0 0.5rem 0;
}

.submenu li {
    margin: 0.25rem 0;
}

.submenu a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    font-size: 0.8rem;
    transition: all 0.2s;
}

.submenu a:hover {
    color: white;
    transform: translateX(4px);
}

.submenu a i {
    font-size: 0.75rem;
    width: 20px;
}

/* Footer */
.sidebar-footer {
    margin-top: auto;
    padding: 1rem 0;
}

.footer-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.08);
    margin: 0 1.5rem 1rem 1.5rem;
}

.footer-links {
    padding: 0 1rem;
}

.footer-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0.5rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.2s;
    margin-bottom: 0.25rem;
}

.footer-link:hover {
    background: rgba(255, 255, 255, 0.05);
    color: white;
}

.footer-link.logout:hover {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}

.footer-icon {
    width: 24px;
    text-align: center;
}

.footer-icon i {
    font-size: 1rem;
}

.footer-text {
    font-size: 0.875rem;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s;
    }
    
    .sidebar.open {
        transform: translateX(0);
    }
    
    .sidebar.collapsed {
        transform: translateX(-100%);
    }
}
</style>

<script>
// Sidebar Toggle Function
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
    
    // Save state to localStorage
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
    
    // Toggle main content margin
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.classList.toggle('expanded', isCollapsed);
    }
}

// Submenu Toggle
function toggleSubmenu(event, submenuId) {
    event.preventDefault();
    event.stopPropagation();
    
    const submenu = document.getElementById(submenuId);
    const icon = event.currentTarget.querySelector('.dropdown-icon');
    
    if (submenu.style.display === 'none' || !submenu.style.display) {
        submenu.style.display = 'block';
        if (icon) icon.style.transform = 'rotate(180deg)';
    } else {
        submenu.style.display = 'none';
        if (icon) icon.style.transform = 'rotate(0deg)';
    }
}

// Load sidebar state on page load
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    
    if (isCollapsed && sidebar) {
        sidebar.classList.add('collapsed');
        const mainContent = document.querySelector('.main-content');
        if (mainContent) {
            mainContent.classList.add('expanded');
        }
    }
    
    // Mobile menu toggle (if needed)
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }
});

// Close sidebar on mobile when clicking outside
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    
    if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('open')) {
        if (!sidebar.contains(event.target) && !mobileToggle?.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    }
});
</script>