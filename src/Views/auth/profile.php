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
                                    <h2 class="mb-1"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
                                    <p class="text-muted mb-2"><?= htmlspecialchars($user['email']) ?></p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-calendar me-2"></i>
                                        <span>Member since <?= format_date($user['created_at'], 'F Y') ?></span>
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
                                <div class="stat-number text-primary"><?= number_format($userStats['total_entries'] ?? 0) ?></div>
                                <div class="stat-label">Total Songs</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item text-center p-2 rounded" style="background: rgba(138, 43, 226, 0.1);">
                                <div class="stat-number" style="color: var(--accent-purple);"><?= number_format($userStats['favorite_entries'] ?? 0) ?></div>
                                <div class="stat-label">Favorites</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item text-center p-2 rounded" style="background: rgba(254, 202, 87, 0.1);">
                                <div class="stat-number text-warning"><?= number_format($userStats['five_star_entries'] ?? 0) ?></div>
                                <div class="stat-label">5-Star Songs</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item text-center p-2 rounded" style="background: rgba(78, 205, 196, 0.1);">
                                <div class="stat-number" style="color: #4ecdc4;"><?= number_format($userStats['unique_artists'] ?? 0) ?></div>
                                <div class="stat-label">Unique Artists</div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isset($userStats['average_rating']) && $userStats['average_rating'] > 0): ?>
                    <div class="text-center mt-4 pt-4 border-top" style="border-color: #333 !important;">
                        <h6 class="text-muted mb-2">Average Song Rating</h6>
                        <div class="d-flex justify-content-center align-items-center">
                            <?php 
                            $avgRating = round($userStats['average_rating'], 1);
                            for ($i = 1; $i <= 5; $i++): 
                            ?>
                                <i class="bi bi-star<?= $i <= $avgRating ? '-fill text-warning' : ' text-muted' ?> me-1" style="font-size: 1.5rem;"></i>
                            <?php endfor; ?>
                            <span class="ms-3 h5 mb-0"><?= number_format($avgRating, 1) ?> / 5.0</span>
                        </div>
                    </div>
                    <?php endif; ?>
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
                                    <i class="bi bi-check-circle me-1"></i><?= ucfirst($user['status']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <label class="form-label text-muted small">Last Login</label>
                            <div><?= $user['last_login'] ? format_date($user['last_login'], 'M j, Y g:i A') : 'First login!' ?></div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <label class="form-label text-muted small">Member Since</label>
                            <div><?= format_date($user['created_at'], 'F j, Y') ?></div>
                        </div>
                    </div>
                    
                    <hr class="my-4" style="border-color: #333;">
                    
                    <h6 class="mb-3 text-muted">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="<?= route_url('music.add') ?>" class="btn btn-glow">
                            <i class="bi bi-plus-circle me-2"></i>Add New Song
                        </a>
                        <a href="<?= route_url('music.index') ?>" class="btn btn-outline-glow">
                            <i class="bi bi-music-note-list me-2"></i>View Collection
                        </a>
                        <a href="<?= route_url('dashboard') ?>" class="btn btn-outline-secondary">
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
            <form method="POST" action="<?= route_url('profile') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control form-control-dark" id="edit_first_name" name="first_name" 
                               value="<?= htmlspecialchars($user['first_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control form-control-dark" id="edit_last_name" name="last_name" 
                               value="<?= htmlspecialchars($user['last_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control form-control-dark" id="edit_email" name="email" 
                               value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-glow">Save Changes</button>
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
            <form method="POST" action="<?= route_url('profile') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control form-control-dark" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control form-control-dark" id="new_password" name="new_password" required>
                        <div class="form-text">Password must be at least 8 characters long</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control form-control-dark" id="confirm_new_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-glow">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for profile page -->
<?php ob_start(); ?>
<script>
    function showEditProfile() {
        const modal = new bootstrap.Modal(document.getElementById('editProfileModal'));
        modal.show();
    }

    function showChangePassword() {
        const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
        modal.show();
    }

    // Password confirmation validation
    document.getElementById('confirm_new_password').addEventListener('input', function() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = this.value;

        if (confirmPassword && newPassword !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
<?php
$additional_js = ob_get_clean();
?>