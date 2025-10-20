@extends('layouts.app')

@section('title', $user->full_name . "'s Music")

@section('content')
<div class="container">
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $user->full_name }}'s Music Collection</h1>
            <p class="page-description">{{ $musicEntries->total() }} total tracks</p>
        </div>
        <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-secondary">Back to User</a>
    </div>

    <div class="admin-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Album</th>
                            <th>Rating</th>
                            <th>Favorite</th>
                            <th>Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($musicEntries as $entry)
                        <tr>
                            <td>{{ $entry->title }}</td>
                            <td>{{ $entry->artist }}</td>
                            <td>{{ $entry->album ?: '-' }}</td>
                            <td>
                                <x-rating-stars :rating="$entry->personal_rating" />
                            </td>
                            <td>
                                @if($entry->is_favorite)
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color: #ef4444;">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                @endif
                            </td>
                            <td>{{ formatDate($entry->created_at) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No music entries found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $musicEntries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
