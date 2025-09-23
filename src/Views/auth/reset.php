<!-- Reset Password Form Section -->
<section class="py-5" style="margin-top: 100px;">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <div class="feature-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock display-4 mb-3" 
                           style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        <h1 class="h2 mb-2">Reset Password</h1>
                        <p class="text-muted">Enter your new password below</p>
                    </div>

                    <form id="resetForm" method="POST" action="<?= route_url('reset') ?>" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= htmlspecialchars($reset_token) ?>">
                        
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

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label form-label-dark">
                                <i class="bi bi-lock me-1"></i>New Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-dark <?= isset($flash_messages['validation_errors']['password']) ? 'is-invalid' : '' ?>" 
                                       id="password" name="password" 
                                       placeholder="Enter your new password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                        style="border-color: #333; color: var(--text-gray);">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <?php if (isset($flash_messages['validation_errors']['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($flash_messages['validation_errors']['password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!isset($flash_messages['validation_errors']['password'])): ?>
                                <div class="invalid-feedback">
                                    Password must be at least 8 characters long.
                                </div>
                            <?php endif; ?>
                            <div class="form-text text-muted small mt-2">
                                <i class="bi bi-info-circle me-1"></i>Password must be at least 8 characters long
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label form-label-dark">
                                <i class="bi bi-lock-fill me-1"></i>Confirm New Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-dark <?= isset($flash_messages['validation_errors']['confirm_password']) ? 'is-invalid' : '' ?>" 
                                       id="confirm_password" name="confirm_password" 
                                       placeholder="Confirm your new password" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword"
                                        style="border-color: #333; color: var(--text-gray);">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <?php if (isset($flash_messages['validation_errors']['confirm_password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($flash_messages['validation_errors']['confirm_password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if (!isset($flash_messages['validation_errors']['confirm_password'])): ?>
                                <div class="invalid-feedback">
                                    Please confirm your new password.
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Security Notice -->
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-shield-check me-2"></i>
                            <strong>Security Note:</strong> After resetting your password, you'll be automatically logged out from all devices for security reasons.
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-glow w-100 py-3 mb-3">
                            <i class="bi bi-shield-check me-2"></i>Reset Password
                        </button>

                        <!-- Back to Login -->
                        <div class="text-center">
                            <a href="<?= route_url('login') ?>" class="text-decoration-none" 
                               style="color: var(--accent-blue);">
                                <i class="bi bi-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </form>

                    <!-- Security Tips -->
                    <div class="text-center mt-4 pt-4 border-top" style="border-color: #333 !important;">
                        <div class="text-muted small">
                            <p class="mb-2"><strong>Password Security Tips:</strong></p>
                            <p class="mb-1">• Use at least 8 characters with mixed case letters</p>
                            <p class="mb-1">• Include numbers and special characters</p>
                            <p class="mb-1">• Avoid using personal information</p>
                            <p class="mb-3">• Don't reuse passwords from other sites</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for reset password form -->
<?php ob_start(); ?>
<script>
    // Password visibility toggles
    function setupPasswordToggle(passwordId, toggleId) {
        document.getElementById(toggleId).addEventListener('click', function() {
            const password = document.getElementById(passwordId);
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    }

    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('confirm_password', 'toggleConfirmPassword');

    // Form validation
    document.getElementById('resetForm').addEventListener('submit', function(event) {
        const form = this;
        let isValid = true;

        // Get form values
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        // Validate password
        if (!password || password.length < 8) {
            document.getElementById('password').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('password').classList.remove('is-invalid');
        }

        // Validate password confirmation
        if (!confirmPassword || password !== confirmPassword) {
            document.getElementById('confirm_password').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('confirm_password').classList.remove('is-invalid');
        }

        if (isValid) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="bi bi-circle-notch spin me-2"></i>Resetting Password...';
            submitBtn.disabled = true;
            
            // Allow form to submit naturally
        } else {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    });

    // Real-time validation feedback
    ['password', 'confirm_password'].forEach(function(fieldId) {
        document.getElementById(fieldId).addEventListener('input', function() {
            this.classList.remove('is-invalid');
            
            // Special handling for password confirmation
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (confirmPassword && password !== confirmPassword) {
                document.getElementById('confirm_password').classList.add('is-invalid');
            } else if (confirmPassword && password === confirmPassword) {
                document.getElementById('confirm_password').classList.remove('is-invalid');
            }
        });
    });
</script>
<?php 
$additional_js = ob_get_clean();
?>