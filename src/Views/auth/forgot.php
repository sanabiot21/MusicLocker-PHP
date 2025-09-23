<!-- Forgot Password Form Section -->
<section class="py-5" style="margin-top: 100px;">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <div class="feature-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-key display-4 mb-3" 
                           style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        <h1 class="h2 mb-2">Forgot Password?</h1>
                        <p class="text-muted">Enter your email address and we'll send you a link to reset your password</p>
                    </div>

                    <form id="forgotForm" method="POST" action="<?= route_url('forgot') ?>" novalidate>
                        <?= csrf_field() ?>
                        
                        <!-- Display validation errors -->
                        <?php if (isset($flash_messages['validation_errors'])): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($flash_messages['validation_errors'] as $field => $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label form-label-dark">
                                <i class="bi bi-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control form-control-dark <?= isset($flash_messages['validation_errors']['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" 
                                   value="<?= htmlspecialchars($flash_messages['old_input']['email'] ?? '') ?>"
                                   placeholder="Enter your email address" required>
                            <?php if (isset($flash_messages['validation_errors']['email'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($flash_messages['validation_errors']['email']) ?>
                                </div>
                            <?php else: ?>
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            <?php endif; ?>
                            <div class="form-text text-muted small mt-2">
                                <i class="bi bi-info-circle me-1"></i>We'll send password reset instructions to this email
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-glow w-100 py-3 mb-3">
                            <i class="bi bi-envelope me-2"></i>Send Reset Link
                        </button>

                        <!-- Back to Login -->
                        <div class="text-center">
                            <a href="<?= route_url('login') ?>" class="text-decoration-none" 
                               style="color: var(--accent-blue);">
                                <i class="bi bi-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </form>

                    <!-- Additional Help -->
                    <div class="text-center mt-4 pt-4 border-top" style="border-color: #333 !important;">
                        <div class="text-muted small">
                            <p class="mb-2"><strong>Having trouble?</strong></p>
                            <p class="mb-1">• Make sure you entered the correct email address</p>
                            <p class="mb-1">• Check your spam/junk folder for the reset email</p>
                            <p class="mb-3">• The reset link expires after 1 hour for security</p>
                            <p>
                                Don't have an account? 
                                <a href="<?= route_url('register') ?>" class="text-decoration-none" 
                                   style="color: var(--accent-blue);">Create one here</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for forgot password form -->
<?php ob_start(); ?>
<script>
    // Form validation and submission
    document.getElementById('forgotForm').addEventListener('submit', function(event) {
        const form = this;
        const email = document.getElementById('email').value.trim();

        // Client-side validation
        let isValid = true;

        if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            document.getElementById('email').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('email').classList.remove('is-invalid');
        }

        if (isValid) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="bi bi-circle-notch spin me-2"></i>Sending...';
            submitBtn.disabled = true;
            
            // Allow form to submit naturally
        } else {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });

    // Remove validation classes on input
    document.getElementById('email').addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
</script>
<?php 
$additional_js = ob_get_clean();
?>