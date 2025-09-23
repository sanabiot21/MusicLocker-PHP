<!-- User Detail Section -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row">
            <!-- User Profile Header -->
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
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if ($user['status'] === 'active'): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Active
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-pause-circle me-1"></i>Inactive
                                            </span>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            Member since <?= format_date($user['created_at'], 'F Y') ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group" role="group">
                                <a href="<?= route_url('admin.users') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Users
                                </a>
                                <button class="btn btn-outline-warning" onclick="editUser()">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteUser()">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="col-lg-8">
                <!-- User Statistics -->
                <div class="feature-card mb-4">
                    <h4 class="mb-4">Music Statistics</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="stat-item text-center p-3 rounded" style="background: rgba(0, 212, 255, 0.1);">
                                <div class="stat-number text-primary"><?= number_format($userStats['total_entries'] ?? 0) ?></div>
                                <div class="stat-label">Total Songs</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item text-center p-3 rounded" style="background: rgba(138, 43, 226, 0.1);">
                                <div class="stat-number" style="color: var(--accent-purple);"><?= number_format($userStats['favorite_entries'] ?? 0) ?></div>
                                <div class="stat-label">Favorites</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item text-center p-3 rounded" style="background: rgba(254, 202, 87, 0.1);">
                                <div class="stat-number text-warning"><?= number_format($userStats['five_star_entries'] ?? 0) ?></div>
                                <div class="stat-label">5-Star Songs</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-item text-center p-3 rounded" style="background: rgba(78, 205, 196, 0.1);">
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

                <!-- Recent Activity -->
                <div class="feature-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Recent Activity</h4>
                        <button class="btn btn-sm btn-outline-glow" onclick="refreshActivity()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                    
                    <?php if (!empty($recentActivity)): ?>
                        <div class="activity-timeline">
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <?php
                                        $iconClass = match($activity['type']) {
                                            'music_add' => 'bi-plus-circle text-success',
                                            'favorite' => 'bi-heart-fill text-danger',
                                            'profile_update' => 'bi-person-gear',
                                            'login' => 'bi-box-arrow-in-right',
                                            default => 'bi-circle'
                                        };
                                        ?>
                                        <i class="bi <?= $iconClass ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-header">
                                            <strong><?= htmlspecialchars($activity['action']) ?></strong>
                                            <small class="text-muted"><?= format_date($activity['timestamp'], 'M j, g:i A') ?></small>
                                        </div>
                                        <p class="activity-description mb-0">
                                            <?= htmlspecialchars($activity['description']) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clock-history display-4 mb-3"></i>
                            <p>No recent activity found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Account Information -->
                <div class="feature-card mb-4">
                    <h5 class="mb-3">Account Information</h5>
                    <div class="info-group">
                        <div class="info-item">
                            <label class="info-label">User ID</label>
                            <div class="info-value">#<?= $user['id'] ?></div>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Full Name</label>
                            <div class="info-value"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Email Address</label>
                            <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Account Status</label>
                            <div class="info-value">
                                <?php if ($user['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Inactive</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Member Since</label>
                            <div class="info-value"><?= format_date($user['created_at'], 'F j, Y') ?></div>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Last Login</label>
                            <div class="info-value">
                                <?= $user['last_login'] ? format_date($user['last_login'], 'M j, Y g:i A') : 'Never logged in' ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="feature-card mb-4">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-glow" onclick="viewUserMusic()">
                            <i class="bi bi-music-note-list me-2"></i>View Music Collection
                        </button>
                        <button class="btn btn-outline-glow" onclick="sendMessage()">
                            <i class="bi bi-envelope me-2"></i>Send Message
                        </button>
                        <button class="btn btn-outline-warning" onclick="editUser()">
                            <i class="bi bi-pencil me-2"></i>Edit Account
                        </button>
                        <?php if ($user['status'] === 'active'): ?>
                            <button class="btn btn-outline-secondary" onclick="suspendUser()">
                                <i class="bi bi-pause-circle me-2"></i>Suspend Account
                            </button>
                        <?php else: ?>
                            <button class="btn btn-outline-success" onclick="activateUser()">
                                <i class="bi bi-check-circle me-2"></i>Activate Account
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-outline-danger" onclick="deleteUser()">
                            <i class="bi bi-trash me-2"></i>Delete Account
                        </button>
                    </div>
                </div>

                <!-- System Notes -->
                <div class="feature-card">
                    <h5 class="mb-3">Admin Notes</h5>
                    <textarea class="form-control form-control-dark mb-3" rows="4" 
                              placeholder="Add admin notes about this user..."></textarea>
                    <button class="btn btn-outline-glow btn-sm w-100">
                        <i class="bi bi-save me-1"></i>Save Notes
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Edit User Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control form-control-dark" 
                           value="<?= htmlspecialchars($user['first_name']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control form-control-dark" 
                           value="<?= htmlspecialchars($user['last_name']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control form-control-dark" 
                           value="<?= htmlspecialchars($user['email']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Account Status</label>
                    <select class="form-select form-select-dark">
                        <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-glow">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for User Detail -->
<?php ob_start(); ?>
<script>
    function editUser() {
        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    }
    
    function deleteUser() {
        if (confirm('Are you sure you want to delete this user account? This action cannot be undone.')) {
            // In a real implementation, this would make an AJAX call
            alert('Delete functionality would be implemented here');
        }
    }
    
    function suspendUser() {
        if (confirm('Are you sure you want to suspend this user account?')) {
            alert('Suspend functionality would be implemented here');
        }
    }
    
    function activateUser() {
        if (confirm('Are you sure you want to activate this user account?')) {
            alert('Activate functionality would be implemented here');
        }
    }
    
    function viewUserMusic() {
        alert('This would redirect to the user\'s music collection');
    }
    
    function sendMessage() {
        alert('Message functionality would be implemented here');
    }
    
    function refreshActivity() {
        // In a real implementation, this would reload activity data
        location.reload();
    }
</script>
<?php 
$additional_js = ob_get_clean();
?>

<!-- Additional CSS -->
<?php ob_start(); ?>
<style>
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.5rem;
        font-family: 'Kode Mono', monospace;
    }
    
    .stat-label {
        font-size: 0.9rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-gray);
    }
    
    .profile-avatar {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .info-group {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
    }
    
    .info-label {
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-gray);
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-weight: 500;
    }
    
    .activity-timeline {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 8px;
        border-left: 3px solid var(--accent-blue);
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .activity-content {
        flex-grow: 1;
    }
    
    .activity-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 0.25rem;
        gap: 1rem;
    }
    
    .activity-description {
        color: var(--text-gray);
        font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
        .profile-avatar {
            width: 60px;
            height: 60px;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
        
        .activity-item {
            flex-direction: column;
            text-align: center;
        }
        
        .activity-header {
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
    }
</style>
<?php 
$additional_css = ob_get_clean();
?>