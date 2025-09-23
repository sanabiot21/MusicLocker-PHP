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
                                <a href="<?= route_url('admin.users') ?>" class="btn btn-glow">
                                    <i class="bi bi-people me-1"></i>Users
                                </a>
                                <a href="<?= route_url('admin.system') ?>" class="btn btn-outline-glow">
                                    <i class="bi bi-cpu me-1"></i>System
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

        <div class="row">
            <!-- Quick Actions -->
            <div class="col-lg-8">
                <div class="feature-card">
                    <h4 class="mb-4">Quick Actions</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="<?= route_url('admin.users') ?>" class="card bg-dark border-secondary text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-people display-1 mb-3" style="color: var(--accent-blue);"></i>
                                    <h5 class="card-title">User Management</h5>
                                    <p class="card-text text-muted">View, edit, and manage user accounts</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= route_url('admin.system') ?>" class="card bg-dark border-secondary text-decoration-none h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-cpu display-1 mb-3" style="color: var(--accent-purple);"></i>
                                    <h5 class="card-title">System Health</h5>
                                    <p class="card-text text-muted">Monitor system status and performance</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-dark border-secondary">
                                <div class="card-body text-center">
                                    <i class="bi bi-bar-chart display-1 mb-3 text-success"></i>
                                    <h5 class="card-title">Analytics</h5>
                                    <p class="card-text text-muted">View usage statistics and reports</p>
                                    <small class="text-muted">Coming Soon</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-dark border-secondary">
                                <div class="card-body text-center">
                                    <i class="bi bi-gear display-1 mb-3 text-warning"></i>
                                    <h5 class="card-title">Settings</h5>
                                    <p class="card-text text-muted">Configure application settings</p>
                                    <small class="text-muted">Coming Soon</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="col-lg-4">
                <div class="feature-card">
                    <h4 class="mb-4">Recent Activity</h4>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item bg-transparent border-secondary">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-plus me-3 text-success"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">New User Registration</div>
                                    <small class="text-muted">Charlie Brown joined</small>
                                </div>
                                <small class="text-muted">2h ago</small>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent border-secondary">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-music-note me-3" style="color: var(--accent-blue);"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Music Entry Added</div>
                                    <small class="text-muted">Alice added new song</small>
                                </div>
                                <small class="text-muted">4h ago</small>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent border-secondary">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-box-arrow-in-right me-3" style="color: var(--accent-purple);"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">User Login</div>
                                    <small class="text-muted">John Doe logged in</small>
                                </div>
                                <small class="text-muted">6h ago</small>
                            </div>
                        </div>
                        <div class="list-group-item bg-transparent border-secondary">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-heart me-3 text-danger"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">Song Favorited</div>
                                    <small class="text-muted">Jane liked a song</small>
                                </div>
                                <small class="text-muted">1d ago</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= route_url('admin.users') ?>" class="btn btn-outline-glow btn-sm">
                            View All Activity
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="text-center">
                    <a href="<?= route_url('home') ?>" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-house me-1"></i>Back to Home
                    </a>
                    <a href="<?= route_url('dashboard') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-speedometer2 me-1"></i>User Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional CSS for Admin Dashboard -->
<?php ob_start(); ?>
<style>
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
        font-family: 'Kode Mono', monospace;
    }
    
    .stat-label {
        font-size: 0.9rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-gray);
    }
    
    .card:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
    }
    
    .list-group-item {
        padding: 1rem;
    }
    
    .list-group-item:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }
    
    @media (max-width: 768px) {
        .stat-number {
            font-size: 2rem;
        }
        
        .display-1 {
            font-size: 3rem;
        }
    }
</style>
<?php 
$additional_css = ob_get_clean();
?>