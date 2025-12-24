@extends('layouts.app')

@section('title', 'Account Recovery Request')

@section('content')
<!-- Account Recovery Section -->
<section class="py-5" style="margin-top: 100px;">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="feature-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-envelope-paper display-4 mb-3"
                           style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        <h1 class="h2 mb-2">Account Recovery Request</h1>
                        <p class="text-muted">Send a message to the administrator to request account recovery.</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4">
                            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show mb-4">
                            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('auth.account-recovery.submit') }}" id="recoveryForm">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label form-label-dark">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <input type="email"
                                   class="form-control form-control-dark @error('email') is-invalid @enderror"
                                   id="email" name="email"
                                   value="{{ old('email', $email ?? '') }}"
                                   placeholder="Enter your email address" required>
                            @error('email')
                                <div class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <label for="message" class="form-label form-label-dark">
                                <i class="bi bi-chat-left-text me-1"></i>Message
                            </label>
                            <textarea class="form-control form-control-dark @error('message') is-invalid @enderror"
                                      id="message" name="message" rows="6"
                                      placeholder="Please explain why you believe your account should be restored. Include any relevant details or context."
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @else
                                <div class="invalid-feedback">
                                    Please provide a message (minimum 10 characters).
                                </div>
                                <div class="form-text text-muted">
                                    Minimum 10 characters required. Be specific about why you believe your account should be restored.
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-glow w-100 py-3 mb-3">
                            <i class="bi bi-send me-2"></i>Send Recovery Request
                        </button>
                    </form>

                    <!-- Back Links -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none small"
                           style="color: var(--accent-blue);">
                            <i class="bi bi-arrow-left me-1"></i>Back to Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Form validation
    document.getElementById('recoveryForm').addEventListener('submit', function(event) {
        const form = this;
        const email = document.getElementById('email').value;
        const message = document.getElementById('message').value;

        let isValid = true;

        if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            document.getElementById('email').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('email').classList.remove('is-invalid');
        }

        if (!message || message.trim().length < 10) {
            document.getElementById('message').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('message').classList.remove('is-invalid');
        }

        if (isValid) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-circle-notch spin me-2"></i>Sending...';
            submitBtn.disabled = true;
        } else {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });

    // Remove validation classes on input
    ['email', 'message'].forEach(function(fieldId) {
        document.getElementById(fieldId).addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });
</script>
@endpush




