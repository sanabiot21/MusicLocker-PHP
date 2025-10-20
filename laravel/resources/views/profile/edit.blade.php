@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<!-- Profile Section -->
<section class="py-5" style="margin-top: 80px; margin-bottom: 5rem;">
    <div class="container">
        <div class="row">
            <!-- Profile Header -->
            <div class="col-12 mb-4">
                <div class="feature-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="profile-avatar me-4">
                                    <i class="bi bi-person-circle display-1" style="color: var(--accent-blue);"></i>
                                </div>
                                <div>
                                    <h2 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h2>
                                    <p class="text-muted mb-2">{{ $user->email }}</p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-calendar me-2"></i>
                                        <span>Member since {{ $user->created_at->format('F Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-glow" onclick="showEditProfile()">
                                    <i class="bi bi-pencil me-1"></i>Edit Profile
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="showChangePassword()">
                                    <i class="bi bi-key me-1"></i>Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="col-lg-8">
                <!-- Profile Statistics -->
                <div class="feature-card mb-4">
                    <h4 class="mb-4">Your Music Statistics</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="stat-item text-center p-2 rounded" style="background: rgba(0, 212, 255, 0.1);">
                                <div class="stat-number text-primary">{{ number_format($userStats['total_entries'] ?? 0) }}</div>
                                <div class="stat-label">Total Songs</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item text-center p-2 rounded" style="background: rgba(138, 43, 226, 0.1);">
                                <div class="stat-number" style="color: var(--accent-purple);">{{ number_format($userStats['favorite_entries'] ?? 0) }}</div>
                                <div class="stat-label">Favorites</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item text-center p-2 rounded" style="background: rgba(254, 202, 87, 0.1);">
                                <div class="stat-number text-warning">{{ number_format($userStats['five_star_entries'] ?? 0) }}</div>
                                <div class="stat-label">5-Star Songs</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item text-center p-2 rounded" style="background: rgba(78, 205, 196, 0.1);">
                                <div class="stat-number" style="color: #4ecdc4;">{{ number_format($userStats['unique_artists'] ?? 0) }}</div>
                                <div class="stat-label">Unique Artists</div>
                            </div>
                        </div>
                    </div>

                    @if(isset($userStats['average_rating']) && $userStats['average_rating'] > 0)
                    <div class="text-center mt-4 pt-4 border-top" style="border-color: #333 !important;">
                        <h6 class="text-muted mb-2">Average Song Rating</h6>
                        <div class="d-flex justify-content-center align-items-center">
                            @php
                                $avgRating = round($userStats['average_rating'], 1);
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $avgRating ? '-fill text-warning' : ' text-muted' }} me-1" style="font-size: 1.5rem;"></i>
                            @endfor
                            <span class="ms-3 h5 mb-0">{{ number_format($avgRating, 1) }} / 5.0</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Unified Account Status Card -->
                <div class="feature-card">
                    <h5 class="mb-4">Account Status</h5>

                    <div class="info-group">
                        <div class="info-item mb-3">
                            <label class="form-label text-muted small">Status</label>
                            <div>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>{{ ucfirst($user->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="info-item mb-3">
                            <label class="form-label text-muted small">Last Login</label>
                            <div>{{ $user->last_login ? $user->last_login->format('M j, Y g:i A') : 'First login!' }}</div>
                        </div>

                        <div class="info-item mb-3">
                            <label class="form-label text-muted small">Member Since</label>
                            <div>{{ $user->created_at->format('F j, Y') }}</div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: #333;">

                    <h6 class="mb-3 text-muted">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('music.create') }}" class="btn btn-glow">
                            <i class="bi bi-plus-circle me-2"></i>Add New Song
                        </a>
                        <a href="{{ route('music.index') }}" class="btn btn-outline-glow">
                            <i class="bi bi-music-note-list me-2"></i>View Collection
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" id="editProfileForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control form-control-dark @error('first_name') is-invalid @enderror"
                               id="edit_first_name" name="first_name"
                               value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control form-control-dark @error('last_name') is-invalid @enderror"
                               id="edit_last_name" name="last_name"
                               value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control form-control-dark @error('email') is-invalid @enderror"
                               id="edit_email" name="email"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-glow" id="editProfileBtn">
                        <span class="btn-text">Save Changes</span>
                        <span class="btn-spinner spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.password.update') }}" id="changePasswordForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control form-control-dark @error('current_password') is-invalid @enderror"
                               id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control form-control-dark @error('password') is-invalid @enderror"
                               id="password" name="password" required minlength="8">
                        <div class="form-text">Password must be at least 8 characters long</div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control form-control-dark"
                               id="password_confirmation" name="password_confirmation" required minlength="8">
                        <div class="invalid-feedback">Passwords do not match</div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-glow" id="changePasswordBtn">
                        <span class="btn-text">Change Password</span>
                        <span class="btn-spinner spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Modal initialization
    let editProfileModal = null;
    let changePasswordModal = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        const editModalEl = document.getElementById('editProfileModal');
        const passwordModalEl = document.getElementById('changePasswordModal');

        if (editModalEl) {
            editProfileModal = new bootstrap.Modal(editModalEl);
        }
        if (passwordModalEl) {
            changePasswordModal = new bootstrap.Modal(passwordModalEl);
        }

        // Password confirmation validation
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');

        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                const newPassword = passwordInput.value;
                const confirmPassword = this.value;

                if (confirmPassword && newPassword !== confirmPassword) {
                    this.setCustomValidity('Passwords do not match');
                    this.classList.add('is-invalid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                }
            });
        }

        // Form submission handlers with loading states
        const editProfileForm = document.getElementById('editProfileForm');
        if (editProfileForm) {
            editProfileForm.addEventListener('submit', function(e) {
                const btn = document.getElementById('editProfileBtn');
                const btnText = btn.querySelector('.btn-text');
                const btnSpinner = btn.querySelector('.btn-spinner');

                btn.disabled = true;
                btnText.classList.add('d-none');
                btnSpinner.classList.remove('d-none');
            });
        }

        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
                const confirmPassword = document.getElementById('password_confirmation');
                const newPassword = document.getElementById('password');

                // Final validation check
                if (confirmPassword.value !== newPassword.value) {
                    e.preventDefault();
                    confirmPassword.setCustomValidity('Passwords do not match');
                    confirmPassword.classList.add('is-invalid');
                    return false;
                }

                const btn = document.getElementById('changePasswordBtn');
                const btnText = btn.querySelector('.btn-text');
                const btnSpinner = btn.querySelector('.btn-spinner');

                btn.disabled = true;
                btnText.classList.add('d-none');
                btnSpinner.classList.remove('d-none');
            });
        }

        // Show modals if there are validation errors
        @if($errors->has('first_name') || $errors->has('last_name') || $errors->has('email'))
            if (editProfileModal) {
                editProfileModal.show();
            }
        @endif

        @if($errors->has('current_password') || $errors->has('password'))
            if (changePasswordModal) {
                changePasswordModal.show();
            }
        @endif

        // Show success toast if present
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
    });

    function showEditProfile() {
        if (editProfileModal) {
            editProfileModal.show();
        }
    }

    function showChangePassword() {
        if (changePasswordModal) {
            changePasswordModal.show();
        }
    }

    function showToast(message, type = 'success') {
        // Create toast element
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        // Get or create toast container
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        // Add toast to container
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        // Show toast
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 3000 });
        toast.show();

        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }
