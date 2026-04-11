@extends('layouts.app')

@section('title', auth()->user()->isAdministrator() ? 'Add New User' : 'Add New Pharmacist')

@section('content')
<div class="user-create-modern">
    {{-- Hero Section --}}
    <div class="create-hero">
        <div class="create-hero-content">
            <div class="create-hero-left">
                <div class="create-badge">{{ auth()->user()->isAdministrator() ? 'User Management' : 'Pharmacist Management' }}</div>
                <h1 class="create-title">{{ auth()->user()->isAdministrator() ? 'Add New User' : 'Add New Pharmacist' }}</h1>
                <p class="create-subtitle">Create a new {{ auth()->user()->isAdministrator() ? 'user account' : 'pharmacist profile' }} with appropriate permissions</p>
            </div>
            <div class="create-hero-right">
                <a href="{{ route('users.index') }}" class="create-back-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Users</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="create-error-container">
        <div class="create-error-card">
            <div class="create-error-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="create-error-content">
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
    <div class="create-form-card">
        <form action="{{ route('users.store') }}" method="POST" class="create-modern-form">
            @csrf

            {{-- Personal Information Section --}}
            <div class="create-form-section">
                <div class="create-section-header">
                    <div class="create-section-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="create-section-title">
                        <h3>Personal Information</h3>
                        <p>Basic details about the user</p>
                    </div>
                </div>
                
                <div class="create-form-grid">
                    <div class="create-form-group full-width">
                        <label for="name">
                            Full Name <span class="create-required">*</span>
                        </label>
                        <div class="create-input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="create-form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter full name"
                                   autofocus
                                   required>
                        </div>
                        @error('name')
                            <span class="create-error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Contact Information Section --}}
            <div class="create-form-section">
                <div class="create-section-header">
                    <div class="create-section-icon">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="create-section-title">
                        <h3>Contact Information</h3>
                        <p>Email, phone, and address details</p>
                    </div>
                </div>
                
                <div class="create-form-grid">
                    <div class="create-form-group">
                        <label for="email">
                            Email Address <span class="create-required">*</span>
                        </label>
                        <div class="create-input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="create-form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" 
                                   placeholder="user@example.com"
                                   required>
                        </div>
                        @error('email')
                            <span class="create-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="create-form-group">
                        <label for="phone">Phone Number</label>
                        <div class="create-input-with-icon">
                            <i class="fas fa-phone-alt"></i>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="create-form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" 
                                   placeholder="(123) 456-7890">
                        </div>
                        @error('phone')
                            <span class="create-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="create-form-group full-width">
                        <label for="address">Address</label>
                        <div class="create-input-with-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <textarea id="address" 
                                      name="address" 
                                      class="create-form-control @error('address') is-invalid @enderror" 
                                      rows="2" 
                                      placeholder="Enter address (optional)">{{ old('address') }}</textarea>
                        </div>
                        @error('address')
                            <span class="create-error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Security Section --}}
            <div class="create-form-section">
                <div class="create-section-header">
                    <div class="create-section-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="create-section-title">
                        <h3>Security Settings</h3>
                        <p>Password and role configuration</p>
                    </div>
                </div>
                
                <div class="create-form-grid">
                    <div class="create-form-group">
                        <label for="password">
                            Password <span class="create-required">*</span>
                        </label>
                        <div class="create-input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="create-form-control @error('password') is-invalid @enderror" 
                                   placeholder="Enter password"
                                   required>
                        </div>
                        <small class="create-field-hint">Minimum 8 characters</small>
                        @error('password')
                            <span class="create-error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="create-form-group">
                        <label for="password_confirmation">
                            Confirm Password <span class="create-required">*</span>
                        </label>
                        <div class="create-input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="create-form-control" 
                                   placeholder="Confirm password"
                                   required>
                        </div>
                    </div>

                    <div class="create-form-group">
                        <label for="role">
                            Role <span class="create-required">*</span>
                        </label>
                        <div class="create-input-with-icon">
                            <i class="fas fa-briefcase"></i>
                            <select id="role" 
                                    name="role" 
                                    class="create-form-control @error('role') is-invalid @enderror" 
                                    required>
                                <option value="">Select Role</option>
                                @if(auth()->user()->isAdministrator())
                                    <option value="{{ App\Models\User::ROLE_STAFF }}" {{ old('role') == App\Models\User::ROLE_STAFF ? 'selected' : '' }}>Staff</option>
                                @endif
                                <option value="{{ App\Models\User::ROLE_PHARMACIST }}" {{ old('role') == App\Models\User::ROLE_PHARMACIST ? 'selected' : '' }}>Pharmacist</option>
                            </select>
                        </div>
                        @error('role')
                            <span class="create-error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="create-form-actions">
                <a href="{{ route('users.index') }}" class="create-btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit" class="create-btn-submit">
                    <i class="fas fa-save"></i>
                    <span>Create User</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* User Create Page Styles */
