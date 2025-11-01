@extends('layouts.app')

@section('title', $user->full_name . "'s Music")

@section('content')
<section class="py-5" style="margin-top: 80px;">
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-music-note-list me-2" style="color: var(--accent-blue);"></i>
                {{ $user->full_name }}'s Music Collection
            </h1>
            <p class="page-description">{{ $musicEntries->total() }} total tracks</p>
        </div>
        <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-outline-glow">
            <i class="bi bi-arrow-left me-1"></i>Back to User
        </a>
    </div>

    <div class="feature-card">
        <div class="table-responsive">
            <table class="admin-table-enhanced">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Artist</th>
                        <th>Album</th>
                        <th>Genre</th>
                        <th>Rating</th>
                        <th>Favorite</th>
                        <th>Added</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($musicEntries as $entry)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $entry->title }}</div>
                        </td>
                        <td>{{ $entry->artist }}</td>
                        <td>{{ $entry->album ?: '-' }}</td>
                        <td>
                            @if($entry->genre)
                            <span class="badge bg-secondary">{{ $entry->genre }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $entry->personal_rating)
                                        <i class="bi bi-star-fill" style="color: #ffc107; font-size: 0.9rem;"></i>
                                    @else
                                        <i class="bi bi-star" style="color: var(--text-gray); font-size: 0.9rem;"></i>
                                    @endif
                                @endfor
                            </div>
                        </td>
                        <td>
                            @if($entry->is_favorite)
                                <i class="bi bi-heart-fill" style="color: #ef4444; font-size: 1.1rem;"></i>
                            @else
                                <i class="bi bi-heart" style="color: var(--text-gray);"></i>
                            @endif
                        </td>
                        <td>{{ formatDate($entry->created_at) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No music entries found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($musicEntries->hasPages())
        <div class="divider"></div>
        <div class="d-flex justify-content-center py-3">
            {{ $musicEntries->links() }}
        </div>
        @endif
    </div>
</div>
</section>
@endsection
