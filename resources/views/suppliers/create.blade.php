@extends('layouts.app')

@section('title', 'Add New Supplier')

@section('content')
<div class="supplier-modern">
    {{-- Hero Section --}}
    <div class="supplier-hero create">
        <div class="supplier-hero-content">
            <div class="supplier-hero-left">
                <div class="supplier-badge">Supplier Management</div>
                <h1 class="supplier-title">Add New Supplier</h1>
                <p class="supplier-subtitle">Register a new supplier to your pharmacy network</p>
            </div>
            <div class="supplier-hero-right">
                <a href="{{ route('suppliers.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Suppliers</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="error-content">
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
    <div class="form-card">
        <form action="{{ route('suppliers.store') }}" method="POST" class="modern-form">
            @csrf

            {{-- Company Information Section --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="section-title">
                        <h3>Company Information</h3>
                        <p>Basic details about the supplier company</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="name">
                            Company Name <span class="required">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-store"></i>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter company name"
                                   autofocus
                                   required>
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Contact Information Section --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="section-title">
                        <h3>Contact Information</h3>
                        <p>Email, phone, and address details</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="email">
                            Email Address <span class="required">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" 
                                   placeholder="supplier@example.com"
                                   required>
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">
                            Phone Number <span class="required">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone-alt"></i>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone') }}" 
                                   placeholder="(123) 456-7890"
                                   required>
                        </div>
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="address">
                            Address <span class="required">*</span>
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <textarea id="address" 
                                      name="address" 
                                      class="form-control @error('address') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Enter complete address"
                                      required>{{ old('address') }}</textarea>
                        </div>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Additional Information Section --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="section-title">
                        <h3>Additional Information</h3>
                        <p>Contact person and license details</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="contact_person">Contact Person</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" 
                                   id="contact_person" 
                                   name="contact_person" 
                                   class="form-control @error('contact_person') is-invalid @enderror" 
                                   value="{{ old('contact_person') }}" 
                                   placeholder="Enter contact person name">
                        </div>
                        <small class="field-hint">Leave blank if same as company contact</small>
                        @error('contact_person')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="license_number">License Number</label>
                        <div class="input-with-icon">
                            <i class="fas fa-id-card"></i>
                            <input type="text" 
                                   id="license_number" 
                                   name="license_number" 
                                   class="form-control @error('license_number') is-invalid @enderror" 
                                   value="{{ old('license_number') }}" 
                                   placeholder="e.g., LIC-2024-001">
                        </div>
                        <small class="field-hint">Optional: Supplier's business license number</small>
                        @error('license_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ route('suppliers.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit" class="btn-submit create">
                    <i class="fas fa-save"></i>
                    <span>Save Supplier</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Supplier Form Modern CSS */
.supplier-modern {
    max-width: 1000px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.supplier-hero {
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.supplier-hero.create {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
}

.supplier-hero.edit {
    background: linear-gradient(135deg, #78350f 0%, #92400e 100%);
}

.supplier-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.supplier-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    z-index: 1;
}

.supplier-badge {
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

.supplier-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.supplier-subtitle {
    color: rgba(255, 255, 255, 0.7);
    margin: 0;
}

.btn-back {
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

.btn-back:hover {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    transform: translateX(-4px);
}

/* Error Container */
.error-container {
    margin-bottom: 2rem;
}

.error-card {
    background: #fef2f2;
    border-left: 4px solid #ef4444;
    border-radius: 16px;
    padding: 1rem;
    display: flex;
    gap: 1rem;
    align-items: flex-start;
}

.error-icon {
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

.error-content h4 {
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #991b1b;
}

.error-content ul {
    margin: 0;
    padding-left: 1.25rem;
}

.error-content li {
    font-size: 0.75rem;
    color: #b91c1c;
    margin-bottom: 0.25rem;
}

.error-content li i {
    margin-right: 0.5rem;
    font-size: 0.65rem;
}

/* Form Card */
.form-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.modern-form {
    padding: 2rem;
}

/* Form Sections */
.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.section-icon {
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

.section-title h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
}

.section-title p {
    margin: 0;
    font-size: 0.75rem;
    color: #64748b;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

.form-group.full-width {
    grid-column: span 2;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #334155;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.required {
    color: #ef4444;
}

.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.input-with-icon i {
    position: absolute;
    left: 0.875rem;
    color: #94a3b8;
    font-size: 0.875rem;
    pointer-events: none;
}

.input-with-icon input,
.input-with-icon textarea {
    width: 100%;
    padding: 0.75rem 0.875rem 0.75rem 2.5rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.2s;
    font-family: inherit;
}

.input-with-icon textarea {
    padding-top: 0.75rem;
    resize: vertical;
}

.input-with-icon input:focus,
.input-with-icon textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.input-with-icon input.is-invalid,
.input-with-icon textarea.is-invalid {
    border-color: #ef4444;
}

.error-message {
    font-size: 0.7rem;
    color: #ef4444;
    margin-top: 0.25rem;
}

.field-hint {
    font-size: 0.65rem;
    color: #94a3b8;
    margin-top: 0.25rem;
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}

.btn-cancel {
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

.btn-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
}

.btn-submit {
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
}

.btn-submit.create {
    background: #10b981;
}

.btn-submit.create:hover {
    background: #059669;
    transform: translateY(-2px);
}

.btn-submit.edit {
    background: #f59e0b;
}

.btn-submit.edit:hover {
    background: #d97706;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .supplier-modern {
        padding: 1rem;
    }
    
    .supplier-hero-content {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-group.full-width {
        grid-column: span 1;
    }
    
    .modern-form {
        padding: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-cancel,
    .btn-submit {
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