.user-create-modern {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.create-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.create-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.create-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    z-index: 1;
}

.create-badge {
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

.create-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.create-subtitle {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.create-back-btn {
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

.create-back-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateX(-4px);
}

/* Error Container */
.create-error-container {
    margin-bottom: 2rem;
}

.create-error-card {
    background: #fef2f2;
    border-left: 4px solid #ef4444;
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.create-error-icon {
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

.create-error-content h4 {
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #991b1b;
}

.create-error-content ul {
    margin: 0;
    padding-left: 1.25rem;
}

.create-error-content li {
    font-size: 0.75rem;
    color: #b91c1c;
    margin-bottom: 0.25rem;
}

.create-error-content li i {
    margin-right: 0.5rem;
    font-size: 0.65rem;
}

/* Form Card */
.create-form-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.create-modern-form {
    padding: 2rem;
}

/* Form Sections */
.create-form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.create-form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.create-section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.create-section-icon {
    width: 48px;
    height: 48px;
    background: #eff6ff;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2563eb;
    font-size: 1.25rem;
}

.create-section-title h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
}

.create-section-title p {
    margin: 0;
    font-size: 0.75rem;
    color: #64748b;
}

/* Form Grid */
.create-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

.create-form-group.full-width {
    grid-column: span 2;
}

.create-form-group {
    display: flex;
    flex-direction: column;
}

.create-form-group label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.create-required {
    color: #ef4444;
}

.create-input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.create-input-with-icon i {
    position: absolute;
    left: 0.875rem;
    color: #94a3b8;
    font-size: 0.875rem;
    pointer-events: none;
}

.create-input-with-icon input,
.create-input-with-icon select,
.create-input-with-icon textarea {
    width: 100%;
    padding: 0.75rem 0.875rem 0.75rem 2.5rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.2s;
    font-family: inherit;
}

.create-input-with-icon textarea {
    padding-top: 0.75rem;
    resize: vertical;
}

.create-input-with-icon select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-position: right 0.875rem center;
    background-repeat: no-repeat;
    background-size: 1.25rem;
    padding-right: 2.5rem;
}

.create-input-with-icon input:focus,
.create-input-with-icon select:focus,
.create-input-with-icon textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.create-input-with-icon input.is-invalid,
.create-input-with-icon select.is-invalid,
.create-input-with-icon textarea.is-invalid {
    border-color: #ef4444;
}

.create-error-message {
    font-size: 0.7rem;
    color: #ef4444;
    margin-top: 0.25rem;
}

.create-field-hint {
    font-size: 0.65rem;
    color: #94a3b8;
    margin-top: 0.25rem;
}

/* Form Actions */
.create-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}

.create-btn-cancel {
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

.create-btn-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
}

.create-btn-submit {
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
    background: #2563eb;
}

.create-btn-submit:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .user-create-modern {
        padding: 1rem;
    }
    
    .create-hero-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .create-form-grid {
        grid-template-columns: 1fr;
    }
    
    .create-form-group.full-width {
        grid-column: span 1;
    }
    
    .create-modern-form {
        padding: 1.5rem;
    }
    
    .create-form-actions {
        flex-direction: column;
    }
    
    .create-btn-cancel,
    .create-btn-submit {
        justify-content: center;
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