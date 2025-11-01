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
                        <span class="badge badge-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                            {{ $user->status === 'active' ? 'Active' : 'Banned' }}
                        </span>
                    </div>
                    @if($user->status !== 'active' && $user->ban_reason)
                    <div class="info-item">
                        <span class="info-label">Ban Reason:</span>
                        <span class="info-value">{{ $user->ban_reason }}</span>
                    </div>
                    @endif
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

    <!-- Admin Notes -->
    <div class="admin-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="bi bi-sticky me-2"></i>Admin Notes
            </h2>
        </div>
        <div class="card-body">
            <!-- Note Input Form -->
            <form id="noteForm" class="mb-4">
                <div class="input-group">
                    <textarea class="form-control form-control-dark" id="noteInput" name="note"
                              placeholder="Add a note about this user..." rows="3" maxlength="500"></textarea>
                </div>
                <small class="form-text text-muted mt-2">Max 500 characters</small>
                <button type="submit" class="btn btn-primary mt-2">
                    <i class="bi bi-plus-circle me-1"></i>Add Note
                </button>
            </form>

            <!-- Notes List -->
            <div id="notesList" class="notes-container">
                @if($user->adminNotes->count() > 0)
                    @foreach($user->adminNotes->sortByDesc('created_at') as $note)
                    <div class="note-item" data-note-id="{{ $note->id }}">
                        <div class="note-header">
                            <strong>{{ $note->admin->full_name }}</strong>
                            <span class="text-muted ms-2" title="{{ $note->created_at->format('M d, Y H:i:s') }}">
                                {{ $note->created_at->format('M d, Y H:i') }}
                            </span>
                            <button type="button" class="btn btn-sm btn-danger float-end delete-note-btn" data-note-id="{{ $note->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="note-content">{{ $note->note }}</div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">No notes yet. Add one to get started.</p>
                @endif
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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal">
                    <i class="bi bi-pencil me-1"></i>Edit User
                </button>
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

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: var(--card-bg); border-color: #333;">
                <div class="modal-header" style="border-color: #333;">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User: {{ $user->full_name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        
                        <div class="mb-3">
                            <label for="edit_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control form-control-dark" id="edit_first_name" name="first_name" value="{{ $user->first_name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control form-control-dark" id="edit_last_name" name="last_name" value="{{ $user->last_name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control form-control-dark" id="edit_email" name="email" value="{{ $user->email }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control form-control-dark" id="edit_status" name="status" required>
                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Banned</option>
                            </select>
                        </div>

                        <div class="mb-3" id="ban_reason_group" style="{{ $user->status === 'active' ? 'display: none;' : '' }}">
                            <label for="edit_ban_reason" class="form-label">Ban Reason</label>
                            <textarea class="form-control form-control-dark" id="edit_ban_reason" name="ban_reason" rows="3" maxlength="1000" placeholder="Enter reason for banning this account">{{ $user->ban_reason }}</textarea>
                            <small class="form-text text-muted">This reason will be shown to the user when they try to log in.</small>
                        </div>

                        <div class="mb-3">
                            <label for="edit_role" class="form-label">Role</label>
                            <select class="form-control form-control-dark" id="edit_role" name="role" required>
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-color: #333;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const userId = {{ $user->id }};

// Handle add note form submission
document.getElementById('noteForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const noteInput = document.getElementById('noteInput');
    const note = noteInput.value.trim();

    if (!note) {
        MusicLocker.showToast('Please enter a note', 'warning');
        return;
    }

    fetch(`/admin/users/${userId}/notes`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ note: note })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new note to the list
            const notesList = document.getElementById('notesList');
            const noteItem = document.createElement('div');
            noteItem.className = 'note-item';
            noteItem.setAttribute('data-note-id', data.note.id);
            noteItem.innerHTML = `
                <div class="note-header">
                    <strong>${data.note.admin_name}</strong>
                    <span class="text-muted ms-2">${data.note.created_at}</span>
                    <button type="button" class="btn btn-sm btn-danger float-end delete-note-btn" data-note-id="${data.note.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="note-content">${data.note.note}</div>
            `;

            // Remove "No notes" message if it exists
            const noNotesMsg = notesList.querySelector('.text-muted');
            if (noNotesMsg && noNotesMsg.textContent.includes('No notes')) {
                noNotesMsg.remove();
            }

            // Insert new note at the top
            notesList.insertBefore(noteItem, notesList.firstChild);

            // Clear input
            noteInput.value = '';

            // Attach delete handler
            noteItem.querySelector('.delete-note-btn').addEventListener('click', handleDeleteNote);

            MusicLocker.showToast(data.message || 'Note added successfully', 'success');
        } else {
            MusicLocker.showToast(data.message || 'Error adding note', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        MusicLocker.showToast('Error adding note', 'danger');
    });
});

// Handle delete note
function handleDeleteNote(e) {
    e.preventDefault();

    const noteId = this.getAttribute('data-note-id');

    if (!confirm('Are you sure you want to delete this note?')) {
        return;
    }

    fetch(`/admin/users/${userId}/notes/${noteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const noteItem = document.querySelector(`[data-note-id="${noteId}"]`);
            noteItem.remove();

            // Check if no notes left
            const notesList = document.getElementById('notesList');
            if (notesList.children.length === 0) {
                notesList.innerHTML = '<p class="text-muted">No notes yet. Add one to get started.</p>';
            }

            MusicLocker.showToast(data.message || 'Note deleted successfully', 'success');
        } else {
            MusicLocker.showToast(data.message || 'Error deleting note', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        MusicLocker.showToast('Error deleting note', 'danger');
    });
}

// Attach delete handlers to existing notes
document.querySelectorAll('.delete-note-btn').forEach(btn => {
    btn.addEventListener('click', handleDeleteNote);
});

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

// Toggle ban reason field based on status
document.getElementById('edit_status').addEventListener('change', function() {
    const banReasonGroup = document.getElementById('ban_reason_group');
    if (this.value === 'inactive') {
        banReasonGroup.style.display = 'block';
    } else {
        banReasonGroup.style.display = 'none';
        document.getElementById('edit_ban_reason').value = '';
    }
});

// Handle edit user form submission
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('{{ route("admin.users.update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            MusicLocker.showToast(data.message || 'User updated successfully', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            MusicLocker.showToast(data.message || 'Error updating user', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        MusicLocker.showToast('Error updating user', 'danger');
    });
});
</script>
@endpush
@endsection
