@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-key text-warning me-2"></i>
                    Change Password
                </h2>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Profile
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-lock me-2"></i>
                        Update Your Password
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('POST')

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-bold">
                                Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Enter your current password"
                                       required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-bold">
                                New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" 
                                       name="new_password" 
                                       placeholder="Enter new password"
                                       required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Password Requirements -->
                            <div class="mt-2 small">
                                <div class="password-requirement" id="length-check">
                                    <i class="fas fa-times-circle text-danger me-1"></i> At least 8 characters
                                </div>
                                <div class="password-requirement" id="uppercase-check">
                                    <i class="fas fa-times-circle text-danger me-1"></i> One uppercase letter
                                </div>
                                <div class="password-requirement" id="lowercase-check">
                                    <i class="fas fa-times-circle text-danger me-1"></i> One lowercase letter
                                </div>
                                <div class="password-requirement" id="number-check">
                                    <i class="fas fa-times-circle text-danger me-1"></i> One number
                                </div>
                                <div class="password-requirement" id="special-check">
                                    <i class="fas fa-times-circle text-danger me-1"></i> One special character
                                </div>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label fw-bold">
                                Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       placeholder="Confirm new password"
                                       required>
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="password-match" class="small mt-1"></div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Password Tips:</strong>
                            <ul class="mb-0 mt-1">
                                <li>Use a strong password that you don't use elsewhere</li>
                                <li>Mix uppercase, lowercase, numbers, and special characters</li>
                                <li>Avoid common words or personal information</li>
                            </ul>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Password strength checker
    const password = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    const matchIndicator = document.getElementById('password-match');

    password.addEventListener('input', checkPasswordStrength);
    confirmPassword.addEventListener('input', checkPasswordMatch);

    function checkPasswordStrength() {
        const value = password.value;
        
        // Length check
        const lengthCheck = document.getElementById('length-check');
        if (value.length >= 8) {
            lengthCheck.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> At least 8 characters';
            lengthCheck.style.color = '#10b981';
        } else {
            lengthCheck.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i> At least 8 characters';
            lengthCheck.style.color = '#ef4444';
        }

        // Uppercase check
        const uppercaseCheck = document.getElementById('uppercase-check');
        if (/[A-Z]/.test(value)) {
            uppercaseCheck.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> One uppercase letter';
            uppercaseCheck.style.color = '#10b981';
        } else {
            uppercaseCheck.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i> One uppercase letter';
            uppercaseCheck.style.color = '#ef4444';
        }

        // Lowercase check
        const lowercaseCheck = document.getElementById('lowercase-check');
        if (/[a-z]/.test(value)) {
            lowercaseCheck.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> One lowercase letter';
            lowercaseCheck.style.color = '#10b981';
        } else {
            lowercaseCheck.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i> One lowercase letter';
            lowercaseCheck.style.color = '#ef4444';
        }

        // Number check
        const numberCheck = document.getElementById('number-check');
        if (/[0-9]/.test(value)) {
            numberCheck.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> One number';
            numberCheck.style.color = '#10b981';
        } else {
            numberCheck.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i> One number';
            numberCheck.style.color = '#ef4444';
        }

        // Special character check
        const specialCheck = document.getElementById('special-check');
        if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
            specialCheck.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> One special character';
            specialCheck.style.color = '#10b981';
        } else {
            specialCheck.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i> One special character';
            specialCheck.style.color = '#ef4444';
        }
    }

    function checkPasswordMatch() {
        if (confirmPassword.value === '') {
            matchIndicator.innerHTML = '';
        } else if (password.value === confirmPassword.value) {
            matchIndicator.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> Passwords match';
            matchIndicator.style.color = '#10b981';
        } else {
            matchIndicator.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i> Passwords do not match';
            matchIndicator.style.color = '#ef4444';
        }
    }
</script>
@endpush