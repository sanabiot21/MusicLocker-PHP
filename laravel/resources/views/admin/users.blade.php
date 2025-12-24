@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<section class="py-5" style="margin-top: 80px;">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-people me-2" style="color: var(--accent-blue);"></i>
                User Management
            </h1>
            <p class="page-description">View and manage all users</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Users -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-blue">
                <div class="mb-3">
                    <i class="bi bi-people display-4" style="color: var(--accent-blue);"></i>
                </div>
                <h2 class="stat-value stat-value-blue mb-1">{{ formatNumber($stats['total_users']) }}</h2>
                <p class="stat-label mb-0">Total Users</p>
            </div>
        </div>

        <!-- Active Users -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-green">
                <div class="mb-3">
                    <i class="bi bi-person-check display-4" style="color: #28a745;"></i>
                </div>
                <h2 class="stat-value stat-value-green mb-1">{{ formatNumber($stats['active_users']) }}</h2>
                <p class="stat-label mb-0">Active Users</p>
            </div>
        </div>

        <!-- Suspended Users -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-yellow">
                <div class="mb-3">
                    <i class="bi bi-person-x display-4" style="color: #feca57;"></i>
                </div>
                <h2 class="stat-value stat-value-yellow mb-1">{{ formatNumber($stats['inactive_users']) }}</h2>
                <p class="stat-label mb-0">Suspended Users</p>
            </div>
        </div>

        <!-- Total Songs -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-purple">
                <div class="mb-3">
                    <i class="bi bi-music-note-list display-4" style="color: var(--accent-purple);"></i>
                </div>
                <h2 class="stat-value stat-value-purple mb-1">{{ formatNumber($stats['total_songs']) }}</h2>
                <p class="stat-label mb-0">Total Songs</p>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="feature-card mb-4">
        <form action="{{ route('admin.users') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text" style="background: var(--card-bg); border-color: #333; color: var(--accent-blue);">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or email..." class="form-control form-control-dark">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select form-control-dark">
                        <option value="">All Status</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive (legacy)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="btn-group-admin">
                        <button type="submit" class="btn btn-glow">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-glow">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="feature-card">
        <div class="table-responsive">
            <table class="admin-table-enhanced">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Songs</th>
                        <th>Joined</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar-icon">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $user->full_name }}</div>
                                    <small class="text-muted">#{{ $user->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge badge-with-icon bg-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                                <i class="bi bi-{{ $user->status === 'active' ? 'check-circle' : 'pause-circle' }}"></i>
                                {{ $user->status === 'active' ? 'Active' : 'Suspended' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-with-icon bg-{{ $user->role === 'admin' ? 'primary' : 'info' }}">
                                <i class="bi bi-{{ $user->role === 'admin' ? 'shield-check' : 'person' }}"></i>
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-songs">
                                <i class="bi bi-music-note-list me-1"></i>{{ $user->music_entries_count }}
                            </span>
                        </td>
                        <td>{{ formatDate($user->created_at) }}</td>
                        <td>{{ $user->last_login ? formatDate($user->last_login) : 'Never' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($user->canBeDeleted())
                                <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }})" title="Delete User">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No users found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="divider"></div>
        <div class="d-flex justify-content-center py-3">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
</section>

@push('scripts')
<script>
function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }

    fetch(`/admin/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            MusicLocker.showToast(data.message || 'Error deleting user', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        MusicLocker.showToast('Error deleting user', 'danger');
    });
}
</script>
@endpush
@endsection
