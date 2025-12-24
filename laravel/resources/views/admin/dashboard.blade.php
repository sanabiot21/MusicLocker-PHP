@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<section class="py-5" style="margin-top: 80px;">
<div class="container">
    <!-- Header Card with Navigation -->
    <div class="feature-card mb-4">
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
                    <a href="{{ route('admin.users') }}" class="btn btn-glow">
                        <i class="bi bi-people me-1"></i>Users
                    </a>
                    <a href="{{ route('admin.system.health') }}" class="btn btn-outline-glow">
                        <i class="bi bi-cpu me-1"></i>System
                    </a>
                    <a href="{{ route('admin.settings') }}" class="btn btn-outline-glow">
                        <i class="bi bi-gear me-1"></i>Settings
                    </a>
                    <a href="{{ route('admin.recovery.requests') }}" class="btn btn-outline-glow">
                        <i class="bi bi-shield-exclamation me-1"></i>Recovery
                    </a>
                </div>
            </div>
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
                <h2 class="stat-value stat-value-blue mb-1">{{ formatNumber($userStats['total_users']) }}</h2>
                <p class="stat-label mb-0">Total Users</p>
            </div>
        </div>

        <!-- Active Users -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-green">
                <div class="mb-3">
                    <i class="bi bi-person-check display-4" style="color: #28a745;"></i>
                </div>
                <h2 class="stat-value stat-value-green mb-1">{{ formatNumber($userStats['active_users']) }}</h2>
                <p class="stat-label mb-0">Active Users</p>
            </div>
        </div>

        <!-- New Users Today -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-purple">
                <div class="mb-3">
                    <i class="bi bi-person-plus display-4" style="color: var(--accent-purple);"></i>
                </div>
                <h2 class="stat-value stat-value-purple mb-1">{{ formatNumber($userStats['new_users_today']) }}</h2>
                <p class="stat-label mb-0">New Today</p>
            </div>
        </div>

        <!-- Music Entries -->
        <div class="col-lg-3 col-md-6">
            <div class="feature-card stat-card-colored stat-card-yellow">
                <div class="mb-3">
                    <i class="bi bi-music-note-list display-4" style="color: #feca57;"></i>
                </div>
                <h2 class="stat-value stat-value-yellow mb-1">{{ formatNumber($userStats['total_music_entries']) }}</h2>
                <p class="stat-label mb-0">Music Entries</p>
            </div>
        </div>
    </div>

    <!-- Weekly Analytics Card -->
    <div class="feature-card mb-4">
        <div class="card-header mb-4">
            <h3 class="mb-0">
                <i class="bi bi-graph-up me-2" style="color: var(--accent-blue);"></i>Weekly Analytics
            </h3>
        </div>

        <div class="row g-4">
            <!-- New Users This Week -->
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="weekly-stat-number" style="color: var(--accent-blue);">
                        {{ $weeklyStats['new_users'] ?? 0 }}
                    </div>
                    <div class="weekly-stat-label">New Users</div>
                    <div class="weekly-stat-period">This Week</div>
                </div>
            </div>

            <!-- Songs Added This Week -->
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="weekly-stat-number" style="color: var(--accent-purple);">
                        {{ $weeklyStats['new_music'] ?? 0 }}
                    </div>
                    <div class="weekly-stat-label">Songs Added</div>
                    <div class="weekly-stat-period">This Week</div>
                </div>
            </div>

            <!-- Most Active User -->
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    @if($weeklyStats['most_active'])
                        <div class="weekly-stat-number" style="color: #ff6b6b;">
                            {{ $weeklyStats['most_active']->music_entries_count ?? 0 }}
                        </div>
                        <div class="weekly-stat-label">{{ $weeklyStats['most_active']->full_name ?? 'N/A' }}</div>
                        <div class="weekly-stat-period">Most Active User</div>
                    @else
                        <div class="weekly-stat-number" style="color: #ff6b6b;">0</div>
                        <div class="weekly-stat-label">No Users</div>
                        <div class="weekly-stat-period">Most Active User</div>
                    @endif
                </div>
            </div>

            <!-- Popular Tag -->
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    @if($weeklyStats['popular_tag'])
                        <div class="weekly-stat-number" style="color: #51cf66;">
                            {{ $weeklyStats['popular_tag']->usage_count ?? 0 }}
                        </div>
                        <div class="weekly-stat-label">{{ $weeklyStats['popular_tag']->name ?? 'N/A' }}</div>
                        <div class="weekly-stat-period">Popular Tag</div>
                    @else
                        <div class="weekly-stat-number" style="color: #51cf66;">0</div>
                        <div class="weekly-stat-label">No Tags</div>
                        <div class="weekly-stat-period">Popular Tag</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Password Resets -->
    @if($pendingResets->count() > 0)
    <div class="feature-card mb-4">
        <div class="card-header mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="mb-0">
                    <i class="bi bi-exclamation-circle me-2" style="color: #ffc107;"></i>Pending Password Resets
                </h3>
                <span class="badge bg-warning text-dark">{{ $pendingResets->count() }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingResets as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2" style="background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                </div>
                                {{ $user->full_name }}
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ formatDateTime($user->reset_requested_at ?? $user->updated_at) }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-sm btn-glow">
                                <i class="bi bi-eye me-1"></i>View User
                            </a>
                            <button class="btn btn-sm btn-success" onclick="approveReset({{ $user->id }})">
                                <i class="bi bi-check2-circle me-1"></i>Approve
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="rejectReset({{ $user->id }})">
                                <i class="bi bi-x-circle me-1"></i>Reject
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Pending Recovery Requests -->
    @if(isset($pendingRecoveries) && $pendingRecoveries->count() > 0)
    <div class="feature-card mb-4">
        <div class="card-header mb-3">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="mb-0">
                    <i class="bi bi-envelope-exclamation me-2" style="color: var(--accent-purple);"></i>Pending Recovery Requests
                </h3>
                <span class="badge bg-info text-dark">{{ $pendingRecoveries->count() }}</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRecoveries as $request)
                    <tr>
                        <td>{{ $request->user->full_name ?? 'Unknown User' }}</td>
                        <td>{{ $request->user->email ?? 'N/A' }}</td>
                        <td style="max-width: 320px;">{{ \Illuminate\Support\Str::limit($request->message, 120) }}</td>
                        <td>{{ formatDateTime($request->created_at) }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.users.detail', $request->user_id) }}" class="btn btn-sm btn-outline-glow">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                            <button class="btn btn-sm btn-success" onclick="resolveRecovery({{ $request->id }}, 'approve')">
                                <i class="bi bi-check2-circle me-1"></i>Approve
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="resolveRecovery({{ $request->id }}, 'reject')">
                                <i class="bi bi-x-circle me-1"></i>Reject
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Recent Activity -->
    <div class="feature-card">
        <div class="card-header mb-3">
            <h3 class="mb-0">
                <i class="bi bi-clock-history me-2" style="color: var(--accent-blue);"></i>Recent Activity
            </h3>
        </div>

        @if($recentActivity->count() > 0)
            <div class="activity-timeline-enhanced">
                @foreach($recentActivity as $activity)
                <div class="activity-item-enhanced">
                    <div class="activity-icon-enhanced">
                        <i class="bi {{ $activity->icon_class }}" style="color: var(--accent-{{ $activity->icon_color === 'success' ? 'blue' : ($activity->icon_color === 'danger' ? 'purple' : 'blue') }});"></i>
                    </div>
                    <div class="activity-content-enhanced">
                        <div class="activity-header-enhanced">
                            <div class="activity-description-enhanced">
                                {{ $activity->description }}
                            </div>
                        </div>
                        <div class="activity-meta-admin text-muted small">
                            @if($activity->user)
                                <i class="bi bi-person me-1"></i>
                                <span class="activity-user">{{ $activity->user->full_name }}</span>
                                <span class="mx-2">•</span>
                            @endif
                            <i class="bi bi-clock me-1"></i>
                            <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-muted text-center py-4">No recent activity</p>
        @endif
    </div>
</div>
</section>

@push('styles')
<style>
    .admin-header-icon {
        font-size: 2.5rem;
        background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-right: 1.5rem;
    }

    .stat-card-admin {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 212, 255, 0.1);
    }

    .stat-card-admin:hover {
        border-color: var(--accent-blue);
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 212, 255, 0.1);
    }

    .stat-icon {
        font-size: 1.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 8px;
    }

    .stat-icon-blue {
        background: rgba(0, 212, 255, 0.1);
        color: var(--accent-blue);
    }

    .stat-icon-green {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .stat-icon-purple {
        background: rgba(138, 43, 226, 0.1);
        color: var(--accent-purple);
    }

    .stat-icon-warning {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .stat-value-admin {
        font-size: 1.75rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label-admin {
        font-size: 0.875rem;
        color: var(--text-gray);
        font-weight: 500;
        margin-top: 0.25rem;
    }

    .weekly-stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .weekly-stat-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--text-light);
        margin-bottom: 0.25rem;
    }

    .weekly-stat-period {
        font-size: 0.8rem;
        color: var(--text-gray);
    }

    .card-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 1rem;
    }

    .card-header h3 {
        color: var(--text-light);
        font-weight: 600;
    }

    .table {
        color: var(--text-light);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 212, 255, 0.05);
    }

    .table-dark {
        background-color: rgba(0, 0, 0, 0.3);
        border-color: rgba(255, 255, 255, 0.1);
    }

    .table-dark th {
        color: var(--text-light);
        font-weight: 600;
        border-color: rgba(255, 255, 255, 0.1);
    }

    .activity-item-admin {
        display: flex;
        align-items: flex-start;
    }

    .activity-icon-admin {
        flex-shrink: 0;
        margin-top: 0.125rem;
    }

    .activity-description-admin {
        color: var(--text-light);
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .activity-meta-admin {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .activity-user {
        font-weight: 500;
    }

    .activity-time {
        color: var(--text-gray);
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .admin-header-icon {
            font-size: 2rem;
            margin-right: 1rem;
        }

        .col-md-8,
        .col-md-4 {
            margin-bottom: 1rem;
        }

        .text-md-end {
            text-align: left !important;
        }

        .d-flex.flex-md-row {
            flex-direction: column;
        }

        .weekly-stat-number {
            font-size: 1.5rem;
        }

        .stat-value-admin {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
function resolveRecovery(id, action) {
    const message = action === 'reject'
        ? 'Reject this recovery request?'
        : 'Approve and restore this account?';

    if (!confirm(message)) {
        return;
    }

    fetch(`/admin/recovery-requests/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            MusicLocker.showToast(data.message || 'Request updated', 'success');
            setTimeout(() => window.location.reload(), 800);
        } else {
            MusicLocker.showToast(data.message || 'Unable to update request', 'danger');
        }
    })
    .catch(() => {
        MusicLocker.showToast('Unable to update request', 'danger');
    });
}

function approveReset(userId) {
    fetch('/admin/reset-requests/approve', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            MusicLocker.showToast(data.message || 'Request approved', 'success');
            setTimeout(() => window.location.reload(), 800);
        } else {
            MusicLocker.showToast(data.message || 'Unable to approve request', 'danger');
        }
    })
    .catch(() => MusicLocker.showToast('Unable to approve request', 'danger'));
}

function rejectReset(userId) {
    if (!confirm('Reject this password reset request?')) {
        return;
    }

    fetch('/admin/reset-requests/reject', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            MusicLocker.showToast(data.message || 'Request rejected', 'success');
            setTimeout(() => window.location.reload(), 800);
        } else {
            MusicLocker.showToast(data.message || 'Unable to reject request', 'danger');
        }
    })
    .catch(() => MusicLocker.showToast('Unable to reject request', 'danger'));
}
</script>
@endpush
@endsection
