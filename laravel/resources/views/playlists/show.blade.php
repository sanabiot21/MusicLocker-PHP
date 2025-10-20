@extends('layouts.app')

@section('title', $playlist->name)

@section('content')
<!-- Playlist Show Page -->
<section class="py-5" style="margin-top: 80px;">
<div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1><i class="bi bi-music-note-list me-2"></i>{{ $playlist->name }}</h1>
                @if($playlist->description)
                    <p class="text-muted mb-0">{{ $playlist->description }}</p>
                @endif
            </div>
            <a href="{{ route('playlists.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Playlists
            </a>
        </div>

        <!-- Playlist Info -->
        <div class="feature-card mb-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex gap-4">
                        <div>
                            <div class="small text-muted">Tracks</div>
                            <div class="fs-5">{{ $playlist->musicEntries->count() }}</div>
    </div>
                        @php
                            $totalDuration = $playlist->musicEntries->sum('duration') ?? 0;
                        @endphp
                        @if($totalDuration > 0)
                            <div>
                                <div class="small text-muted">Duration</div>
                                <div class="fs-5">{{ gmdate("H:i:s", $totalDuration) }}</div>
                </div>
            @endif
                        <div>
                            <div class="small text-muted">Visibility</div>
                            <div>
                        @if($playlist->is_public)
                                    <span class="badge bg-success"><i class="bi bi-globe"></i> Public</span>
                        @else
                                    <span class="badge bg-secondary"><i class="bi bi-lock"></i> Private</span>
                        @endif
                            </div>
            </div>
        </div>
    </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('playlists.edit', $playlist->id) }}" class="btn btn-glow me-2">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </a>
                    <button class="btn btn-outline-glow me-2" data-bs-toggle="modal" data-bs-target="#addTracksModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Tracks
                    </button>
                    <button class="btn btn-outline-danger delete-playlist-btn" 
                            data-playlist-id="{{ $playlist->id }}"
                            data-name="{{ $playlist->name }}">
                        <i class="bi bi-trash me-1"></i>Delete
        </button>
                </div>
            </div>
    </div>

        <!-- Tracks List -->
        @if($playlist->musicEntries->count() == 0)
            <div class="feature-card text-center py-5">
                <i class="bi bi-music-note display-1 text-muted mb-3"></i>
                <h3>No Tracks Yet</h3>
                <p class="text-muted mb-4">Add tracks from your music collection to this playlist</p>
                <a href="{{ route('music.index') }}" class="btn btn-glow">
                    <i class="bi bi-collection me-2"></i>Browse Music
                </a>
            </div>
        @else
            <div class="feature-card">
                <h5 class="mb-4"><i class="bi bi-music-note me-2"></i>Tracks</h5>
                <div class="list-group list-group-flush">
            @foreach($playlist->musicEntries as $index => $entry)
                        <div class="list-group-item bg-dark text-white d-flex align-items-center py-3">
                            <div class="me-3 text-muted">{{ $index + 1 }}</div>

                            @if($entry->album_art_url)
                                <img src="{{ $entry->album_art_url }}" class="rounded me-3" 
                                     style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $entry->title }}">
                            @else
                                <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-music-note text-muted"></i>
                                </div>
                            @endif
                            
                            <div class="flex-grow-1">
                                <div class="fw-bold">
                                    <a href="{{ route('music.show', $entry->id) }}" class="text-decoration-none text-white">
                                        {{ $entry->title }}
                                    </a>
                                </div>
                                <div class="text-muted small">{{ $entry->artist }}</div>
                        </div>
                            
                            @if($entry->duration)
                                <div class="text-muted small me-3">{{ gmdate("i:s", $entry->duration) }}</div>
                            @endif
                            
                            <button class="btn btn-outline-danger btn-sm remove-track-btn" 
                                    data-playlist-id="{{ $playlist->id }}"
                                    data-entry-id="{{ $entry->id }}"
                                    data-title="{{ $entry->title }}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                </div>
            @endforeach
        </div>
        </div>
    @endif
</div>
</section>

<!-- Add Tracks Modal -->
<div class="modal fade" id="addTracksModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Tracks from Your Collection</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-dark border-secondary"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="addTracksSearch" placeholder="Search your collection...">
                </div>
                <div id="addTracksList" class="list-group list-group-flush">
                    @foreach(auth()->user()->musicEntries()->with('tags')->latest()->limit(50)->get() as $ue)
                        @if(!$playlist->musicEntries->contains($ue->id))
                            <div class="list-group-item bg-dark text-white d-flex align-items-center add-track-item" 
                                 data-search="{{ strtolower($ue->title . ' ' . $ue->artist . ' ' . ($ue->album ?? '')) }}">
                                @if($ue->album_art_url)
                                    <img src="{{ $ue->album_art_url }}" class="rounded me-3" 
                                         style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $ue->title }}">
                                @else
                                    <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="bi bi-music-note text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $ue->title }}</div>
                                    <div class="text-muted small">{{ $ue->artist }}{{ $ue->album ? (' • ' . $ue->album) : '' }}</div>
                                </div>
                                <button class="btn btn-sm btn-outline-glow btn-add-track" 
                                        data-playlist-id="{{ $playlist->id }}" 
                                        data-entry-id="{{ $ue->id }}">
                                    <i class="bi bi-plus-circle me-1"></i>Add
                                </button>
                            </div>
                        @endif
                    @endforeach
                    @if(auth()->user()->musicEntries->count() == 0)
                        <div class="text-muted">No entries found in your collection.</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Playlist Modal -->
