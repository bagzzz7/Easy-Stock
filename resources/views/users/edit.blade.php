@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="user-edit-modern">
    {{-- Hero Section --}}
    <div class="edit-hero">
        <div class="edit-hero-content">
            <div class="edit-hero-left">
                <div class="edit-badge">Edit User</div>
                <h1 class="edit-title">{{ $user->name }}</h1>
                <p class="edit-subtitle">Update user information, permissions, and account status</p>
            </div>
            <div class="edit-hero-right">
                <a href="{{ route('users.index') }}" class="edit-back-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Users</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="edit-error-container">
        <div class="edit-error-card">
            <div class="edit-error-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="edit-error-content">
                <h4>Please fix the following errors:</h4>
                <ul>
                    @foreach($errors->all() as $error)
                        <li><i class="fas fa-times"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Form --}}
    <div class="edit-form-card">
        <form action="{{ route('users.update', $user) }}" method="POST" class="edit-modern-form">
            @csrf
            @method('PUT')

            {{-- Personal Information Section --}}
            <div class="edit-form-section">
                <div class="edit-section-header">
                    <div class="edit-section-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="edit-section-title">
                        <h3>Personal Information</h3>
                        <p>Basic details about the user</p>
                    </div>
                </div>
                
                <div class="edit-form-grid">
                    <div class="edit-form-group full-width">
                        <label for="name">
                            Full Name <span class="edit-required">*</span>
                        </label>
                        <div class="edit-input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="edit-form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $user->name) }}" 
                                   placeholder="Enter full name"
                                   required>
                        </div>
                        @error('name')
                            <span class="edit-error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Contact Information Section --}}
            <div class="edit-form-section">
                <div class="edit-section-header">
                    <div class="edit-section-icon">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="edit-section-title">
                        <h3>Contact Information</h3>
                        <p>Email, phone, and address details</p>
                    </div>
                </div>
                
                <div class="edit-form-grid">
                    <div class="edit-form-group">
                        <label for="email">
                            Email Address <span class="edit-required">*</span>
                        </label>
                        <div class="edit-input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="edit-form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" 
                                   placeholder="user@example.com"
                                   required>
                        </div>
                        @error('email')
                            <span class="edit-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="edit-form-group">
                        <label for="phone">Phone Number</label>
                        <div class="edit-input-with-icon">
                            <i class="fas fa-phone-alt"></i>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="edit-form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $user->phone) }}" 
                                   placeholder="(123) 456-7890">
                        </div>
                        @error('phone')
                            <span class="edit-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="edit-form-group full-width">
                        <label for="address">Address</label>
                        <div class="edit-input-with-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <textarea id="address" 
                                      name="address" 
                                      class="edit-form-control @error('address') is-invalid @enderror" 
                                      rows="2" 
                                      placeholder="Enter address (optional)">{{ old('address', $user->address) }}</textarea>
                        </div>
                        @error('address')
                            <span class="edit-error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Security Settings Section --}}
            <div class="edit-form-section">
                <div class="edit-section-header">
                    <div class="edit-section-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="edit-section-title">
                        <h3>Security Settings</h3>
                        <p>Password and role configuration</p>
                    </div>
                </div>
                
                <div class="edit-form-grid">
                    <div class="edit-form-group">
                        <label for="password">New Password</label>
                        <div class="edit-input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="edit-form-control @error('password') is-invalid @enderror" 
                                   placeholder="Leave blank to keep current">
                        </div>
                        <small class="edit-field-hint">Leave blank to keep current password</small>
                        @error('password')
                            <span class="edit-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="edit-form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <div class="edit-input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="edit-form-control" 
                                   placeholder="Confirm new password">
                        </div>
                    </div>

                    <div class="edit-form-group">
                        <label for="role">
                            Role <span class="edit-required">*</span>
                        </label>
                        <div class="edit-input-with-icon">
                            <i class="fas fa-briefcase"></i>
                            <select id="role" 
                                    name="role" 
                                    class="edit-form-control @error('role') is-invalid @enderror" 
                                    required>
                                <option value="">Select Role</option>
                                @if(auth()->user()->isAdministrator())
                                    <option value="{{ App\Models\User::ROLE_STAFF }}" {{ old('role', $user->role) == App\Models\User::ROLE_STAFF ? 'selected' : '' }}>Staff</option>
                                @endif
                                <option value="{{ App\Models\User::ROLE_PHARMACIST }}" {{ old('role', $user->role) == App\Models\User::ROLE_PHARMACIST ? 'selected' : '' }}>Pharmacist</option>
                            </select>
                        </div>
                        @error('role')
                            <span class="edit-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="edit-form-group">
                        <label class="edit-toggle-label">Account Status</label>
                        <div class="edit-toggle-switch">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active" 
                                   value="1"
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label for="is_active">
                                <span class="edit-toggle-slider"></span>
                                <span class="edit-toggle-text">Active Account</span>
                            </label>
                        </div>
                        <small class="edit-field-hint">Inactive users cannot log in</small>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="edit-form-actions">
                <a href="{{ route('users.index') }}" class="edit-btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit" class="edit-btn-submit">
                    <i class="fas fa-save"></i>
                    <span>Update User</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* User Edit Page Styles */
