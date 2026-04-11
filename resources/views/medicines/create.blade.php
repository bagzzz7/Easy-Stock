@extends('layouts.app')

@section('title', isset($medicine) ? 'Edit Medicine' : 'Add New Medicine')

@section('content')
<div class="medicine-form-modern">
    {{-- Hero Section --}}
    <div class="form-hero {{ isset($medicine) ? 'edit' : 'create' }}">
        <div class="form-hero-content">
            <div class="form-hero-left">
                <div class="form-badge">
                    {{ isset($medicine) ? 'Edit Medicine' : 'Create New Medicine' }}
                </div>
                <h1 class="form-title">
                    {{ isset($medicine) ? 'Edit ' . $medicine->name : 'Add New Medicine' }}
                </h1>
                <p class="form-subtitle">
                    {{ isset($medicine) ? 'Update medicine information and inventory details' : 'Enter medicine details to add to your inventory' }}
                </p>
            </div>
            <div class="form-hero-right">
                <a href="{{ route('medicines.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Medicines</span>
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
        <form action="{{ isset($medicine) ? route('medicines.update', $medicine) : route('medicines.store') }}" 
              method="POST" 
              class="modern-form">
            @csrf
            @if(isset($medicine))
                @method('PUT')
            @endif

            {{-- Basic Information Section --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="section-title">
                        <h3>Basic Information</h3>
                        <p>Core details about the medicine</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">
                            Medicine Name <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $medicine->name ?? '') }}" 
                               placeholder="e.g., Paracetamol"
                               required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="generic_name">
                            Generic Name <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="generic_name" 
                               name="generic_name" 
                               class="form-control @error('generic_name') is-invalid @enderror" 
                               value="{{ old('generic_name', $medicine->generic_name ?? '') }}" 
                               placeholder="e.g., Acetaminophen"
                               required>
                        @error('generic_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="brand">
                            Brand <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="brand" 
                               name="brand" 
                               class="form-control @error('brand') is-invalid @enderror" 
                               value="{{ old('brand', $medicine->brand ?? '') }}" 
                               placeholder="e.g., Biogesic"
                               required>
                        @error('brand')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Enter medicine description, uses, warnings, etc...">{{ old('description', $medicine->description ?? '') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Dosage Information Section --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="section-title">
                        <h3>Dosage Information</h3>
                        <p>Strength, unit, and dosage form details</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="strength">Strength</label>
                        <input type="text" 
                               id="strength" 
                               name="strength" 
                               class="form-control @error('strength') is-invalid @enderror" 
                               value="{{ old('strength', $medicine->strength ?? '') }}" 
                               placeholder="e.g., 500">
                        @error('strength')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="unit">
                            Unit <span class="required">*</span>
                        </label>
                        <select id="unit" 
                                name="unit" 
                                class="form-control @error('unit') is-invalid @enderror" 
                                required>
                            <option value="">Select Unit</option>
                            <option value="ml" {{ old('unit', $medicine->unit ?? '') == 'ml' ? 'selected' : '' }}>Milliliter (ml)</option>
                            <option value="mg" {{ old('unit', $medicine->unit ?? '') == 'mg' ? 'selected' : '' }}>Milligram (mg)</option>
                            <option value="g" {{ old('unit', $medicine->unit ?? '') == 'g' ? 'selected' : '' }}>Gram (g)</option>
                            <option value="mcg" {{ old('unit', $medicine->unit ?? '') == 'mcg' ? 'selected' : '' }}>Microgram (mcg)</option>
                            <option value="tablet" {{ old('unit', $medicine->unit ?? '') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="capsule" {{ old('unit', $medicine->unit ?? '') == 'capsule' ? 'selected' : '' }}>Capsule</option>
                            <option value="drop" {{ old('unit', $medicine->unit ?? '') == 'drop' ? 'selected' : '' }}>Drop</option>
                        </select>
                        @error('unit')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="dosage_form">
                            Dosage Form <span class="required">*</span>
                        </label>
                        <select id="dosage_form" 
                                name="dosage_form" 
                                class="form-control @error('dosage_form') is-invalid @enderror" 
                                required>
                            <option value="">Select Dosage Form</option>
                            <option value="tablet" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="capsule" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'capsule' ? 'selected' : '' }}>Capsule</option>
                            <option value="syrup" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'syrup' ? 'selected' : '' }}>Syrup</option>
                            <option value="suspension" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'suspension' ? 'selected' : '' }}>Suspension</option>
                            <option value="drops" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'drops' ? 'selected' : '' }}>Drops</option>
                            <option value="injection" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'injection' ? 'selected' : '' }}>Injection</option>
                            <option value="ointment" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'ointment' ? 'selected' : '' }}>Ointment</option>
                            <option value="cream" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'cream' ? 'selected' : '' }}>Cream</option>
                            <option value="powder" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'powder' ? 'selected' : '' }}>Powder</option>
                            <option value="inhaler" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'inhaler' ? 'selected' : '' }}>Inhaler</option>
                            <option value="patch" {{ old('dosage_form', $medicine->dosage_form ?? '') == 'patch' ? 'selected' : '' }}>Patch</option>
                        </select>
                        <small class="field-hint">Select the physical form of the medicine</small>
                        @error('dosage_form')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="category_type">Suitable For</label>
                        <select id="category_type" 
                                name="category_type" 
                                class="form-control @error('category_type') is-invalid @enderror">
                            <option value="">Select Age Group</option>
                            <option value="both" {{ old('category_type', $medicine->category_type ?? '') == 'both' ? 'selected' : '' }}>Both Adults & Children</option>
                            <option value="adults" {{ old('category_type', $medicine->category_type ?? '') == 'adults' ? 'selected' : '' }}>Adults Only</option>
                            <option value="children" {{ old('category_type', $medicine->category_type ?? '') == 'children' ? 'selected' : '' }}>Children Only</option>
                        </select>
                        @error('category_type')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Classification Section --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="section-title">
                        <h3>Classification</h3>
                        <p>Category, supplier, and prescription requirements</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="category_id">
                            Category <span class="required">*</span>
                        </label>
                        <select id="category_id" 
                                name="category_id" 
                                class="form-control @error('category_id') is-invalid @enderror" 
                                required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('category_id', $medicine->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="supplier_id">
                            Supplier <span class="required">*</span>
                        </label>
                        <select id="supplier_id" 
                                name="supplier_id" 
                                class="form-control @error('supplier_id') is-invalid @enderror" 
                                required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" 
                                    {{ old('supplier_id', $medicine->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" 
                                   name="requires_prescription" 
                                   value="1"
                                   {{ old('requires_prescription', $medicine->requires_prescription ?? false) ? 'checked' : '' }}>
                            <span class="checkbox-text">Requires Prescription (Rx)</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Pricing & Stock Section --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="section-title">
                        <h3>Pricing & Stock</h3>
                        <p>Cost, selling price, and inventory levels</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="purchase_price">
                            Purchase Price <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-prefix">₱</span>
                            <input type="number" 
                                   step="0.01" 
                                   id="purchase_price" 
                                   name="purchase_price" 
                                   class="form-control @error('purchase_price') is-invalid @enderror" 
                                   value="{{ old('purchase_price', $medicine->purchase_price ?? '') }}" 
                                   required>
                        </div>
                        @error('purchase_price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="selling_price">
                            Selling Price <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-prefix">₱</span>
                            <input type="number" 
                                   step="0.01" 
                                   id="selling_price" 
                                   name="selling_price" 
                                   class="form-control @error('selling_price') is-invalid @enderror" 
                                   value="{{ old('selling_price', $medicine->selling_price ?? '') }}" 
                                   required>
                        </div>
                        @error('selling_price')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="quantity">
                            Quantity <span class="required">*</span>
                        </label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               class="form-control @error('quantity') is-invalid @enderror" 
                               value="{{ old('quantity', $medicine->quantity ?? 0) }}" 
                               min="0"
                               required>
                        @error('quantity')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="reorder_level">
                            Reorder Level <span class="required">*</span>
                        </label>
                        <input type="number" 
                               id="reorder_level" 
                               name="reorder_level" 
                               class="form-control @error('reorder_level') is-invalid @enderror" 
                               value="{{ old('reorder_level', $medicine->reorder_level ?? 10) }}" 
                               min="1"
                               required>
                        <small class="field-hint">Alert when stock reaches this level</small>
                        @error('reorder_level')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Batch Information Section --}}
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="section-title">
                        <h3>Batch Information</h3>
                        <p>Batch tracking and storage details</p>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="batch_number">
                            Batch Number <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="batch_number" 
                               name="batch_number" 
                               class="form-control @error('batch_number') is-invalid @enderror" 
                               value="{{ old('batch_number', $medicine->batch_number ?? '') }}" 
                               placeholder="e.g., BATCH001"
                               required>
                        @error('batch_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="expiry_date">
                            Expiry Date <span class="required">*</span>
                        </label>
                        <input type="date" 
                               id="expiry_date" 
                               name="expiry_date" 
                               class="form-control @error('expiry_date') is-invalid @enderror" 
                               value="{{ old('expiry_date', isset($medicine) ? $medicine->expiry_date->format('Y-m-d') : '') }}" 
                               min="{{ now()->addDay()->format('Y-m-d') }}"
                               required>
                        @error('expiry_date')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="shelf_number">Shelf Number</label>
                        <input type="text" 
                               id="shelf_number" 
                               name="shelf_number" 
                               class="form-control @error('shelf_number') is-invalid @enderror" 
                               value="{{ old('shelf_number', $medicine->shelf_number ?? '') }}" 
                               placeholder="e.g., A-12">
                        <small class="field-hint">Storage location identifier</small>
                        @error('shelf_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ route('medicines.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </a>
                <button type="submit" class="btn-submit {{ isset($medicine) ? 'edit' : 'create' }}">
                    <i class="fas {{ isset($medicine) ? 'fa-save' : 'fa-plus-circle' }}"></i>
                    <span>{{ isset($medicine) ? 'Update Medicine' : 'Save Medicine' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Medicine Form Modern CSS */
.medicine-form-modern {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

/* Hero Section */
.form-hero {
    border-radius: 24px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.form-hero.create {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
}

.form-hero.edit {
    background: linear-gradient(135deg, #78350f 0%, #92400e 100%);
}

.form-hero::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.form-hero-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    z-index: 1;
}

.form-badge {
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

.form-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
}

.form-subtitle {
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

.form-control {
    padding: 0.625rem 0.875rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.875rem;
    transition: all 0.2s;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

.input-group {
    display: flex;
    align-items: center;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
}

.input-group:focus-within {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.input-prefix {
    padding: 0.625rem 0.875rem;
    background: #f8fafc;
    color: #64748b;
    font-weight: 600;
    border-right: 1.5px solid #e2e8f0;
}

.input-group .form-control {
    border: none;
    flex: 1;
}

.input-group .form-control:focus {
    box-shadow: none;
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

/* Checkbox */
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    margin-top: 1.5rem;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-text {
    font-size: 0.875rem;
    font-weight: 500;
    color: #334155;
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
    background: #2563eb;
}

.btn-submit.create:hover {
    background: #1d4ed8;
    transform: translateY(-2px);
}

.btn-submit.edit {
    background: #f59e0b;
}

.btn-submit.edit:hover {
    background: #d97706;
    transform: translateY(-2px);
}

/* Dosage Form Select Styling */
select#dosage_form {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.25rem;
    padding-right: 2.5rem;
    appearance: none;
}

/* Responsive */
@media (max-width: 768px) {
    .medicine-form-modern {
        padding: 1rem;
    }
    
    .form-hero-content {
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

/* Select styling */
select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.25rem;
    padding-right: 2.5rem;
}

/* Number input styling */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    opacity: 0.5;
}

input[type="number"]:hover::-webkit-inner-spin-button,
input[type="number"]:hover::-webkit-outer-spin-button {
    opacity: 1;
}
</style>
@endpush

@push('scripts')
<script>
    @if(!isset($medicine))
    // Auto-calculate suggested selling price (20% markup) for new medicines
    document.getElementById('purchase_price').addEventListener('input', function() {
        let purchasePrice = parseFloat(this.value) || 0;
        let sellingPrice = document.getElementById('selling_price');
        
        if (!sellingPrice.value && purchasePrice > 0) {
            sellingPrice.value = (purchasePrice * 1.2).toFixed(2);
        }
    });
    @endif

    // Validate expiry date is not in the past
    document.getElementById('expiry_date').addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            this.setCustomValidity('Expiry date cannot be in the past');
            this.reportValidity();
        } else {
            this.setCustomValidity('');
        }
    });

    // Show/hide fields based on dosage form
    document.getElementById('dosage_form').addEventListener('change', function() {
        const dosageForm = this.value;
        const unitField = document.getElementById('unit');
        const strengthField = document.getElementById('strength');
        
        if (dosageForm === 'tablet' || dosageForm === 'capsule') {
            if (unitField && unitField.value === '') {
                unitField.value = dosageForm;
            }
        } else if (dosageForm === 'syrup' || dosageForm === 'suspension' || dosageForm === 'drops') {
            if (unitField && unitField.value === '') {
                unitField.value = 'ml';
            }
        }
    });
</script>
@endpush