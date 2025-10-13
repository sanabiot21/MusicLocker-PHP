<!-- User Management Section -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row">
            <!-- Page Header -->
            <div class="col-12 mb-4">
                <div class="feature-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-1">
                                <i class="bi bi-people me-2" style="color: var(--accent-blue);"></i>
                                User Management
                            </h1>
                            <p class="text-muted mb-0">Manage user accounts and view user activity</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="btn-group" role="group">
                                <a href="/admin" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Dashboard
                                </a>
                                <a href="/admin/system" class="btn btn-outline-glow">
                                    <i class="bi bi-cpu me-1"></i>System
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Controls -->
            <div class="col-12 mb-4">
                <div class="feature-card">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="userSearch" class="form-control form-control-dark" 
                                       placeholder="Search users by name or email...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select id="statusFilter" class="form-select form-select-dark">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="sortBy" class="form-select form-select-dark">
                                <option value="created_desc">Newest First</option>
                                <option value="created_asc">Oldest First</option>
                                <option value="name_asc">Name A-Z</option>
                                <option value="name_desc">Name Z-A</option>
                                <option value="login_desc">Last Login</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="col-12 mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="feature-card text-center">
                            <i class="bi bi-people display-4 mb-2" style="color: var(--accent-blue);"></i>
                            <div class="stat-number"><?= count($users) ?></div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="feature-card text-center">
                            <i class="bi bi-person-check display-4 mb-2 text-success"></i>
                            <div class="stat-number text-success"><?= count(array_filter($users, fn($u) => $u['status'] === 'active')) ?></div>
                            <div class="stat-label">Active</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="feature-card text-center">
                            <i class="bi bi-person-x display-4 mb-2 text-warning"></i>
                            <div class="stat-number text-warning"><?= count(array_filter($users, fn($u) => $u['status'] === 'inactive')) ?></div>
                            <div class="stat-label">Inactive</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="feature-card text-center">
                            <i class="bi bi-music-note-list display-4 mb-2" style="color: var(--accent-purple);"></i>
                            <div class="stat-number" style="color: var(--accent-purple);"><?= array_sum(array_column($users, 'music_entries_count')) ?></div>
                            <div class="stat-label">Total Songs</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="col-12">
                <div class="feature-card">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        <i class="bi bi-person me-1"></i>User
                                    </th>
                                    <th scope="col">
                                        <i class="bi bi-envelope me-1"></i>Email
                                    </th>
                                    <th scope="col">
                                        <i class="bi bi-circle-fill me-1"></i>Status
                                    </th>
                                    <th scope="col">
                                        <i class="bi bi-music-note-list me-1"></i>Songs
                                    </th>
                                    <th scope="col">
                                        <i class="bi bi-calendar me-1"></i>Joined
                                    </th>
                                    <th scope="col">
                                        <i class="bi bi-clock me-1"></i>Last Login
                                    </th>
                                    <th scope="col" class="text-center">
                                        <i class="bi bi-gear me-1"></i>Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr data-user-status="<?= htmlspecialchars($user['status']) ?>" 
                                            data-user-name="<?= htmlspecialchars(strtolower($user['first_name'] . ' ' . $user['last_name'])) ?>"
                                            data-user-email="<?= htmlspecialchars(strtolower($user['email'])) ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-3">
                                                        <i class="bi bi-person-circle fs-2" style="color: var(--accent-blue);"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">
                                                            <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                                        </div>
                                                        <small class="text-muted">ID: <?= $user['id'] ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted"><?= htmlspecialchars($user['email']) ?></span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-warning' ?>" id="status-badge-<?= $user['id'] ?>">
                                                    <i class="bi <?= $user['status'] === 'active' ? 'bi-check-circle' : 'bi-pause-circle' ?> me-1"></i>
                                                    <span class="status-text"><?= ucfirst($user['status']) ?></span>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge" style="background: rgba(138, 43, 226, 0.2); color: var(--accent-purple);">
                                                    <?= number_format($user['music_entries_count']) ?> songs
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= format_date($user['created_at'], 'M j, Y') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $user['last_login'] ? format_date($user['last_login'], 'M j, g:i A') : 'Never' ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="/admin/users/<?= $user['id'] ?>"
                                                       class="btn btn-outline-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button class="btn btn-outline-warning" title="Toggle Status"
                                                            onclick="toggleStatus(<?= $user['id'] ?>)">
                                                        <i class="bi bi-toggle-on"></i>
                                                    </button>
                                                    <?php if ($user['id'] !== 1): ?>
                                                    <button class="btn btn-outline-danger" title="Delete User"
                                                            onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="bi bi-people display-4 mb-3"></i>
                                            <p>No users found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Showing <?= count($users) ?> users
                        </div>
                        <nav aria-label="User pagination">
                            <ul class="pagination pagination-dark mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-danger">Delete User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="deleteUserId">
                <p>Are you sure you want to delete the user <strong id="deleteUserName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    This action cannot be undone. All user data including music entries will be permanently deleted.
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteUser()">Delete User</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for User Management -->
<?php ob_start(); ?>
<script>
    // Search functionality
    document.getElementById('userSearch').addEventListener('input', function() {
        filterUsers();
    });
    
    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        filterUsers();
    });
    
    // Sort functionality
    document.getElementById('sortBy').addEventListener('change', function() {
        sortUsers();
    });
    
    function filterUsers() {
        const searchTerm = document.getElementById('userSearch').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const rows = document.querySelectorAll('#usersTable tbody tr');
        
        rows.forEach(function(row) {
            if (row.querySelector('td[colspan]')) return; // Skip "no users found" row
            
            const userName = row.dataset.userName || '';
            const userEmail = row.dataset.userEmail || '';
            const userStatus = row.dataset.userStatus || '';
            
            const matchesSearch = userName.includes(searchTerm) || userEmail.includes(searchTerm);
            const matchesStatus = !statusFilter || userStatus === statusFilter;
            
            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function sortUsers() {
        const sortBy = document.getElementById('sortBy').value;
        const tbody = document.querySelector('#usersTable tbody');
        const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]'));
        
        rows.sort(function(a, b) {
            switch(sortBy) {
                case 'name_asc':
                    return (a.dataset.userName || '').localeCompare(b.dataset.userName || '');
                case 'name_desc':
                    return (b.dataset.userName || '').localeCompare(a.dataset.userName || '');
                case 'created_asc':
                case 'created_desc':
                case 'login_desc':
                default:
                    // For demo purposes, we'll just reverse the order
                    return sortBy.includes('desc') ? 
                        b.querySelector('td:first-child small').textContent.localeCompare(a.querySelector('td:first-child small').textContent) :
                        a.querySelector('td:first-child small').textContent.localeCompare(b.querySelector('td:first-child small').textContent);
            }
        });
        
        // Reorder rows in DOM
        rows.forEach(row => tbody.appendChild(row));
    }
    
    function toggleStatus(userId) {
        if (!confirm('Are you sure you want to toggle this user\'s status?')) {
            return;
        }

        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('_token', '<?= csrf_token() ?>');

        fetch('/admin/user/toggle-status', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update badge
                const badge = document.getElementById('status-badge-' + userId);
                if (data.newStatus === 'active') {
                    badge.className = 'badge bg-success';
                    badge.innerHTML = '<i class="bi bi-check-circle me-1"></i><span class="status-text">Active</span>';
                } else {
                    badge.className = 'badge bg-warning';
                    badge.innerHTML = '<i class="bi bi-pause-circle me-1"></i><span class="status-text">Inactive</span>';
                }

                MusicLocker.showToast(data.message, 'success');
            } else {
                MusicLocker.showToast(data.message || 'Failed to toggle status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            MusicLocker.showToast('An error occurred', 'error');
        });
    }

    function deleteUser(userId, userName) {
        document.getElementById('deleteUserName').textContent = userName;
        document.getElementById('deleteUserId').value = userId;
        const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
        modal.show();
    }

    function confirmDeleteUser() {
        const userId = document.getElementById('deleteUserId').value;
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('_token', '<?= csrf_token() ?>');

        fetch('/admin/user/delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                MusicLocker.showToast(data.message, 'success');
                // Remove row from table
                const row = document.querySelector(`tr[data-user-id="${userId}"]`);
                if (row) row.remove();

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('deleteUserModal')).hide();

                // Reload page after a delay
                setTimeout(() => location.reload(), 1500);
            } else {
                MusicLocker.showToast(data.message || 'Failed to delete user', 'error');
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

<!-- Additional CSS -->
<?php ob_start(); ?>
<style>
    .table-dark {
        --bs-table-bg: transparent;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }

    .pagination-dark .page-link {
        background-color: #2a2a2a;
        border-color: #444;
        color: #fff;
    }

    .pagination-dark .page-link:hover {
        background-color: #3a3a3a;
        border-color: var(--accent-blue);
        color: var(--accent-blue);
    }

    .pagination-dark .page-item.active .page-link {
        background-color: var(--accent-blue);
        border-color: var(--accent-blue);
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .btn-group-sm .btn {
            padding: 0.125rem 0.25rem;
        }
    }
</style>
<?php
$additional_css = ob_get_clean();
?>