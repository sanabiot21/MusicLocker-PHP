@extends('layouts.app')

@section('title', 'Recovery Requests')

@section('content')
<section class="py-5" style="margin-top: 80px;">
<div class="container">
    <div class="feature-card mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-1">
                <i class="bi bi-shield-exclamation me-2" style="color: var(--accent-blue);"></i>
                Account Recovery Requests
            </h1>
            <p class="text-muted mb-0">Review and process user ban appeals</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-glow">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>

    <div class="feature-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Message</th>
                        <th>Submitted</th>
                        <th>Resolved</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->user->full_name ?? 'Unknown User' }}</td>
                        <td>{{ $request->user->email ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $request->status === 'pending' ? 'warning text-dark' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td style="max-width: 320px;">{{ \Illuminate\Support\Str::limit($request->message, 140) }}</td>
                        <td>{{ formatDateTime($request->created_at) }}</td>
                        <td>{{ $request->resolved_at ? formatDateTime($request->resolved_at) : '—' }}</td>
                        <td class="d-flex gap-2">
                            <a href="{{ route('admin.users.detail', $request->user_id) }}" class="btn btn-sm btn-outline-glow">
                                <i class="bi bi-eye me-1"></i>User
                            </a>
                            @if($request->status === 'pending')
                                <button class="btn btn-sm btn-success" onclick="resolveRecovery({{ $request->id }}, 'approve')">
                                    <i class="bi bi-check2-circle me-1"></i>Approve
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="resolveRecovery({{ $request->id }}, 'reject')">
                                    <i class="bi bi-x-circle me-1"></i>Reject
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No recovery requests yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $requests->links() }}
        </div>
    </div>
</div>
</section>

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
</script>
@endpush
@endsection