<div class="modal fade" id="deletePlaylistModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="deletePlaylistName"></span>"?</p>
                <p class="text-warning small">This will remove the playlist and all its tracks. This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deletePlaylistForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Playlist
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Remove Track Modal -->
<div class="modal fade" id="removeTrackModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title">Remove Track</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove "<span id="removeTrackName"></span>" from this playlist?</p>
            </div>
            <div class="modal-footer border-top border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveTrackBtn">
                    <i class="bi bi-trash me-1"></i>Remove
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('addTracksSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#addTracksList .add-track-item').forEach(item => {
                const text = item.dataset.search;
                item.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // Track if any tracks were added during this modal session
    let tracksAdded = false;

    // Add track buttons
    document.querySelectorAll('.btn-add-track').forEach(btn => {
        btn.addEventListener('click', async function() {
            const playlistId = this.dataset.playlistId;
            const musicEntryId = this.dataset.entryId;
            this.disabled = true;
            const originalHtml = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Adding';

            try {
                const response = await fetch('/playlists/add-track', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        playlist_id: playlistId,
                        music_entry_id: musicEntryId
                    })
                });

                const data = await response.json();
                if (data.success || response.ok) {
                    this.classList.remove('btn-outline-glow');
                    this.classList.add('btn-success');
                    this.innerHTML = '<i class="bi bi-check"></i> Added';
                    MusicLocker.showToast('Track added to playlist!', 'success');
                    tracksAdded = true; // Mark that tracks were added
                } else {
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                    MusicLocker.showToast(data.error || 'Failed to add track', 'danger');
                }
            } catch (e) {
                console.error('Error:', e);
                this.disabled = false;
                this.innerHTML = originalHtml;
                MusicLocker.showToast('Network error adding track', 'danger');
            }
        });
    });

    // Handle modal close - reload page if tracks were added
    const addTracksModal = document.getElementById('addTracksModal');
    if (addTracksModal) {
        addTracksModal.addEventListener('hidden.bs.modal', function() {
            if (tracksAdded) {
                // Small delay to let the user see the modal close
                setTimeout(() => {
                    location.reload();
                }, 300);
            }
        });
    }

    // Delete playlist
    const deleteBtn = document.querySelector('.delete-playlist-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const playlistId = this.dataset.playlistId;
            const playlistName = this.dataset.name;
            document.getElementById('deletePlaylistName').textContent = playlistName;
            const deleteForm = document.getElementById('deletePlaylistForm');
            deleteForm.action = `/playlists/${playlistId}/delete`;
            new bootstrap.Modal(document.getElementById('deletePlaylistModal')).show();
        });
    }

    // Remove track with modal
    let removeTrackData = null;
    document.querySelectorAll('.remove-track-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const playlistId = this.dataset.playlistId;
            const entryId = this.dataset.entryId;
            const title = this.dataset.title;

            removeTrackData = { playlistId, entryId };
            document.getElementById('removeTrackName').textContent = title;
            new bootstrap.Modal(document.getElementById('removeTrackModal')).show();
        });
    });

    // Confirm remove track
    const confirmRemoveBtn = document.getElementById('confirmRemoveTrackBtn');
    if (confirmRemoveBtn) {
        confirmRemoveBtn.addEventListener('click', async function() {
            if (!removeTrackData) return;

            const { playlistId, entryId } = removeTrackData;
            confirmRemoveBtn.disabled = true;
            const originalHtml = confirmRemoveBtn.innerHTML;
            confirmRemoveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Removing';

            try {
                const response = await fetch('/playlists/remove-track', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        playlist_id: playlistId,
                        entry_id: entryId
                    })
                });

                if (response.ok) {
                    bootstrap.Modal.getInstance(document.getElementById('removeTrackModal')).hide();
                    document.querySelector(`[data-entry-id="${entryId}"]`)?.closest('.list-group-item')?.remove();
                    MusicLocker.showToast('Track removed from playlist', 'success');
                    location.reload();
                } else {
                    const data = await response.json();
                    MusicLocker.showToast(data.error || 'Failed to remove track', 'danger');
                }
            } catch (e) {
                console.error('Error:', e);
                MusicLocker.showToast('Network error removing track', 'danger');
            } finally {
                confirmRemoveBtn.disabled = false;
                confirmRemoveBtn.innerHTML = originalHtml;
            }
        });
    }
});
</script>
@endpush
@endsection
