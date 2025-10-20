@extends('layouts.app')

@section('title', 'User Detail - ' . $user->full_name)

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $user->full_name }}</h1>
            <p class="page-description">User ID: {{ $user->id }}</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary">Back to Users</a>
    </div>

    <div class="admin-grid">
        <!-- User Information -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">User Information</h2>
            </div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Role:</span>
                        <span class="badge badge-{{ $user->role === 'admin' ? 'primary' : 'info' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Joined:</span>
                        <span class="info-value">{{ formatDate($user->created_at) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Login:</span>
                        <span class="info-value">{{ $user->last_login ? formatDateTime($user->last_login) : 'Never' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="admin-card">
            <div class="card-header">
                <h2 class="card-title">Statistics</h2>
            </div>
            <div class="card-body">
                <div class="stats-list">
                    <div class="stat-item">
                        <div class="stat-value">{{ $stats['total_entries'] }}</div>
                        <div class="stat-label">Music Entries</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $user->playlists_count }}</div>
                        <div class="stat-label">Playlists</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $stats['favorite_entries'] }}</div>
                        <div class="stat-label">Favorites</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $stats['average_rating'] }}</div>
                        <div class="stat-label">Avg Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">Admin Actions</h2>
        </div>
        <div class="card-body">
            <div class="action-buttons">
                <a href="{{ route('admin.users.music', $user->id) }}" class="btn btn-primary">
                    View Music Collection
                </a>

                @if($user->reset_token)
                <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="password" name="new_password" placeholder="New Password" required style="margin-right: 8px;">
                    <input type="password" name="new_password_confirmation" placeholder="Confirm Password" required style="margin-right: 8px;">
                    <button type="submit" class="btn btn-warning">Reset Password</button>
                </form>
                @endif

                @if($user->canBeDeleted())
                <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }})">
                    Delete User
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? All their data will be permanently deleted.')) {
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
            window.location.href = '/admin/users';
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