.user-edit-modern {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.edit-hero {
    background: linear-gradient(135deg, #78350f 0%, #92400e 100%);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.edit-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.edit-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    z-index: 1;
}

.edit-badge {
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

.edit-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.edit-subtitle {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.edit-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 40px;
    color: white;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.edit-back-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateX(-4px);
}

/* Error Container */
.edit-error-container {
    margin-bottom: 2rem;
}

.edit-error-card {
    background: #fef2f2;
    border-left: 4px solid #ef4444;
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.edit-error-icon {
    width: 40px;
    height: 40px;
    background: #fee2e2;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ef4444;
    font-size: 1.25rem;
}

.edit-error-content h4 {
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #991b1b;
}

.edit-error-content ul {
    margin: 0;
    padding-left: 1.25rem;
}

.edit-error-content li {
    font-size: 0.75rem;
    color: #b91c1c;
    margin-bottom: 0.25rem;
}

.edit-error-content li i {
    margin-right: 0.5rem;
    font-size: 0.65rem;
}

/* Form Card */
.edit-form-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.edit-modern-form {
    padding: 2rem;
}

/* Form Sections */
.edit-form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.edit-form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.edit-section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.edit-section-icon {
    width: 48px;
    height: 48px;
    background: #fef3c7;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f59e0b;
    font-size: 1.25rem;
}

.edit-section-title h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
}

.edit-section-title p {
    margin: 0;
    font-size: 0.75rem;
    color: #64748b;
}

/* Form Grid */
.edit-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

.edit-form-group.full-width {
    grid-column: span 2;
}

.edit-form-group {
    display: flex;
    flex-direction: column;
}

.edit-form-group label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.edit-required {
    color: #ef4444;
}

.edit-input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.edit-input-with-icon i {
    position: absolute;
    left: 0.875rem;
    color: #94a3b8;
    font-size: 0.875rem;
    pointer-events: none;
}

.edit-input-with-icon input,
.edit-input-with-icon select,
.edit-input-with-icon textarea {
    width: 100%;
    padding: 0.75rem 0.875rem 0.75rem 2.5rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.2s;
    font-family: inherit;
}

.edit-input-with-icon textarea {
    padding-top: 0.75rem;
    resize: vertical;
}

.edit-input-with-icon select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-position: right 0.875rem center;
    background-repeat: no-repeat;
    background-size: 1.25rem;
    padding-right: 2.5rem;
}

.edit-input-with-icon input:focus,
.edit-input-with-icon select:focus,
.edit-input-with-icon textarea:focus {
    outline: none;
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.edit-input-with-icon input.is-invalid,
.edit-input-with-icon select.is-invalid,
.edit-input-with-icon textarea.is-invalid {
    border-color: #ef4444;
}

.edit-error-message {
    font-size: 0.7rem;
    color: #ef4444;
    margin-top: 0.25rem;
}

.edit-field-hint {
    font-size: 0.65rem;
    color: #94a3b8;
    margin-top: 0.25rem;
}

/* Toggle Switch */
.edit-toggle-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.edit-toggle-switch {
    position: relative;
    display: inline-block;
    width: 100%;
}

.edit-toggle-switch input {
    display: none;
}

.edit-toggle-switch label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
}

.edit-toggle-slider {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
    background-color: #cbd5e1;
    border-radius: 34px;
    transition: all 0.3s;
}

.edit-toggle-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    borderRadius: 50%;
    transition: transform 0.3s;
}

.edit-toggle-switch input:checked + label .edit-toggle-slider {
    background-color: #f59e0b;
}

.edit-toggle-switch input:checked + label .edit-toggle-slider:before {
    transform: translateX(24px);
}

.edit-toggle-text {
    font-size: 0.875rem;
    font-weight: 500;
    color: #334155;
}

.edit-toggle-switch input:checked + label .edit-toggle-text {
    color: #f59e0b;
}

/* Form Actions */
.edit-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}

.edit-btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 40px;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.edit-btn-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
}

.edit-btn-submit {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.5rem;
    border: none;
    border-radius: 40px;
    font-weight: 600;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s;
    color: white;
    background: #f59e0b;
}

.edit-btn-submit:hover {
    background: #d97706;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .user-edit-modern {
        padding: 1rem;
    }
    
    .edit-hero-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .edit-form-grid {
        grid-template-columns: 1fr;
    }
    
    .edit-form-group.full-width {
        grid-column: span 1;
    }
    
    .edit-modern-form {
        padding: 1.5rem;
    }
    
    .edit-form-actions {
        flex-direction: column;
    }
    
    .edit-btn-cancel,
    .edit-btn-submit {
        justify-content: center;
    }
    
    .edit-toggle-switch label {
        justify-content: space-between;
    }
}
</style>
@endpush

@push('scripts')
<script>
    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }
</script>
@endpush