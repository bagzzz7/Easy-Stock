@extends('layouts.app')

@section('title', auth()->user()->isAdministrator() ? 'User Management' : 'Pharmacist Management')

@section('content')
<div class="user-management-modern">
    {{-- Hero Section --}}
    <div class="users-hero">
        <div class="users-hero-content">
            <div class="users-hero-left">
                <div class="users-badge">{{ auth()->user()->isAdministrator() ? 'Administration' : 'Pharmacy Staff' }}</div>
                <h1 class="users-title">{{ auth()->user()->isAdministrator() ? 'User Management' : 'Pharmacist Management' }}</h1>
                <p class="users-subtitle">
                    {{ auth()->user()->isAdministrator() ? 'Manage system users, roles, and permissions' : 'Manage pharmacy staff and their access levels' }}
                </p>
            </div>
            <div class="users-hero-right">
                <div class="stats-chip">
                    <i class="fas fa-users"></i>
                    <span>{{ $users->total() }} Total {{ auth()->user()->isAdministrator() ? 'Users' : 'Pharmacists' }}</span>
                </div>
            </div>
        </div>
        <div class="users-actions">
            <a href="{{ route('users.create') }}" class="btn-modern btn-primary">
                <i class="fas fa-user-plus"></i>
                <span>{{ auth()->user()->isAdministrator() ? 'Add New User' : 'Add Pharmacist' }}</span>
            </a>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="filters-card">
        <div class="filters-header">
            <i class="fas fa-filter"></i>
            <h3>Filter Users</h3>
        </div>
        <form action="{{ route('users.index') }}" method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>Search</label>
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" 
                               name="search" 
                               placeholder="Search by name, email, or phone..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                
                @if(auth()->user()->isAdministrator())
                <div class="filter-group">
                    <label>Role</label>
                    <div class="select-input">
                        <i class="fas fa-briefcase"></i>
                        <select name="role">
                            <option value="all">All Roles</option>
                            <option value="{{ App\Models\User::ROLE_STAFF }}" {{ request('role') == App\Models\User::ROLE_STAFF ? 'selected' : '' }}>Staff</option>
                            <option value="{{ App\Models\User::ROLE_PHARMACIST }}" {{ request('role') == App\Models\User::ROLE_PHARMACIST ? 'selected' : '' }}>Pharmacist</option>
                        </select>
                    </div>
                </div>
                @endif
                
                <div class="filter-actions">
                    <button type="submit" class="btn-apply">
                        <i class="fas fa-sliders-h"></i> Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'role']) && request()->role != 'all')
                    <a href="{{ route('users.index') }}" class="btn-clear">
                        <i class="fas fa-times"></i> Clear
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="users-table-container">
        @if($users->isEmpty())
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h4>No users found</h4>
                <p>Start by adding a new {{ auth()->user()->isAdministrator() ? 'user' : 'pharmacist' }}</p>
                <a href="{{ route('users.create') }}" class="btn-modern btn-primary">
                    <i class="fas fa-user-plus"></i> Add {{ auth()->user()->isAdministrator() ? 'User' : 'Pharmacist' }}
                </a>
            </div>
        @else
            <div class="table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="user-row">
                            <td class="user-cell">
                                <div class="user-avatar-wrapper">
                                    @if($user->profile_photo_url)
                                        <img class="user-avatar-img" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                    @else
                                        <div class="user-avatar-initials">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="user-fullname">{{ $user->name }}</div>
                                    <div class="user-email-address">{{ $user->email }}</div>
                                </div>
                            </td>
                            <td class="contact-cell">
                                @if($user->phone)
                                    <div class="contact-phone-item">
                                        <i class="fas fa-phone-alt"></i>
                                        <span>{{ $user->phone }}</span>
                                    </div>
                                @endif
                                @if($user->address)
                                    <div class="contact-address-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>{{ Str::limit($user->address, 40) }}</span>
                                    </div>
                                @endif
                                @if(!$user->phone && !$user->address)
                                    <span class="no-contact-info">No contact info</span>
                                @endif
                            </td>
                            <td class="role-cell">
                                <span class="user-role-badge user-role-{{ $user->role }}">
                                    <i class="fas {{ $user->role === 'administrator' ? 'fa-crown' : ($user->role === 'pharmacist' ? 'fa-prescription-bottle' : 'fa-user') }}"></i>
                                    {{ $user->role_display_name }}
                                </span>
                            </td>
                            
                            <td class="actions-cell">
                                <div class="action-buttons-group">
                                    <a href="{{ route('users.show', $user) }}" class="action-btn view-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="action-btn edit-btn" title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        @if(!$user->sales()->exists())
                                        <button type="button" class="action-btn delete-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteUserModal{{ $user->id }}" 
                                                title="Delete User">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="pagination-container">
                <div class="pagination-info">
                    <i class="fas fa-info-circle"></i>
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                </div>
                <div class="pagination-links">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Delete Modals --}}
