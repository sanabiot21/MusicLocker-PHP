@extends('layouts.app')

@section('title', 'My Playlists')

@section('content')
<!-- Playlists Index Page -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-music-note-list me-2"></i>Your Playlists</h1>
            <a href="{{ route('playlists.create') }}" class="btn btn-glow">
                <i class="bi bi-plus-circle me-2"></i>Create Playlist
            </a>
        </div>

        @if($playlists->count() == 0)
            <!-- Empty State -->
            <div class="feature-card text-center py-5">
                <i class="bi bi-music-note-list display-1 text-muted mb-3"></i>
                <h3>No Playlists Yet</h3>
                <p class="text-muted mb-4">Create your first playlist to organize your music collection</p>
                <a href="{{ route('playlists.create') }}" class="btn btn-glow">
                    <i class="bi bi-plus-circle me-2"></i>Create Your First Playlist
                </a>
            </div>
        @else
            <!-- Playlists Grid -->
            <div class="row g-4">
                @foreach($playlists as $playlist)
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('playlists.show', $playlist->id) }}" class="text-decoration-none">
                            <div class="feature-card h-100 playlist-card">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1">{{ $playlist->name }}</h5>
                                        @if($playlist->description)
                                            <p class="text-muted small mb-0">
                                                {{ Str::limit($playlist->description, 80) }}
                                            </p>
                                        @endif
                                    </div>
                                    @if($playlist->is_public)
                                        <span class="badge bg-success ms-2">
                                            <i class="bi bi-globe"></i> Public
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top border-secondary">
                                    <div class="small text-muted">
                                        <i class="bi bi-music-note me-1"></i>{{ $playlist->musicEntries->count() }} tracks
                                    </div>
                                    @php
                                        $totalDuration = $playlist->musicEntries->sum('duration') ?? 0;
                                    @endphp
                                    @if($totalDuration > 0)
                                        <div class="small text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ gmdate("H:i:s", $totalDuration) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="small text-muted mt-2">
                                    <i class="bi bi-calendar me-1"></i>Updated {{ $playlist->updated_at->diffForHumans() }}
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@push('styles')
<style>
    .playlist-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }

    .playlist-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 212, 255, 0.1);
    }

    .playlist-card a {
        text-decoration: none !important;
    }
</style>
@endpush
@endsection
