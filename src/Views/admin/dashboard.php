<!-- Admin Dashboard Section -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row">
            <!-- Dashboard Header -->
            <div class="col-12 mb-4">
                <div class="feature-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-1">
                                <i class="bi bi-shield-check me-2" style="color: var(--accent-blue);"></i>
                                Admin Dashboard
                            </h1>
                            <p class="text-muted mb-0">Manage users and monitor system health</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group" role="group">
                                <a href="/admin/users" class="btn btn-glow">
                                    <i class="bi bi-people me-1"></i>Users
                                </a>
                                <a href="/admin/system" class="btn btn-outline-glow">
                                    <i class="bi bi-cpu me-1"></i>System
                                </a>
                                <a href="/admin/settings" class="btn btn-outline-glow">
                                    <i class="bi bi-gear me-1"></i>Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-people display-4" style="color: var(--accent-blue);"></i>
                    </div>
                    <h2 class="stat-number text-primary mb-1"><?= number_format($userStats['total_users'] ?? 0) ?></h2>
                    <p class="stat-label mb-0">Total Users</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-person-check display-4" style="color: #28a745;"></i>
                    </div>
                    <h2 class="stat-number text-success mb-1"><?= number_format($userStats['active_users'] ?? 0) ?></h2>
                    <p class="stat-label mb-0">Active Users</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-person-plus display-4" style="color: var(--accent-purple);"></i>
                    </div>
                    <h2 class="stat-number" style="color: var(--accent-purple);"><?= number_format($userStats['new_users_today'] ?? 0) ?></h2>
                    <p class="stat-label mb-0">New Today</p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-card text-center">
                    <div class="mb-3">
                        <i class="bi bi-music-note-list display-4" style="color: #ffc107;"></i>
                    </div>
                    <h2 class="stat-number text-warning mb-1"><?= number_format($userStats['total_music_entries'] ?? 0) ?></h2>
                    <p class="stat-label mb-0">Music Entries</p>
                </div>
            </div>
        </div>

        <!-- Weekly Analytics Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="feature-card">
                    <h4 class="mb-4">
                        <i class="bi bi-bar-chart me-2" style="color: var(--accent-purple);"></i>
                        This Week's Activity
                    </h4>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="bi bi-person-plus-fill mb-2" style="font-size: 2rem; color: var(--accent-blue);"></i>
                                <div class="stat-number" style="font-size: 1.5rem;">+<?= $weeklyStats['new_users'] ?></div>
                                <div class="stat-label">New Users</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="bi bi-music-note-list mb-2" style="font-size: 2rem; color: var(--accent-purple);"></i>
                                <div class="stat-number" style="font-size: 1.5rem;">+<?= $weeklyStats['new_music'] ?></div>
                                <div class="stat-label">Songs Added</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="bi bi-fire mb-2" style="font-size: 2rem; color: #ff6b6b;"></i>
                                <div class="stat-number" style="font-size: 1.2rem;">
                                    <?php if ($weeklyStats['most_active']): ?>
                                        <?= htmlspecialchars($weeklyStats['most_active']['first_name']) ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </div>
                                <div class="stat-label">
                                    Most Active
                                    <?php if ($weeklyStats['most_active']): ?>
                                        <small>(<?= $weeklyStats['most_active']['song_count'] ?> songs)</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="bi bi-tag-fill mb-2" style="font-size: 2rem; color: #51cf66;"></i>
                                <div class="stat-number" style="font-size: 1.2rem;">
                                    <?php if ($weeklyStats['popular_tag']): ?>
                                        <?= htmlspecialchars($weeklyStats['popular_tag']['name']) ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </div>
                                <div class="stat-label">
                                    Popular Tag
                                    <?php if ($weeklyStats['popular_tag']): ?>
                                        <small>(<?= $weeklyStats['popular_tag']['usage_count'] ?> uses)</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Reset Requests Section -->
        <?php if (!empty($resetRequests)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="feature-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">
                            <i class="bi bi-key me-2 text-warning"></i>
                            Password Reset Requests
                            <span class="badge bg-danger ms-2"><?= count($resetRequests) ?></span>
                        </h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-person me-1"></i>User</th>
                                    <th><i class="bi bi-envelope me-1"></i>Email</th>
                                    <th><i class="bi bi-clock me-1"></i>Requested</th>
                                    <th class="text-center"><i class="bi bi-gear me-1"></i>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resetRequests as $request): ?>
                                <tr>
                                    <td><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></td>
                                    <td><?= htmlspecialchars($request['email']) ?></td>
                                    <td><?= format_time_ago($request['created_at']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-success" onclick="approveResetRequest(<?= $request['user_id'] ?>, '<?= htmlspecialchars($request['email']) ?>')">
                                            <i class="bi bi-check-circle me-1"></i>Approve
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Recent Activity -->
            <div class="col-12">
                <div class="feature-card">
                    <h4 class="mb-4">Recent Activity</h4>
                    <div class="list-group list-group-flush">
                        <?php if (!empty($recentActivity)): ?>
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="list-group-item bg-transparent border-secondary">
                                    <div class="d-flex align-items-center">
                                        <i class="bi <?= $activity['icon'] ?> me-3 <?= $activity['color'] ?>"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold"><?= htmlspecialchars($activity['title']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($activity['description']) ?></small>
                                        </div>
                                        <small class="text-muted"><?= format_time_ago($activity['timestamp']) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item bg-transparent border-secondary text-center text-muted py-4">
                                <i class="bi bi-clock-history display-4 mb-2"></i>
                                <p class="mb-0">No recent activity</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="/admin/users" class="btn btn-outline-glow btn-sm">
                            View All Users
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- Additional CSS for Admin Dashboard -->
<?php ob_start(); ?>
<style>
    .list-group-item {
        padding: 1rem;
    }

    .list-group-item:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    @media (max-width: 768px) {
        .display-1 {
            font-size: 3rem;
        }
    }
</style>
<?php
$additional_css = ob_get_clean();
?>

<!-- JavaScript for Password Reset Approval -->
<?php ob_start(); ?>
<script>
    function approveResetRequest(userId, userEmail) {
        if (!confirm('Approve password reset for ' + userEmail + '?\n\nUser will be able to set their own password.')) {
            return;
        }

        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('_token', '<?= csrf_token() ?>');

        fetch('/admin/reset-request/approve', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                MusicLocker.showToast(data.message + ' User can now visit /forgot?email=' + userEmail, 'success');
                // Reload page after delay
                setTimeout(() => location.reload(), 2000);
            } else {
                MusicLocker.showToast(data.message || 'Failed to approve request', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            MusicLocker.showToast('An error occurred', 'error');
        });
    }
</script>
<?php
$additional_js = ob_get_clean();
?>