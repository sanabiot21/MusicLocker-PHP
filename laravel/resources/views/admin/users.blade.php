@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">User Management</h1>
        <p class="page-description">View and manage all users</p>
    </div>

    <!-- Search and Filter -->
    <div class="admin-card">
        <div class="card-body">
            <form action="{{ route('admin.users') }}" method="GET" class="filters-form">
                <div class="form-row">
                    <div class="form-group">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search users..." class="form-control">
                    </div>
                    <div class="form-group">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Banned</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="admin-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th>Music</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->full_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                    {{ $user->status === 'active' ? 'Active' : 'Banned' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->role === 'admin' ? 'primary' : 'info' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->music_entries_count }}</td>
                            <td>{{ formatDate($user->created_at) }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                    @if($user->canBeDeleted())
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }})">
                                        Delete
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

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