</script>
@endpush

@push('styles')
<style>
    .stat-item {
        padding: 2rem 1.5rem;
        border-radius: 12px;
        transition: transform 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-2px);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    .info-group .info-item {
        padding-bottom: 0.75rem;
    }

    .info-group .info-item:last-child {
        padding-bottom: 0;
    }

    .form-control-dark {
        background: var(--input-bg, #2a2a3e);
        border: 1px solid var(--border-color, #2d2d3d);
        color: var(--text-primary, #ffffff);
    }

    .form-control-dark:focus {
        background: var(--input-bg, #2a2a3e);
        border-color: var(--accent-blue);
        color: var(--text-primary, #ffffff);
        box-shadow: 0 0 0 0.25rem rgba(0, 212, 255, 0.25);
    }

    .modal-content.bg-dark {
        background: var(--card-bg, #1e1e2e) !important;
        border: 1px solid var(--border-color, #2d2d3d);
    }

    .btn-close-white {
        filter: brightness(0) invert(1);
    }

    .profile-avatar {
        font-size: 4rem;
    }

    @media (max-width: 768px) {
        .profile-avatar {
            font-size: 3rem;
        }

        .btn-group {
            width: 100%;
            margin-top: 1rem;
        }

        .btn-group .btn {
            flex: 1;
        }
    }
</style>
@endpush