@foreach($users as $user)
@if($user->id !== auth()->id() && !$user->sales()->exists())
<div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header-custom">
                <div class="modal-icon-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h5 class="modal-title-custom">Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-custom">
                <p>Are you sure you want to delete user <strong>{{ $user->name }}</strong>?</p>
                <div class="warning-box">
                    <i class="fas fa-info-circle"></i>
                    <span>This action cannot be undone and will remove all user data.</span>
                </div>
                <div class="user-details-summary">
                    <div class="summary-detail">
                        <span>Name:</span>
                        <strong>{{ $user->name }}</strong>
                    </div>
                    <div class="summary-detail">
                        <span>Email:</span>
                        <strong>{{ $user->email }}</strong>
                    </div>
                    <div class="summary-detail">
                        <span>Role:</span>
                        <strong class="user-role-{{ $user->role }}">{{ $user->role_display_name }}</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer-custom">
                <button type="button" class="btn-cancel-modal" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete-modal">
                        <i class="fas fa-trash-alt"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection

@push('styles')
<style>
/* User Management Modern CSS - All scoped to prevent sidebar conflicts */
.user-management-modern {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.user-management-modern .users-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.user-management-modern .users-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.user-management-modern .users-badge {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    margin-bottom: 1rem;
    color: white;
}

.user-management-modern .users-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.user-management-modern .users-subtitle {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.user-management-modern .stats-chip {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    padding: 0.5rem 1rem;
    border-radius: 40px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: white;
}

.user-management-modern .users-actions {
    position: absolute;
    top: 2rem;
    right: 2rem;
}

.user-management-modern .btn-modern {
    padding: 0.75rem 1.5rem;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    text-decoration: none;
}

.user-management-modern .btn-primary {
    background: #10b981;
    color: white;
}

.user-management-modern .btn-primary:hover {
    background: #059669;
    transform: translateY(-2px);
}

/* Filters Card */
.user-management-modern .filters-card {
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.user-management-modern .filters-header {
    padding: 1rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-management-modern .filters-header i {
    font-size: 1rem;
    color: #2563eb;
}

.user-management-modern .filters-header h3 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f172a;
}

.user-management-modern .filters-form {
    padding: 1.5rem;
}

.user-management-modern .filters-grid {
    display: grid;
    grid-template-columns: 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}

.user-management-modern .filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.user-management-modern .filter-group label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.5px;
}

.user-management-modern .search-input,
.user-management-modern .select-input {
    position: relative;
    display: flex;
    align-items: center;
}

.user-management-modern .search-input i,
.user-management-modern .select-input i {
    position: absolute;
    left: 0.875rem;
    color: #94a3b8;
    font-size: 0.875rem;
}

.user-management-modern .search-input input,
.user-management-modern .select-input select {
    width: 100%;
    padding: 0.625rem 0.875rem 0.625rem 2.5rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.user-management-modern .search-input input:focus,
.user-management-modern .select-input select:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.user-management-modern .select-input select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-position: right 0.875rem center;
    background-repeat: no-repeat;
    background-size: 1.25rem;
    padding-right: 2.5rem;
}

.user-management-modern .filter-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.user-management-modern .btn-apply {
    padding: 0.625rem 1.25rem;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.user-management-modern .btn-apply:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

.user-management-modern .btn-clear {
    padding: 0.625rem 1.25rem;
    background: transparent;
    border: 1px solid #e2e8f0;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.user-management-modern .btn-clear:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #ef4444;
}

/* Table Container */
.user-management-modern .users-table-container {
    background: white;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.user-management-modern .table-wrapper {
    overflow-x: auto;
}

.user-management-modern .users-table {
    width: 100%;
    border-collapse: collapse;
}

.user-management-modern .users-table thead {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.user-management-modern .users-table th {
    padding: 1rem;
    text-align: left;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
}

.user-management-modern .users-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.user-management-modern .users-table tbody tr:hover {
    background: #f8fafc;
}

/* User Info - Using unique class names */
.user-management-modern .user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-management-modern .user-avatar-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    overflow: hidden;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.user-management-modern .user-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-management-modern .user-avatar-initials {
    color: white;
    font-weight: 700;
    font-size: 1rem;
    text-transform: uppercase;
}

.user-management-modern .user-fullname {
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 0.25rem;
}

.user-management-modern .user-email-address {
    font-size: 0.75rem;
    color: #64748b;
}

/* Contact Info */
.user-management-modern .contact-cell {
    padding: 1rem;
}

.user-management-modern .contact-phone-item,
.user-management-modern .contact-address-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #475569;
    margin-bottom: 0.25rem;
}

.user-management-modern .contact-phone-item i,
.user-management-modern .contact-address-item i {
    width: 14px;
    color: #94a3b8;
    font-size: 0.7rem;
}

.user-management-modern .no-contact-info {
    font-size: 0.7rem;
    color: #94a3b8;
    font-style: italic;
}

/* Role Badges */
.user-management-modern .user-role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
}

.user-management-modern .user-role-administrator {
    background: #fee2e2;
    color: #991b1b;
}

.user-management-modern .user-role-pharmacist {
    background: #dcfce7;
    color: #15803d;
}

.user-management-modern .user-role-staff {
    background: #dbeafe;
    color: #1e40af;
}

/* Status Badges */
.user-management-modern .user-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
}

.user-management-modern .user-status-active {
    background: #dcfce7;
    color: #15803d;
}

.user-management-modern .user-status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

/* Action Buttons */
.user-management-modern .action-buttons-group {
    display: flex;
    gap: 0.5rem;
}

.user-management-modern .action-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    color: #64748b;
}

.user-management-modern .view-btn:hover {
    border-color: #2563eb;
    color: #2563eb;
    background: #eff6ff;
    transform: translateY(-2px);
}

.user-management-modern .edit-btn:hover {
    border-color: #f59e0b;
    color: #f59e0b;
    background: #fffbeb;
    transform: translateY(-2px);
}

.user-management-modern .deactivate-btn:hover,
.user-management-modern .activate-btn:hover {
    border-color: #10b981;
    color: #10b981;
    background: #dcfce7;
    transform: translateY(-2px);
}

.user-management-modern .delete-btn:hover {
    border-color: #ef4444;
    color: #ef4444;
    background: #fee2e2;
    transform: translateY(-2px);
}

/* Empty State */
.user-management-modern .empty-state {
    text-align: center;
    padding: 4rem;
}

.user-management-modern .empty-state i {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

.user-management-modern .empty-state h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    color: #0f172a;
}

.user-management-modern .empty-state p {
    margin: 0 0 1.5rem 0;
    color: #64748b;
}

/* Pagination */
.user-management-modern .pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    flex-wrap: wrap;
    gap: 1rem;
}

