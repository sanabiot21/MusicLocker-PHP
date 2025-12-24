@extends('layouts.app')

@section('title', 'User Detail - ' . $user->full_name)

@section('content')
<section class="py-5" style="margin-top: 80px;">
<div class="container">
    <!-- User Header with Avatar -->
    <div class="feature-card mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="user-avatar-icon user-avatar-large">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>
            <div class="col">
                <h1 class="page-title mb-1">{{ $user->full_name }}</h1>
                <p class="page-description mb-2">{{ $user->email }}</p>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    <span class="badge badge-with-icon bg-{{ $user->status === 'active' ? 'success' : 'warning' }}">
                        <i class="bi bi-{{ $user->status === 'active' ? 'check-circle' : 'pause-circle' }}"></i>
                        {{ $user->status === 'active' ? 'Active' : 'Suspended' }}
                    </span>
                    <span class="badge badge-with-icon bg-{{ $user->role === 'admin' ? 'primary' : 'info' }}">
                        <i class="bi bi-{{ $user->role === 'admin' ? 'shield-check' : 'person' }}"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="text-muted">
                        <i class="bi bi-calendar me-1"></i>Joined {{ formatDate($user->created_at) }}
                    </span>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.users') }}" class="btn btn-outline-glow">
                    <i class="bi bi-arrow-left me-1"></i>Back to Users
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards with Colors -->
    <div class="row g-4 mb-4">
        <!-- Music Entries -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-blue">
                <div class="mb-3">
                    <i class="bi bi-music-note-list display-4" style="color: var(--accent-blue);"></i>
                </div>
                <h2 class="stat-value stat-value-blue mb-1">{{ $stats['total_entries'] }}</h2>
                <p class="stat-label mb-0">Music Entries</p>
            </div>
        </div>

        <!-- Playlists -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-purple">
                <div class="mb-3">
                    <i class="bi bi-collection-play display-4" style="color: var(--accent-purple);"></i>
                </div>
                <h2 class="stat-value stat-value-purple mb-1">{{ $user->playlists_count }}</h2>
                <p class="stat-label mb-0">Playlists</p>
            </div>
        </div>

        <!-- Favorites -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-yellow">
                <div class="mb-3">
                    <i class="bi bi-heart-fill display-4" style="color: #feca57;"></i>
                </div>
                <h2 class="stat-value stat-value-yellow mb-1">{{ $stats['favorite_entries'] }}</h2>
                <p class="stat-label mb-0">Favorites</p>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-teal">
                <div class="mb-3">
                    <i class="bi bi-star-fill display-4" style="color: #4ecdc4;"></i>
                </div>
                <h2 class="stat-value mb-1" style="color: #4ecdc4;">{{ $stats['average_rating'] }}</h2>
                <p class="stat-label mb-0">Avg Rating</p>
            </div>
        </div>
    </div>

    <!-- User Information -->
    <div class="feature-card mb-4">
        <div class="card-header mb-3">
            <h3 class="mb-0">
                <i class="bi bi-info-circle me-2" style="color: var(--accent-blue);"></i>Account Information
            </h3>
        </div>
        <div class="info-display-group">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="info-display-item">
                        <div class="info-display-label">Email Address</div>
                        <div class="info-display-value">{{ $user->email }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-display-item">
                        <div class="info-display-label">Last Login</div>
                        <div class="info-display-value">{{ $user->last_login ? formatDateTime($user->last_login) : 'Never' }}</div>
                    </div>
                </div>
                @if($user->status !== 'active' && $user->ban_reasons)
                <div class="col-12">
                    <div class="info-display-item">
                        <div class="info-display-label">Ban Reason</div>
                        <div class="info-display-value text-warning">{{ $user->ban_reason }}</div>
                    </div>
                </div>
                @endif
                @if($user->reset_requested_at && !$user->reset_token)
                <div class="col-12">
                    <div class="info-display-item">
                        <div class="info-display-label">Password Reset</div>
                        <div class="info-display-value text-info">Pending admin approval since {{ formatDateTime($user->reset_requested_at) }}</div>
                    </div>
                </div>
                @elseif($user->reset_token)
                <div class="col-12">
                    <div class="info-display-item">
                        <div class="info-display-label">Password Reset</div>
                        <div class="info-display-value text-success">Approved token issued {{ formatDateTime($user->reset_token_created_at) }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Admin Notes -->
    <div class="feature-card mb-4">
        <div class="card-header mb-3">
            <h3 class="mb-0">
                <i class="bi bi-sticky me-2" style="color: var(--accent-blue);"></i>Admin Notes
            </h3>
        </div>
        <div>
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
    <div class="feature-card">
        <div class="card-header mb-3">
            <h3 class="mb-0">
                <i class="bi bi-tools me-2" style="color: var(--accent-blue);"></i>Admin Actions
            </h3>
        </div>
        <div class="action-buttons">
            <button type="button" class="btn btn-glow" data-bs-toggle="modal" data-bs-target="#editUserModal">
                <i class="bi bi-pencil me-1"></i>Edit User
            </button>
            <a href="{{ route('admin.users.music', $user->id) }}" class="btn btn-outline-glow">
                <i class="bi bi-music-note-list me-1"></i>View Music Collection
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
                <i class="bi bi-trash me-1"></i>Delete User
            </button>
            @endif
        </div>
    </div>
</div>
</section>

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
                                <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Suspended</option>
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
