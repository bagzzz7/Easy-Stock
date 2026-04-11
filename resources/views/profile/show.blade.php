@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid px-4">
    <!-- Header with Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold">
                    <i class="fas fa-user-circle text-primary me-2"></i>
                    Profile Settings
                </h2>
                <a href="{{ route('profile.edit') }}" class="btn btn-primary px-4">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Main Profile Card -->
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card border-0 shadow-lg overflow-hidden">
                <!-- Profile Header with Cover Image -->
                <div class="profile-cover" style="background: linear-gradient(135deg, #2A5C7D 0%, #1E4560 100%); height: 120px;"></div>
                
                <div class="card-body px-4 pb-4" style="margin-top: -60px;">
                    <!-- Profile Photo Section -->
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="position-relative d-inline-block">
                                <img src="{{ $user->profile_photo_url }}" 
                                     alt="{{ $user->name }}" 
                                     class="rounded-circle border-4 border-white shadow"
                                     width="120" 
                                     height="120"
                                     style="object-fit: cover; border: 4px solid white;">
                                
                                <!-- Upload Photo Button -->
                                <button type="button" 
                                        class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 shadow"
                                        style="width: 36px; height: 36px;"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#uploadPhotoModal"
                                        title="Change Photo">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-9 mt-4 mt-md-0">
                            <div class="d-flex flex-column h-100 justify-content-center">
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <h3 class="fw-bold mb-0">{{ $user->name }}</h3>
                                    <span class="badge {{ $user->role_badge_class }} px-3 py-2">
                                        <i class="fas {{ $user->role == 'administrator' ? 'fa-crown' : ($user->role == 'staff' ? 'fa-user-tie' : 'fa-user-md') }} me-1"></i>
                                        {{ $user->role_display_name }}
                                    </span>
                                </div>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-envelope text-primary me-2"></i>{{ $user->email }}
                                </p>
                                @if($user->phone)
                                <p class="text-muted mb-0">
                                    <i class="fas fa-phone text-success me-2"></i>{{ $user->phone }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-4">

                    <!-- Personal Information Section -->
                    <div class="row">
                        <div class="col-12 mb-4">
                            <h5 class="fw-semibold mb-3">
                                <i class="fas fa-id-card text-primary me-2"></i>
                                Personal Information
                            </h5>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <small class="text-muted text-uppercase small fw-semibold tracking-wide">Full Name</small>
                                <p class="mb-0 fw-semibold fs-6">{{ $user->name }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <small class="text-muted text-uppercase small fw-semibold tracking-wide">Email Address</small>
                                <p class="mb-0 fw-semibold fs-6">{{ $user->email }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <small class="text-muted text-uppercase small fw-semibold tracking-wide">Phone Number</small>
                                <p class="mb-0 fw-semibold fs-6">{{ $user->phone ?? '—' }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <small class="text-muted text-uppercase small fw-semibold tracking-wide">Address</small>
                                <p class="mb-0 fw-semibold fs-6">{{ $user->address ?? '—' }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <small class="text-muted text-uppercase small fw-semibold tracking-wide">Member Since</small>
                                <p class="mb-0 fw-semibold fs-6">{{ $user->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="info-card p-3 bg-light rounded-3 h-100">
                                <small class="text-muted text-uppercase small fw-semibold tracking-wide">Last Login</small>
                                <p class="mb-0 fw-semibold fs-6">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('M d, Y h:i A') }}
                                    @else
                                        First login
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <hr class="my-4">

                    <!-- Quick Actions Section -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h5 class="fw-semibold mb-3">
                                <i class="fas fa-bolt text-primary me-2"></i>
                                Quick Actions
                            </h5>
                        </div>
                        
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-edit me-2"></i>
                                <span>Edit Profile</span>
                            </a>
                        </div>
                        
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('profile.change-password') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-key me-2"></i>
                                <span>Change Password</span>
                            </a>
                        </div>
                        
                        <div class="col-md-4 mb-2">
                            <a href="{{ route('sales.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-shopping-cart me-2"></i>
                                <span>My Sales</span>
                            </a>
                        </div>
                    </div>

                  
<!-- Upload Photo Modal (Improved) -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>
                    Update Profile Photo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Current Photo Preview -->
                <div class="text-center mb-4">
                    <div class="preview-circle mx-auto">
                        <img id="modalPhotoPreview" 
                             src="{{ $user->profile_photo_url }}" 
                             alt="Preview" 
                             class="rounded-circle border-3 shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover; border: 3px solid var(--primary-color);">
                    </div>
                </div>

                <form action="{{ route('profile.upload-photo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- File Upload Area -->
                    <div class="upload-area mb-3 p-4 text-center border rounded-3 bg-light">
                        <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                        <p class="mb-1 fw-semibold">Click to upload or drag and drop</p>
                        <small class="text-muted">JPEG, PNG, JPG, GIF (Max 2MB)</small>
                        <input type="file" 
                               class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" 
                               id="modalPhoto" 
                               name="photo" 
                               accept="image/jpeg,image/png,image/jpg,image/gif"
                               required>
                    </div>

                    <!-- Upload Tips -->
                    <div class="alert alert-light border small p-2 mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <small>For best results, use a square image (1:1 ratio).</small>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        @if($user->profile_photo)
                        <button type="button" 
                                class="btn btn-outline-danger"
                                onclick="deletePhoto()">
                            <i class="fas fa-trash me-1"></i>Remove
                        </button>
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-1"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Photo Form -->
@if($user->profile_photo)
<form id="delete-photo-form" action="{{ route('profile.delete-photo') }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endif

@push('styles')
<style>
    .tracking-wide {
        letter-spacing: 0.025em;
    }
    
    .info-card {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }
    
    .info-card:hover {
        border-left-color: var(--primary-color);
        background-color: #f8f9fa !important;
    }
    
    .upload-area {
        position: relative;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .upload-area:hover {
        border-color: var(--primary-color) !important;
        background-color: #e9ecef !important;
    }
    
    .upload-area input {
        cursor: pointer;
    }
    
    .preview-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto;
    }
    
    .border-4 {
        border-width: 4px !important;
    }
    
    .btn-outline-primary, .btn-outline-warning, .btn-outline-info {
        border-width: 2px;
        font-weight: 500;
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .profile-cover {
            height: 80px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Image preview in modal
    document.getElementById('modalPhoto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size exceeds 2MB limit. Please choose a smaller file.');
                this.value = '';
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Invalid file type. Please upload JPEG, PNG, JPG, or GIF.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('modalPhotoPreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    // Delete photo function
    function deletePhoto() {
        if (confirm('Are you sure you want to remove your profile photo?')) {
            document.getElementById('delete-photo-form').submit();
        }
    }
</script>
@endpush
@endsection