.user-management-modern .pagination-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #64748b;
}

.user-management-modern .pagination-info i {
    color: #2563eb;
}

.user-management-modern .pagination-links .pagination {
    margin: 0;
    display: flex;
    gap: 0.25rem;
}

.user-management-modern .pagination-links .page-item .page-link {
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-size: 0.75rem;
    transition: all 0.2s;
}

.user-management-modern .pagination-links .page-item.active .page-link {
    background: #2563eb;
    border-color: #2563eb;
    color: white;
}

.user-management-modern .pagination-links .page-item .page-link:hover:not(.active) {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

/* Modal Styles */
.modal-header-custom {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modal-icon-danger {
    width: 40px;
    height: 40px;
    background: #fee2e2;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-icon-danger i {
    font-size: 1.25rem;
    color: #ef4444;
}

.modal-title-custom {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    flex: 1;
}

.modal-body-custom {
    padding: 1.5rem;
}

.warning-box {
    background: #fef3c7;
    border-left: 3px solid #f59e0b;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #92400e;
}

.user-details-summary {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1rem;
}

.summary-detail {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.875rem;
}

.summary-detail:last-child {
    border-bottom: none;
}

.modal-footer-custom {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.btn-cancel-modal {
    padding: 0.5rem 1rem;
    background: transparent;
    border: 1px solid #e2e8f0;
    border-radius: 40px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel-modal:hover {
    background: #f1f5f9;
}

.btn-delete-modal {
    padding: 0.5rem 1rem;
    background: #ef4444;
    border: none;
    border-radius: 40px;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-delete-modal:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 1024px) {
    .user-management-modern {
        padding: 1rem;
    }
    
    .user-management-modern .filters-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .user-management-modern .users-hero {
        flex-direction: column;
        gap: 1rem;
    }
    
    .user-management-modern .users-actions {
        position: static;
        margin-top: 1rem;
    }
    
    .user-management-modern .users-table {
        font-size: 0.75rem;
    }
    
    .user-management-modern .users-table td,
    .user-management-modern .users-table th {
        padding: 0.5rem;
    }
    
    .user-management-modern .user-cell {
        flex-direction: column;
        text-align: center;
    }
    
    .user-management-modern .action-buttons-group {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .user-management-modern .pagination-container {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endpush

@push('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Toggle status function
    function toggleUserStatus(userId) {
        if (confirm('Are you sure you want to change this user\'s status?')) {
            document.getElementById('toggle-status-form-' + userId).submit();
        }
    }
</script>
@endpush