@extends('layouts.app')

@section('title', $entry->title . ' - ' . $entry->artist)

@section('content')
<!-- Music Details Page Content -->
<section class="py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Back Button (top right outside card) -->
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('music.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back to Collection
                    </a>
                </div>

                <!-- Hero Header Card -->
                <div class="feature-card mb-4" style="overflow: hidden;">
                    <div class="row g-0">
                        <!-- Album Art -->
                        <div class="col-md-4">
                            @if($entry->album_art_url)
                                <div class="album-art-container" style="position: relative;">
                                    <img src="{{ $entry->album_art_url }}" 
                                         class="img-fluid w-100" 
                                         alt="{{ $entry->title }}"
                                         style="height: 400px; object-fit: cover; border-radius: 8px; border: 2px solid var(--accent-blue); box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);">
                                </div>
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center" 
                                     style="height: 400px; border-radius: 8px; border: 2px solid rgba(255,255,255,0.1);">
                                    <div class="text-center">
                                        <i class="bi bi-vinyl display-1 text-muted mb-3"></i>
                                        <p class="text-muted">No Album Art</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Track Info & Actions -->
                        <div class="col-md-8 p-4">
                            <!-- Title & Favorite -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1 me-3">
                                    <h1 class="fw-bold mb-2" style="font-size: 2rem; line-height: 1.2;">
                                        {{ $entry->title }}
                                    </h1>
                                    <h4 class="text-muted mb-0">{{ $entry->artist }}</h4>
                                </div>
                                <button class="btn btn-lg favorite-btn pulse-on-hover" 
                                        data-entry-id="{{ $entry->id }}"
                                        data-is-favorite="{{ $entry->is_favorite ? '1' : '0' }}"
                                        title="{{ $entry->is_favorite ? 'Remove from favorites' : 'Add to favorites' }}"
                                        style="min-width: 60px; border: none; background: transparent;">
                                    <i class="bi bi-heart{{ $entry->is_favorite ? '-fill text-danger' : '' }}" 
                                       style="font-size: 2.5rem; {{ $entry->is_favorite ? 'text-shadow: 0 0 15px #ff0040;' : '' }}"></i>
                                </button>
                            </div>

                            <!-- Metadata Pills -->
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                @if($entry->genre)
                                    <span class="badge bg-gradient-primary px-3 py-2" style="font-size: 0.9rem;">
                                        <i class="bi bi-tag me-1"></i>{{ $entry->genre }}
                                    </span>
                                @endif

                                @if($entry->release_year)
                                    <span class="badge bg-dark px-3 py-2" style="font-size: 0.9rem; border: 1px solid var(--accent-blue);">
                                        <i class="bi bi-calendar me-1"></i>{{ $entry->release_year }}
                                    </span>
                                @endif

                                @if($entry->duration)
                                    <span class="badge bg-dark px-3 py-2" style="font-size: 0.9rem; border: 1px solid var(--accent-purple);">
                                        <i class="bi bi-clock me-1"></i>{{ gmdate("i:s", $entry->duration) }}
                                    </span>
                                @endif
                                
                                @if($entry->album)
                                    <span class="badge bg-dark px-3 py-2" style="font-size: 0.9rem; border: 1px solid rgba(255,255,255,0.2);">
                                        <i class="bi bi-disc me-1"></i>{{ $entry->album }}
                                    </span>
                                @endif
                            </div>

                            <!-- Personal Rating -->
                            <div class="mb-4">
                                <div class="small text-muted mb-2">Personal Rating</div>
                                <div class="rating-display">
                                    @if($entry->personal_rating)
                                        <div class="d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $entry->personal_rating ? '-fill' : '' }} me-1" 
                                                   style="font-size: 1.5rem; color: {{ $i <= $entry->personal_rating ? '#FFD700' : '#666' }}; text-shadow: {{ $i <= $entry->personal_rating ? '0 0 10px rgba(255, 215, 0, 0.5)' : 'none' }};"></i>
                                            @endfor
                                            <span class="ms-2 text-muted">{{ $entry->personal_rating }}/5</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star me-1" style="font-size: 1.5rem; color: #444;"></i>
                                            @endfor
                                            <span class="ms-2 text-muted fst-italic">Not rated</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tags -->
                            @if($entry->tags && $entry->tags->count() > 0)
                                <div class="mb-4">
                                    <div class="small text-muted mb-2">Tags</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($entry->tags as $tag)
                                            <span class="badge px-3 py-2" 
                                                  style="background-color: {{ $tag->color }}; font-size: 0.9rem; border-radius: 20px;">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <a href="{{ route('music.edit', $entry->id) }}" 
                                   class="btn btn-glow">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                
                                @if($entry->spotify_url)
                                    <a href="{{ $entry->spotify_url }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="btn btn-success">
                                        <i class="bi bi-spotify me-1"></i>Open in Spotify
                                    </a>
                                @endif
                                
                                <button class="btn btn-outline-secondary copy-link-btn" 
                                        data-url="{{ url()->current() }}"
                                        title="Copy link to this track">
                                    <i class="bi bi-link-45deg me-1"></i>Copy Link
                                </button>
                                
                                <form action="{{ route('music.destroy', $entry->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this track?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>

                            <!-- Dates -->
                            <div class="small text-muted">
                                @if($entry->date_discovered)
                                    <div class="mb-1">
                                        <i class="bi bi-calendar-plus me-1"></i>
                                        Discovered {{ $entry->date_discovered->format('M d, Y') }}
                                    </div>
                                @endif
                                <div>
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Added {{ $entry->created_at->format('M d, Y g:i A') }}
                                </div>
                            </div>

                            <!-- Personal Notes (inline in the same card) -->
                            @if($entry->notes && $entry->notes->count() > 0)
                                @php $note = $entry->notes->first(); @endphp
                                @if($note->note_text || $note->mood || $note->memory_context || $note->listening_context)
                                    <hr class="my-4">
                                    <h5 class="mb-3"><i class="bi bi-journal-text me-2"></i>Personal Notes</h5>
                                    
                                    @if($note->note_text)
                                    <div class="note-content p-3 mb-3" 
                                         style="background: rgba(0, 0, 0, 0.3); border-left: 3px solid var(--accent-blue); border-radius: 4px;">
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $note->note_text }}</p>
                                    </div>
                                    @endif
                                    
                                    <div class="row g-3 mb-2">
                                        @if($note->mood)
                                            <div class="col-md-4">
                                                <div class="small text-muted mb-1">Mood</div>
                                                <span class="badge bg-gradient-primary px-3 py-2">
                                                    <i class="bi bi-emoji-smile me-1"></i>{{ $note->mood }}
                                                </span>
                                            </div>
                                        @endif
                                        
                                        @if($note->memory_context)
                                            <div class="col-md-4">
                                                <div class="small text-muted mb-1">Memory</div>
                                                <div class="text-light">{{ $note->memory_context }}</div>
                                            </div>
                                        @endif
                                        
                                        @if($note->listening_context)
                                            <div class="col-md-4">
                                                <div class="small text-muted mb-1">Listening Context</div>
                                                <div class="text-light">{{ $note->listening_context }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="small text-muted">
                                        <i class="bi bi-clock me-1"></i>Last updated {{ $note->updated_at->format('M d, Y') }}
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
.album-art-container {
    width: 100%;
    height: 400px;
}

.favorite-btn {
    transition: transform 0.3s ease;
}

.favorite-btn:hover {
    transform: scale(1.1);
}

.copy-link-btn {
    transition: all 0.3s ease;
}

.note-content {
    font-size: 0.95rem;
    line-height: 1.6;
}

@media (max-width: 768px) {
    h1 {
        font-size: 1.5rem;
    }
    
    .badge {
        font-size: 0.85rem !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy link functionality
    const copyBtn = document.querySelector('.copy-link-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const url = this.dataset.url;
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-success');
                    
                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-secondary');
                    }, 2000);
                    
                    if (window.MusicLocker && typeof window.MusicLocker.showToast === 'function') {
                        window.MusicLocker.showToast('Link copied to clipboard!', 'success');
                    }
                }).catch(err => {
                    console.error('Copy failed:', err);
                    if (window.MusicLocker && typeof window.MusicLocker.showToast === 'function') {
                        window.MusicLocker.showToast('Failed to copy link', 'danger');
                    }
                });
            }
        });
    }

    // Favorite button functionality
    const favoriteBtn = document.querySelector('.favorite-btn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', async function() {
            const entryId = this.dataset.entryId;
            const isFavorite = this.dataset.isFavorite === '1';

            try {
                const response = await fetch(`/api/v1/music/${entryId}/toggle-favorite`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    location.reload();
                }
            } catch (error) {
                console.error('Error toggling favorite:', error);
                if (window.MusicLocker && typeof window.MusicLocker.showToast === 'function') {
                    window.MusicLocker.showToast('Failed to update favorite status. Please try again.', 'danger');
                }
            }
        });
    }
});
</script>
@endpush
@endsection
